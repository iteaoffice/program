<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Program\Controller;

use Admin\Service\AdminService;
use Contact\Entity\Contact;
use Contact\Service\ContactService;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use General\Service\EmailService;
use General\Service\GeneralService;
use Laminas\Http\Response;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\Mvc\Plugin\Identity\Identity;
use Laminas\Validator\File\FilesSize;
use Laminas\Validator\File\MimeType;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;
use Program\Controller\Plugin\GetFilter;
use Program\Controller\Plugin\RenderNda;
use Program\Entity\Call\Call;
use Program\Entity\Nda;
use Program\Entity\NdaObject;
use Program\Form\AdminUploadNda;
use Program\Form\NdaApproval;
use Program\Service\CallService;
use Program\Service\FormService;

use function array_merge_recursive;
use function sprintf;
use function strlen;

/**
 * @method GetFilter getProgramFilter()
 * @method FlashMessenger flashMessenger()
 * @method Identity|Contact identity()
 * @method RenderNda renderNda()
 */
final class NdaManagerController extends AbstractActionController
{
    private CallService $callService;
    private FormService $formService;
    private ContactService $contactService;
    private GeneralService $generalService;
    private AdminService $adminService;
    private EmailService $emailService;
    private TranslatorInterface $translator;
    private EntityManager $entityManager;

    public function __construct(
        CallService $callService,
        FormService $formService,
        ContactService $contactService,
        GeneralService $generalService,
        AdminService $adminService,
        EmailService $emailService,
        TranslatorInterface $translator,
        EntityManager $entityManager
    ) {
        $this->callService    = $callService;
        $this->formService    = $formService;
        $this->contactService = $contactService;
        $this->generalService = $generalService;
        $this->adminService   = $adminService;
        $this->emailService   = $emailService;
        $this->translator     = $translator;
        $this->entityManager  = $entityManager;
    }

    public function approvalAction(): ViewModel
    {
        $nda  = $this->callService->findNotApprovedNda();
        $form = new NdaApproval($nda);

        return new ViewModel(
            [
                'nda'  => $nda,
                'form' => $form,
            ]
        );
    }

    public function viewAction(): ViewModel
    {
        $nda = $this->callService->find(Nda::class, (int)$this->params('id'));
        if (null === $nda) {
            return $this->notFoundAction();
        }

        return new ViewModel(['nda' => $nda]);
    }

    public function renderAction(): Response
    {
        //Create an empty NDA object
        $nda     = new Nda();
        $contact = $this->contactService->findContactById((int)$this->params('contactId'));

        /** @var Response $response */
        $response = $this->getResponse();

        $nda->setContact($contact);
        /*
         * Add the call when a id is given
         */
        if (null !== $this->params('callId')) {
            $call = $this->callService->findCallById((int)$this->params('callId'));
            if (null === $call) {
                return $response->setStatusCode(Response::STATUS_CODE_404);
            }
            $arrayCollection = new ArrayCollection([$call]);
            $nda->setCall($arrayCollection);
            $renderNda = $this->renderNda()->renderForCall($nda);
        } else {
            $renderNda = $this->renderNda()->render($nda);
        }

        $response->getHeaders()->addHeaderLine('Pragma: public')
            ->addHeaderLine('Content-Disposition', 'attachment; filename="' . $nda->parseFileName() . '.pdf"')
            ->addHeaderLine('Content-Type: application/pdf')
            ->addHeaderLine('Content-Length', strlen($renderNda->getPDFData()));
        $response->setContent($renderNda->getPDFData());

        return $response;
    }

    public function editAction()
    {
        /** @var Nda $nda */
        $nda = $this->callService->find(Nda::class, (int)$this->params('id'));

        if (null === $nda) {
            return $this->notFoundAction();
        }

        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );

        $form = $this->formService->prepare($nda, $data);

        $form->get($nda->get('underscore_entity_name'))->get('contact')->setValueOptions(
            [
                $nda->getContact()->getId() => $nda->getContact()->getFormName(),
            ]
        );
        $form->get($nda->get('underscore_entity_name'))->get('programCall')->setValue($nda->parseCall());

        //Get contacts in an organisation
        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/nda/view', ['id' => $nda->getId()]);
            }

            if (isset($data['delete'])) {
                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('txt-nda-for-contact-%s-has-been-removed'),
                        $nda->getContact()->getDisplayName()
                    )
                );

                $this->callService->delete($nda);

                return $this->redirect()->toRoute('zfcadmin/nda/approval');
            }

            if ($form->isValid()) {
                /**
                 * @var Nda $nda
                 */
                $nda = $form->getData();

                $fileData = $this->params()->fromFiles();

                if ($fileData['program_entity_nda']['file']['error'] === 0) {
                    /*
                     * Replace the content of the object
                     */
                    if (! $nda->getObject()->isEmpty()) {
                        $nda->getObject()->first()->setObject(
                            file_get_contents($fileData['program_entity_nda']['file']['tmp_name'])
                        );
                    } else {
                        $ndaObject = new NdaObject();
                        $ndaObject->setObject(file_get_contents($fileData['program_entity_nda']['file']['tmp_name']));
                        $ndaObject->setNda($nda);
                        $this->callService->save($ndaObject);
                    }

                    //Create a article object element
                    $fileSizeValidator = new FilesSize(PHP_INT_MAX);
                    $fileSizeValidator->isValid($fileData['program_entity_nda']['file']);
                    $nda->setSize($fileSizeValidator->size);

                    $fileTypeValidator = new MimeType();
                    $fileTypeValidator->isValid($fileData['program_entity_nda']['file']);
                    $nda->setContentType(
                        $this->generalService->findContentTypeByContentTypeName($fileTypeValidator->type)
                    );
                }

                /*
                 * The programme call needs to have a dedicated treatment
                 */
                if (! empty($data['program_entity_nda']['programCall'])) {
                    $nda->setCall([$this->callService->findCallById((int)$data['program_entity_nda']['programCall'])]);
                } else {
                    $nda->setCall([]);
                }

                $this->callService->save($nda);

                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('txt-nda-for-contact-%s-has-been-updated'),
                        $nda->getContact()->getDisplayName()
                    )
                );

                return $this->redirect()->toRoute('zfcadmin/nda/view', ['id' => $nda->getId()]);
            }
        }

        return new ViewModel(
            [
                'nda'  => $nda,
                'form' => $form,
            ]
        );
    }

    public function uploadAction()
    {
        $contact = $this->contactService->findContactById((int)$this->params('contactId'));
        $calls   = $this->callService->findAll(Call::class);

        if (null === $contact) {
            return $this->notFoundAction();
        }

        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );

        $form = new AdminUploadNda($this->entityManager);
        $form->setData($data);
        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/contact/view/general', ['id' => $contact->getId()]);
            }

            if ($form->isValid()) {
                $fileData = $form->getData('file');

                $call = $this->callService->findCallById((int)$data['call']);
                $nda  = $this->callService->uploadNda($fileData['file'], $contact, $call);

                //if the date-signed is set, arrange that
                $dateSigned = DateTime::createFromFormat('Y-m-d', $data['dateSigned']);

                if ($dateSigned) {
                    $nda->setDateSigned($dateSigned);
                }

                if ($data['approve'] === '1') {
                    $nda->setDateApproved(new DateTime());
                    $nda->setApprover($this->contactService->findContactById(1));

                    $this->callService->save($nda);
                    $this->adminService->flushPermitsByContact($contact);
                }

                $this->flashMessenger()->addSuccessMessage(
                    sprintf($this->translator->translate('txt-nda-has-been-uploaded-successfully'))
                );

                return $this->redirect()->toRoute('zfcadmin/contact/view/legal', ['id' => $contact->getId()]);
            }
        }

        return new ViewModel(
            [
                'form'    => $form,
                'contact' => $contact,
                'calls'   => $calls
            ]
        );
    }

    public function approveAction(): JsonModel
    {
        $dateSigned = $this->params()->fromPost('dateSigned');
        $sendEmail  = $this->params()->fromPost('sendEmail', 0);

        if (empty($dateSigned)) {
            return new JsonModel(
                [
                    'result' => 'error',
                    'error'  => $this->translator->translate('txt-date-signed-is-empty'),
                ]
            );
        }

        if (! DateTime::createFromFormat('Y-m-d', $dateSigned)) {
            return new JsonModel(
                [
                    'result' => 'error',
                    'error'  => $this->translator->translate('txt-incorrect-date-format-should-be-yyyy-mm-dd'),
                ]
            );
        }

        /** @var Nda $nda */
        $nda = $this->callService->find(Nda::class, (int)$this->params()->fromPost('nda'));
        $nda->setDateSigned(DateTime::createFromFormat('Y-m-d', $dateSigned));
        $nda->setDateApproved(new DateTime());
        $nda->setApprover($this->identity());
        $this->callService->save($nda);

        //Flush the rights of the NDA guy
        $this->adminService->flushPermitsByContact($nda->getContact());

        /**
         * Send the email tot he user
         */
        if ($sendEmail === 'true') {
            $email = $this->emailService->createNewWebInfoEmailBuilder('/program/nda/approved');

            $email->addContactTo($nda->getContact());
            $email->setTemplateVariable('has_idea_tool', false);
            $email->setTemplateVariable('has_call', false);

            if ($nda->hasCall()) {
                /** @var Call $call */
                $call = $nda->getCall()->first();
                $email->setTemplateVariable('has_call', true);
                $email->setTemplateVariable('call', $call);

                if ($call->hasIdeaTool()) {
                    $email->setTemplateVariable('has_idea_tool', true);
                    $email->addDeeplink('community/idea/tool/redirect', 'project_idea_tool_link', $nda->getContact(), null, $call->getIdeaTool()->getId());
                }
            }

            $email->addDeeplink('community/roadmap/index', 'living_roadmap_link', $nda->getContact());
            $this->emailService->sendBuilder($email);
        }

        return new JsonModel(
            [
                'result' => 'success',
            ]
        );
    }
}
