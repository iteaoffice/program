<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Affiliation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        https://itea3.org
 */

declare(strict_types=1);

namespace Program\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Program\Entity\Call\Call;
use Program\Entity\Nda;
use Program\Entity\NdaObject;
use Program\Form\AdminUploadNda;
use Program\Form\NdaApproval;
use Zend\Validator\File\FilesSize;
use Zend\Validator\File\MimeType;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Class NdaManagerController
 *
 * @package Program\Controller
 */
class NdaManagerController extends ProgramAbstractController
{
    /**
     * @return ViewModel
     */
    public function approvalAction(): ViewModel
    {
        $nda = $this->getCallService()->findNotApprovedNda();
        $form = new NdaApproval($nda);

        return new ViewModel(
            [
                'nda'  => $nda,
                'form' => $form,
            ]
        );
    }

    /**
     * @return ViewModel
     */
    public function viewAction(): ViewModel
    {
        $nda = $this->callService->findEntityById(Nda::class, $this->params('id'));
        if (null === $nda) {
            return $this->notFoundAction();
        }

        return new ViewModel(['nda' => $nda]);
    }

    /**
     * @return \Zend\Stdlib\ResponseInterface|ViewModel
     */
    public function renderAction()
    {
        //Create an empty NDA object
        $nda = new Nda();

        $contactId = $this->params('contactId');
        $contact = $this->getContactService()->findContactById($contactId);

        $nda->setContact($contact);
        /*
         * Add the call when a id is given
         */
        if (null !== $this->params('callId')) {
            $call = $this->getCallService()->findCallById($this->params('callId'));
            if (null === $call) {
                return $this->notFoundAction();
            }
            $arrayCollection = new ArrayCollection([$call]);
            $nda->setCall($arrayCollection);
            $renderNda = $this->renderNda()->renderForCall($nda);
        } else {
            $renderNda = $this->renderNda()->render($nda);
        }
        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 36000))
            ->addHeaderLine("Cache-Control: max-age=36000, must-revalidate")->addHeaderLine("Pragma: public")
            ->addHeaderLine('Content-Disposition', 'attachment; filename="' . $nda->parseFileName() . '.pdf"')
            ->addHeaderLine('Content-Type: application/pdf')
            ->addHeaderLine('Content-Length', strlen($renderNda->getPDFData()));
        $response->setContent($renderNda->getPDFData());

        return $response;
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function editAction()
    {
        /** @var Nda $nda */
        $nda = $this->getCallService()->findEntityById(Nda::class, $this->params('id'));

        if (null === $nda) {
            return $this->notFoundAction();
        }

        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );

        $form = $this->getFormService()->prepare(Nda::class, $nda, $data);

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
                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(
                        sprintf(
                            $this->translate("txt-nda-for-contact-%s-has-been-removed"),
                            $nda->getContact()->getDisplayName()
                        )
                    );

                $this->getCallService()->removeEntity($nda);

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
                    if (!$nda->getObject()->isEmpty()) {
                        $nda->getObject()->first()->setObject(
                            file_get_contents($fileData['program_entity_nda']['file']['tmp_name'])
                        );
                    } else {
                        $ndaObject = new NdaObject();
                        $ndaObject->setObject(file_get_contents($fileData['program_entity_nda']['file']['tmp_name']));
                        $ndaObject->setNda($nda);
                        $this->getCallService()->newEntity($ndaObject);
                    }

                    //Create a article object element
                    $fileSizeValidator = new FilesSize(PHP_INT_MAX);
                    $fileSizeValidator->isValid($fileData['program_entity_nda']['file']);
                    $nda->setSize($fileSizeValidator->size);

                    $fileTypeValidator = new MimeType();
                    $fileTypeValidator->isValid($fileData['program_entity_nda']['file']);
                    $nda->setContentType(
                        $this->getGeneralService()->findContentTypeByContentTypeName($fileTypeValidator->type)
                    );
                }

                /*
                 * The programme call needs to have a dedicated treatment
                 */
                if (!empty($data['program_entity_nda']['programCall'])) {
                    $nda->setCall([$this->getCallService()->findCallById($data['program_entity_nda']['programCall'])]);
                } else {
                    $nda->setCall([]);
                }

                $this->getCallService()->updateEntity($nda);

                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(
                        sprintf(
                            _("txt-nda-for-contact-%s-has-been-updated"),
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

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function uploadAction()
    {
        $contact = $this->getContactService()->findContactById($this->params('contactId'));
        $calls = $this->getProgramService()->findAll(Call::class);

        if (null === $contact) {
            return $this->notFoundAction();
        }

        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );

        $form = new AdminUploadNda($this->getEntityManager());
        $form->setData($data);
        if ($this->getRequest()->isPost() && $form->isValid()) {
            if (isset($data['submit'])) {
                $fileData = $form->getData('file');

                $call = $this->getCallService()->findCallById($data['call']);
                $nda = $this->getCallService()->uploadNda($fileData['file'], $contact, $call);

                //if the date-signed is set, arrange that
                $dateSigned = \DateTime::createFromFormat('Y-m-d', $data['dateSigned']);

                if ($dateSigned) {
                    $nda->setDateSigned($dateSigned);
                }

                if ($data['approve'] === '1') {
                    $nda->setDateApproved(new \DateTime());
                    $nda->setApprover($this->zfcUserAuthentication()->getIdentity());

                    $this->getAdminService()->flushPermitsByContact($contact);
                }

                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(sprintf($this->translate("txt-nda-has-been-uploaded-successfully")));
            }


            return $this->redirect()->toRoute('zfcadmin/contact-admin/view', ['id' => $contact->getId()]);
        }

        return new ViewModel(
            [
                'form'    => $form,
                'contact' => $contact,
                'calls'   => $calls
            ]
        );
    }

    /**
     * @return JsonModel
     * @throws \Exception
     */
    public function approveAction(): JsonModel
    {
        $nda = $this->params()->fromPost('nda');
        $dateSigned = $this->params()->fromPost('dateSigned');
        $sendEmail = $this->params()->fromPost('sendEmail', 0);

        if (empty($dateSigned)) {
            return new JsonModel(
                [
                    'result' => 'error',
                    'error'  => $this->translate("txt-date-signed-is-empty"),
                ]
            );
        }

        if (!\DateTime::createFromFormat('Y-m-d', $dateSigned)) {
            return new JsonModel(
                [
                    'result' => 'error',
                    'error'  => $this->translate("txt-incorrect-date-format-should-be-yyyy-mm-dd"),
                ]
            );
        }

        /** @var Nda $nda */
        $nda = $this->getCallService()->findEntityById(Nda::class, $nda);
        $nda->setDateSigned(\DateTime::createFromFormat('Y-m-d', $dateSigned));
        $nda->setDateApproved(new \DateTime());
        $nda->setApprover($this->zfcUserAuthentication()->getIdentity());
        $this->getCallService()->updateEntity($nda);

        //Flush the rights of the NDA guy
        $this->getAdminService()->flushPermitsByContact($nda->getContact());

        /**
         * Send the email tot he user
         */
        if ($sendEmail === 'true') {
            $email = $this->getEmailService()->create();
            $this->getEmailService()->setTemplate("/program/nda/approved");
            $email->setFilename($nda->parseFileName());
            $email->setDateSigned($nda->getDateSigned()->format('d-m-Y'));
            if (!$nda->getCall()->isEmpty()) {
                $email->setCall((string)$nda->getCall());
            }

            //$email->addTo($nda->getContact());
            $email->addTo('info@jield.nl');
            $this->getEmailService()->send();
        }

        //Update the
        return new JsonModel(
            [
                'result' => 'success',
            ]
        );
    }
}
