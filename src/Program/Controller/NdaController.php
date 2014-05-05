<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category   Program
 * @package    Controller
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2014 ITEA Office
 * @license    http://debranova.org/license.txt proprietary
 * @link       http://debranova.org
 */
namespace Program\Controller;

use Program\Entity\Nda;
use Program\Entity\NdaObject;
use Program\Form\UploadNda;
use Zend\Validator\File\FilesSize;
use Zend\View\Model\ViewModel;

/**
 * Create a link to an project
 *
 * @category   Program
 * @package    Controller
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @license    http://debranova.org/licence.txt proprietary
 * @link       http://debranova.org
 */
class NdaController extends ProgramAbstractController
{
    /**
     * @return ViewModel
     */
    public function viewAction()
    {
        $nda = $this->getProgramService()->findEntityById(
            'Nda',
            $this->getEvent()->getRouteMatch()->getParam('id')
        );

        if (is_null($nda) || sizeof($nda->getObject()) === 0) {
            return $this->notFoundAction();
        }

        return new ViewModel(array('nda' => $nda));
    }

    /**
     * @return ViewModel
     */
    public function uploadAction()
    {
        $call = $this->getCallService()->setCallId($this->getEvent()->getRouteMatch()->getParam('call-id'))->getCall();

        if ($this->getCallService()->isEmpty()) {
            return $this->notFoundAction();
        }

        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );

        $form = new UploadNda();
        $form->setData($data);

        if ($this->getRequest()->isPost() && $form->isValid()) {
            $fileData = $form->getData('file');
            $this->getCallService()->uploadNda(
                $fileData['file'],
                $this->zfcUserAuthentication()->getIdentity(),
                $call
            );
        }

        return new ViewModel(
            array(
                'call' => $call,
                'form' => $form
            )
        );
    }

    /**
     * Action to replace an mis-uploaded DoA
     *
     * @return ViewModel
     * @throws \Zend\Form\Exception\InvalidArgumentException
     * @throws \InvalidArgumentException
     * @throws \Zend\Mvc\Exception\DomainException
     * @throws \Zend\Form\Exception\DomainException
     */
    public function replaceAction()
    {

        $nda = $this->getProgramService()->findEntityById(
            'Nda',
            $this->getEvent()->getRouteMatch()->getParam('id')
        );

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

            if (!isset($data['cancel']) && $form->isValid()) {
                $fileData = $this->params()->fromFiles();

                /**
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
                    $this->getGeneralService()->findContentTypeByContentTypeName($fileData['file']['type'])
                );

                $ndaObject->setNda($nda);

                $this->getProgramService()->newEntity($ndaObject);

                $this->flashMessenger()->setNamespace('success')->addMessage(
                    sprintf(_("txt-nda-has-been-replaced-successfully"))
                );
            }

            $this->redirect()->toRoute(
                'program/nda/view',
                array('id' => $nda->getId())
            );
        }

        return new ViewModel(
            array(
                'nda'  => $nda,
                'form' => $form
            )
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

        /**
         * Add the call when a call-id is given
         */
        if (!is_null($this->getEvent()->getRouteMatch()->getParam('call-id'))) {
            $call = $this->getCallService()->setCallId(
                $this->getEvent()->getRouteMatch()->getParam('call-id')
            )->getCall();

            if ($this->getCallService()->isEmpty()) {
                return $this->notFoundAction();
            }

            $nda->setCall($call);
            $renderNda = $this->renderNda()->renderForCall($nda);
        } else {
            $renderNda = $this->renderNda()->render($nda);
        }

        $response = $this->getResponse();
        $response->getHeaders()
            ->addHeaderLine('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 36000))
            ->addHeaderLine("Cache-Control: max-age=36000, must-revalidate")
            ->addHeaderLine("Pragma: public")
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
        set_time_limit(0);

        $nda = $this->getProgramService()->findEntityById('Nda', $this->getEvent()->getRouteMatch()->getParam('id'));

        if (is_null($nda) || sizeof($nda->getObject()) === 0) {
            return $this->notFoundAction();
        }

        /**
         * Due to the BLOB issue, we treat this as an array and we need to capture the first element
         */
        $object   = $nda->getObject()->first()->getObject();
        $response = $this->getResponse();
        $response->setContent(stream_get_contents($object));

        $response->getHeaders()
            ->addHeaderLine('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 36000))
            ->addHeaderLine("Cache-Control: max-age=36000, must-revalidate")
            ->addHeaderLine(
                'Content-Disposition',
                'attachment; filename="' . $nda->parseFileName() . '.' .
                $nda->getContentType()->getExtension() . '"'
            )
            ->addHeaderLine("Pragma: public")
            ->addHeaderLine('Content-Type: ' . $nda->getContentType()->getContentType())
            ->addHeaderLine('Content-Length: ' . $nda->getSize());

        return $this->response;
    }
}
