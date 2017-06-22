<?php
/**
 * ITEA Office all rights reserved
 *
 * @category   Program
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license    https://itea3.org/license.txt proprietary
 *
 * @link       https://itea3.org
 */

namespace Program\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Program\Entity\Call\Call;
use Program\Entity\Nda;
use Program\Entity\NdaObject;
use Program\Form\UploadNda;
use Zend\Validator\File\FilesSize;
use Zend\Validator\File\MimeType;
use Zend\View\Model\ViewModel;

/**
 * Class NdaController
 * @package Program\Controller
 */
class NdaController extends ProgramAbstractController
{
    /**
     * @return array|ViewModel
     */
    public function viewAction()
    {
        $nda = $this->getProgramService()->findEntityById(Nda::class, $this->params('id'));
        if (is_null($nda) || count($nda->getObject()) === 0) {
            return $this->notFoundAction();
        }

        return new ViewModel(['nda' => $nda]);
    }

    /**
     * @return array|\Zend\Http\Response|ViewModel
     */
    public function uploadAction()
    {
        //We only want the active call, having the requirement that this call also requires an NDA
        $call = $this->getCallService()->findLastActiveCall();

        //When the call requires no NDA, remove it form the form
        if (!is_null($call) && $call->getNdaRequirement() !== Call::NDA_REQUIREMENT_PER_CALL) {
            $call = null;
        }

        if (!is_null($callId = $this->params('callId'))) {
            $call = $this->getCallService()->findCallById($callId);
            if (is_null($call)) {
                return $this->notFoundAction();
            }
            $nda = $this->getCallService()
                ->findNdaByCallAndContact($call, $this->zfcUserAuthentication()->getIdentity());
        } elseif (!is_null($call)) {
            $nda = $this->getCallService()
                ->findNdaByCallAndContact($call, $this->zfcUserAuthentication()->getIdentity());
        } else {
            $nda = $this->getCallService()->findNdaByContact($this->zfcUserAuthentication()->getIdentity());
        }
        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );
        $form = new UploadNda();
        $form->setData($data);
        if ($this->getRequest()->isPost() && $form->isValid()) {
            $fileData = $form->getData('file');
            $this->getCallService()->uploadNda($fileData['file'], $this->zfcUserAuthentication()->getIdentity(), $call);

            $this->flashMessenger()->setNamespace('success')
                ->addMessage(sprintf($this->translate("txt-nda-has-been-uploaded-successfully")));

            return $this->redirect()->toRoute('community');
        }

        return new ViewModel(
            [
                'call' => $call,
                'nda'  => $nda,
                'form' => $form,
            ]
        );
    }

    /**
     * @return array|\Zend\Http\Response|ViewModel
     */
    public function submitAction()
    {
        //We only want the active call, having the requirement that this call also requires an NDA
        $call = $this->getCallService()->findLastActiveCall();
        $contact = $this->zfcUserAuthentication()->getIdentity();

        //When the call requires no NDA, remove it form the form
        if (!is_null($call) && $call->getNdaRequirement() !== Call::NDA_REQUIREMENT_PER_CALL) {
            $call = null;
        }

        if (!is_null($callId = $this->params('callId'))) {
            $call = $this->getCallService()->findCallById($callId);

            //Return a 404 when we cannot find the call provided
            if (null === $call) {
                return $this->notFoundAction();
            }
            $nda = $this->getCallService()->findNdaByCallAndContact($call, $contact);
        } elseif (!is_null($call)) {
            $nda = $this->getCallService()->findNdaByCallAndContact($call, $contact);
        } else {
            $nda = $this->getCallService()->findNdaByContact($contact);
        }

        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );

        $form = new UploadNda();
        $form->setData($data);

        if ($this->getRequest()->isPost() && !isset($data['approve']) && $form->isValid()) {
            if (isset($data['submit'])) {
                $fileData = $form->getData('file');
                $this->getCallService()->uploadNda($fileData['file'], $contact, $call);

                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(sprintf($this->translate("txt-nda-has-been-uploaded-successfully")));
            }


            return $this->redirect()->toRoute('community');
        }

        if ($this->getRequest()->isPost()) {
            if (isset($data['approve'])) {
                if ($data['selfApprove'] === '0') {
                    $form->getInputFilter()->get('selfApprove')->setErrorMessage('Error');
                    $form->get('selfApprove')->setMessages(['Error']);
                }

                if ($data['selfApprove'] === '1') {
                    $this->getCallService()->submitNda($contact, $call);

                    $this->flashMessenger()->setNamespace('success')
                        ->addMessage(sprintf($this->translate("txt-nda-has-been-submitted-and-approved-successfully")));

                    return $this->redirect()->toRoute('community');
                }
            }
        }

        //We use the same code as the Helper to render the content of the NDA
        $twigRenderer = $this->getProgramService()->getServiceLocator()->get('ZfcTwigRenderer');
        $ndaContent = $twigRenderer->render(
            'program/pdf/nda-call',
            [
                'contact'        => $contact,
                'call'           => $call,
                'contactService' => $this->getContactService(),
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

    /**
     * @return array|\Zend\Http\Response|ViewModel
     */
    public function replaceAction()
    {
        /** @var Nda $nda */
        $nda = $this->getProgramService()->findEntityById(Nda::class, $this->params('id'));
        if (is_null($nda) || count($nda->getObject()) === 0) {
            return $this->notFoundAction();
        }
        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );
        $form = new UploadNda();
        $form->setData($data);
        if ($this->getRequest()->isPost()) {
            if (!isset($data['cancel']) && $form->isValid()) {
                $fileData = $this->params()->fromFiles();
                /*
                 * Remove the current entity
                 */
                foreach ($nda->getObject() as $object) {
                    $this->getProgramService()->removeEntity($object);
                }
                //Create a article object element
                $ndaObject = new NdaObject();
                $ndaObject->setObject(file_get_contents($fileData['file']['tmp_name']));
                $fileSizeValidator = new FilesSize(PHP_INT_MAX);
                $fileSizeValidator->isValid($fileData['file']);
                $nda->setSize($fileSizeValidator->size);

                $fileTypeValidator = new MimeType();
                $fileTypeValidator->isValid($fileData['file']);
                $nda->setContentType($this->getGeneralService()->findContentTypeByContentTypeName($fileTypeValidator->type));

                $ndaObject->setNda($nda);
                $this->getProgramService()->newEntity($ndaObject);
                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(sprintf($this->translate("txt-nda-has-been-replaced-successfully")));

                return $this->redirect()->toRoute('community/program/nda/view', ['id' => $nda->getId()]);
            }
            if (isset($data['cancel'])) {
                $this->flashMessenger()->setNamespace('info')->addMessage(sprintf(_("txt-action-has-been-cancelled")));

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

    /**
     * @return \Zend\Stdlib\ResponseInterface|ViewModel
     */
    public function renderAction()
    {
        //Create an empty NDA object
        $nda = new Nda();
        $nda->setContact($this->zfcUserAuthentication()->getIdentity());
        /*
         * Add the call when a id is given
         */
        if (!is_null($this->params('callId'))) {
            $call = $this->getCallService()->findCallById($this->params('callId'));
            if (is_null($call)) {
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
     * @return \Zend\Stdlib\ResponseInterface|ViewModel
     */
    public function downloadAction()
    {
        $nda = $this->getProgramService()->findEntityById(Nda::class, $this->params('id'));
        if (is_null($nda) || count($nda->getObject()) === 0) {
            return $this->notFoundAction();
        }
        /*
         * Due to the BLOB issue, we treat this as an array and we need to capture the first element
         */
        $object = $nda->getObject()->first()->getObject();
        $response = $this->getResponse();
        $response->setContent(stream_get_contents($object));
        $response->getHeaders()->addHeaderLine('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 36000))
            ->addHeaderLine("Cache-Control: max-age=36000, must-revalidate")->addHeaderLine(
                'Content-Disposition',
                'attachment; filename="' . $nda->parseFileName() . '.' . $nda->getContentType()->getExtension() . '"'
            )
            ->addHeaderLine("Pragma: public")->addHeaderLine(
                'Content-Type: ' . $nda->getContentType()
                    ->getContentType()
            )->addHeaderLine('Content-Length: ' . $nda->getSize());

        return $this->response;
    }
}
