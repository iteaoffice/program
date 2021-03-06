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

use Contact\Entity\Contact;
use Contact\Service\ContactService;
use Doctrine\Common\Collections\ArrayCollection;
use General\Service\GeneralService;
use Program\Controller\Plugin\GetFilter;
use Program\Controller\Plugin\RenderNda;
use Program\Entity;
use Program\Form\UploadNda;
use Program\Service\CallService;
use Program\Service\ProgramService;
use Laminas\Http\Response;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\Mvc\Plugin\Identity\Identity;
use Laminas\Validator\File\FilesSize;
use Laminas\Validator\File\MimeType;
use Laminas\View\Model\ViewModel;
use ZfcTwig\View\TwigRenderer;

use function count;

/**
 * @method GetFilter getProgramFilter()
 * @method FlashMessenger flashMessenger()
 * @method Identity|Contact identity()
 * @method RenderNda renderNda()
 */
final class NdaController extends AbstractActionController
{
    private ProgramService $programService;
    private CallService $callService;
    private GeneralService $generalService;
    private ContactService $contactService;
    private TranslatorInterface $translator;
    private TwigRenderer $renderer;

    public function __construct(
        ProgramService $programService,
        CallService $callService,
        GeneralService $generalService,
        ContactService $contactService,
        TranslatorInterface $translator,
        TwigRenderer $renderer
    ) {
        $this->programService = $programService;
        $this->callService = $callService;
        $this->generalService = $generalService;
        $this->contactService = $contactService;
        $this->translator = $translator;
        $this->renderer = $renderer;
    }

    public function submitAction()
    {
        //We only want the active call, having the requirement that this call also requires an NDA
        $call = $this->callService->findLastActiveCall();
        $contact = $this->identity();

        //When the call requires no NDA, remove it form the form
        if (null !== $call && $call->getNdaRequirement() !== Entity\Call\Call::NDA_REQUIREMENT_PER_CALL) {
            $call = null;
        }

        if (null !== $callId = $this->params('callId')) {
            $call = $this->callService->findCallById((int)$callId);

            //Return a 404 when we cannot find the call provided
            if (null === $call) {
                return $this->notFoundAction();
            }
            $nda = $this->callService->findNdaByCallAndContact($call, $contact);
        } elseif (null !== $call) {
            $nda = $this->callService->findNdaByCallAndContact($call, $contact);
        } else {
            $nda = $this->callService->findNdaByContact($contact);
        }

        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );

        $form = new UploadNda();
        $form->setData($data);

        if ($this->getRequest()->isPost() && ! isset($data['approve']) && $form->isValid()) {
            if (isset($data['submit'])) {
                $fileData = $form->getData('file');
                $nda = $this->callService->uploadNda($fileData['file'], $contact, $call);

                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate(
                            'txt-nda-has-been-uploaded-successfully'
                        )
                    )
                );
            }

            return $this->redirect()->toRoute('community/program/nda/submit');
        }

        if ($this->getRequest()->isPost() && isset($data['approve'])) {
            if ($data['selfApprove'] === '0') {
                $form->getInputFilter()->get('selfApprove')->setErrorMessage('Error');
                $form->get('selfApprove')->setMessages(['Error']);
            }

            if ($data['selfApprove'] === '1') {
                $nda = $this->callService->submitNda($contact, $call);

                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('txt-nda-has-been-submitted-and-approved-successfully')
                    )
                );

                //Switch the trigger for the update
                $contact->setTriggerUpdate(true);
                $this->contactService->save($contact);

                return $this->redirect()->toRoute('community/program/nda/submit');
            }
        }

        $ndaContent = $this->renderer->render(
            'program/pdf/nda-call',
            [
                'contact'        => $contact,
                'call'           => $call,
                'contactService' => $this->contactService,
            ]
        );

        return new ViewModel(
            [
                'call'       => $call,
                'nda'        => $nda,
                'form'       => $form,
                'ndaContent' => $ndaContent
            ]
        );
    }

    public function replaceAction()
    {
        /** @var Entity\Nda $nda */
        $nda = $this->programService->find(Entity\Nda::class, (int)$this->params('id'));

        if (null === $nda || count($nda->getObject()) === 0) {
            return $this->notFoundAction();
        }
        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );
        $form = new UploadNda();
        $form->setData($data);
        if ($this->getRequest()->isPost()) {
            if (! isset($data['cancel']) && $form->isValid()) {
                $fileData = $this->params()->fromFiles();
                /*
                 * Remove the current entity
                 */
                foreach ($nda->getObject() as $object) {
                    $this->programService->delete($object);
                }
                //Create a article object element
                $ndaObject = new Entity\NdaObject();
                $ndaObject->setObject(file_get_contents($fileData['file']['tmp_name']));
                $fileSizeValidator = new FilesSize(PHP_INT_MAX);
                $fileSizeValidator->isValid($fileData['file']);
                $nda->setSize($fileSizeValidator->size);

                $fileTypeValidator = new MimeType();
                $fileTypeValidator->isValid($fileData['file']);
                $nda->setContentType(
                    $this->generalService->findContentTypeByContentTypeName($fileTypeValidator->type)
                );

                $ndaObject->setNda($nda);
                $this->programService->save($ndaObject);
                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate(
                            'txt-nda-has-been-replaced-successfully'
                        )
                    )
                );

                return $this->redirect()->toRoute('community/program/nda/view', ['id' => $nda->getId()]);
            }
            if (isset($data['cancel'])) {
                $this->flashMessenger()->setNamespace('info')->addMessage(
                    sprintf($this->translator->translate('txt-action-has-been-cancelled'))
                );

                return $this->redirect()->toRoute('community/program/nda/view', ['id' => $nda->getId()]);
            }
        }

        return new ViewModel(
            [
                'nda'  => $nda,
                'form' => $form,
            ]
        );
    }

    public function renderAction(): Response
    {
        //Create an empty NDA object
        $nda = new Entity\Nda();
        $nda->setContact($this->identity());

        /** @var Response $response */
        $response = $this->getResponse();

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

        $response->getHeaders()
            ->addHeaderLine('Content-Disposition', 'attachment; filename="' . $nda->parseFileName() . '.pdf"')
            ->addHeaderLine('Content-Type: application/pdf')
            ->addHeaderLine('Content-Length', strlen($renderNda->getPDFData()));
        $response->setContent($renderNda->getPDFData());

        return $response;
    }

    public function downloadAction(): Response
    {
        /** @var Entity\Nda $nda */
        $nda = $this->programService->find(Entity\Nda::class, (int)$this->params('id'));

        /** @var Response $response */
        $response = $this->getResponse();

        if (null === $nda || count($nda->getObject()) === 0) {
            return $response->setStatusCode(Response::STATUS_CODE_404);
        }
        /*
         * Due to the BLOB issue, we treat this as an array and we need to capture the first element
         */
        $object = $nda->getObject()->first()->getObject();

        $response->setContent(stream_get_contents($object));
        $response->getHeaders()
            ->addHeaderLine(
                'Content-Disposition',
                'attachment; filename="' . $nda->parseFileName() . '.' . $nda->getContentType()->getExtension() . '"'
            )
            ->addHeaderLine('Pragma: public')->addHeaderLine(
                'Content-Type: ' . $nda->getContentType()
                    ->getContentType()
            )->addHeaderLine('Content-Length: ' . $nda->getSize());

        return $response;
    }
}
