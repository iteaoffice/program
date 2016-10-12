<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category   Program
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2015 ITEA Office
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
use Zend\View\Model\ViewModel;

/**
 * Create a link to an project.
 *
 * @category   Program
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @license    https://itea3.org/licence.txt proprietary
 *
 * @link       https://itea3.org
 */
class NdaController extends ProgramAbstractController
{
    /**
     * @return ViewModel
     */
    public function viewAction()
    {
        $nda = $this->getProgramService()->findEntityById(Nda::class, $this->params('id'));
        if (is_null($nda) || sizeof($nda->getObject()) === 0) {
            return $this->notFoundAction();
        }

        return new ViewModel(['nda' => $nda]);
    }

    /**
     * @return ViewModel
     */
    public function uploadAction()
    {
        //We only want the active call, having the requirement that this call also requires an NDA
        $call = $this->getCallService()->findLastActiveCall();

        //When the call requires no NDA, remove it form the form
        if (! is_null($call) && $call->getDoaRequirement() !== Call::DOA_REQUIREMENT_PER_PROGRAM) {
            $call = null;
        }


        if (! is_null($callId = $this->params('callId'))) {
            $call = $this->getCallService()->findCallById($callId);
            if (is_null($call)) {
                return $this->notFoundAction();
            }
            $nda = $this->getCallService()
                ->findNdaByCallAndContact($call, $this->zfcUserAuthentication()->getIdentity());
        } elseif (! is_null($call)) {
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
     * Action to replace an mis-uploaded DoA.
     *
     * @return ViewModel
     *
     * @throws \Zend\Form\Exception\InvalidArgumentException
     * @throws \InvalidArgumentException
     * @throws \Zend\Mvc\Exception\DomainException
     * @throws \Zend\Form\Exception\DomainException
     */
    public function replaceAction()
    {
        $nda = $this->getProgramService()->findEntityById(Nda::class, $this->params('id'));
        if (is_null($nda) || sizeof($nda->getObject()) === 0) {
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
                    $this->getProgramService()->removeEntity($object);
                }
                //Create a article object element
                $ndaObject = new NdaObject();
                $ndaObject->setObject(file_get_contents($fileData['file']['tmp_name']));
                $fileSizeValidator = new FilesSize(PHP_INT_MAX);
                $fileSizeValidator->isValid($fileData['file']);
                $nda->setSize($fileSizeValidator->size);
                $nda->setContentType(
                    $this->getGeneralService()
                        ->findContentTypeByContentTypeName($fileData['file']['type'])
                );
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
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function renderAction()
    {
        //Create an empty NDA object
        $nda = new Nda();
        $nda->setContact($this->zfcUserAuthentication()->getIdentity());
        /*
         * Add the call when a id is given
         */
        if (! is_null($this->params('callId'))) {
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
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function downloadAction()
    {
        $nda = $this->getProgramService()->findEntityById(Nda::class, $this->params('id'));
        if (is_null($nda) || sizeof($nda->getObject()) === 0) {
            return $this->notFoundAction();
        }
        /*
         * Due to the BLOB issue, we treat this as an array and we need to capture the first element
         */
        $object   = $nda->getObject()->first()->getObject();
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
