<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Program
 * @package     Controller
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Program\Controller;

use Program\Form\UploadNda;
use Zend\View\Model\ViewModel;
use Program\Entity\Nda;

/**
 * @category    Program
 * @package     Controller
 */
class NdaController extends ProgramAbstractController
{

    /**
     * @return ViewModel
     */
    public function viewCallAction()
    {
        $call = $this->getProgramService()->findEntityById(
            'Call\Call',
            $this->getEvent()->getRouteMatch()->getParam('call')
        );

        if (is_null($call)) {
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
            $this->getProgramService()->uploadNda(
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
     * @return ViewModel
     */
    public function viewAction()
    {
        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );

        $form = new UploadNda();
        $form->setData($data);

        if ($this->getRequest()->isPost() && $form->isValid()) {
            $fileData = $form->getData('file');
            $this->getProgramService()->uploadNda(
                $fileData['file'],
                $this->zfcUserAuthentication()->getIdentity()
            );
        }

        return new ViewModel(
            array(
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

        $renderNda = $this->renderNda()->render($nda);

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
    public function renderCallAction()
    {
        $call = $this->getProgramService()->findEntityById(
            'Call\Call',
            $this->getEvent()->getRouteMatch()->getParam('call')
        );

        if (is_null($call)) {
            return $this->notFoundAction();
        }

        //Create an empty NDA object
        $nda = new Nda();
        $nda->setContact($this->zfcUserAuthentication()->getIdentity());
        $nda->setCall($call);

        $renderNda = $this->renderNda()->renderCall($nda);

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
