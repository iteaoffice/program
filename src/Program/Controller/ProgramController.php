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

use Program\Builder\Nda;
use Program\Form\UploadNda;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

use Project\Service\ProjectService;

use Program\Service\ProgramService;
use Program\Service\FormServiceAwareInterface;
use Program\Service\FormService;

/**
 * @category    Program
 * @package     Controller
 */
class ProgramController extends AbstractActionController implements
    FormServiceAwareInterface,
    ServiceLocatorAwareInterface
{
    /**
     * @var ProgramService
     */
    protected $programService;
    /**
     * @var ProjectService
     */
    protected $projectService;
    /**
     * @var FormService
     */
    protected $formService;

    /**
     * Message container
     * @return array|void
     */
    public function viewNdaCallAction()
    {
        $call = $this->getProgramService()->findEntityById(
            'call',
            $this->getEvent()->getRouteMatch()->getParam('call')
        );

        if (is_null($call)) {
            return $this->notFoundAction();
        }

        $form = new UploadNda();

        if ($this->getRequest()->isPost()) {
            $data = array_merge_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );
            $form->setData($data);
            if ($form->isValid()) {
                $fileData = $form->getData('file');
                $this->getProgramService()->uploadNda(
                    $fileData['file'],
                    $this->zfcUserAuthentication()->getIdentity(),
                    $call
                );
            }
        }

        return new ViewModel(
            array(
                'call' => $call,
                'form' => $form
            )
        );
    }

    /**
     * Message container
     * @return array|void
     */
    public function renderNdaCallAction()
    {
        $call = $this->getProgramService()->findEntityById(
            'call',
            $this->getEvent()->getRouteMatch()->getParam('call')
        );

        if (is_null($call)) {
            return $this->notFoundAction();
        }

        $builderNda = new Nda($this->zfcUserAuthentication()->getIdentity(), $call);

        $response = $this->getResponse();
        $response->getHeaders()
            ->addHeaderLine('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 36000))
            ->addHeaderLine("Cache-Control: max-age=36000, must-revalidate")
            ->addHeaderLine("Pragma: public")
            ->addHeaderLine('Content-Disposition', "attachment; filename=\"call-xxx-2\"")
            ->addHeaderLine('Content-Type: application/pdf');

        $response->setContent($builderNda->getPdf());

        return $response;
    }

    /**
     * @param \Zend\Mvc\Controller\string $layout
     *
     * @return void|\Zend\Mvc\Controller\Plugin\Layout|\Zend\View\Model\ModelInterface
     */
    public function layout($layout)
    {
        if (false === $layout) {
            $this->getEvent()->getViewModel()->setTemplate('layout/nolayout');
        } else {
            $this->getEvent()->getViewModel()->setTemplate('layout/' . $layout);
        }
    }

    /**
     * @return FormService
     */
    public function getFormService()
    {
        return $this->formService;
    }

    /**
     * @param $formService
     *
     * @return ProgramController
     */
    public function setFormService($formService)
    {
        $this->formService = $formService;

        return $this;
    }

    /**
     * Gateway to the Program Service
     *
     * @return ProgramService
     */
    public function getProgramService()
    {
        return $this->getServiceLocator()->get('program_program_service');
    }

    /**
     * @return \Project\Service\ProjectService
     */
    public function getProjectService()
    {
        return $this->getServiceLocator()->get('project_project_service');
    }

    /**
     * @param $programService
     *
     * @return ProgramController
     */
    public function setProgramService($programService)
    {
        $this->programService = $programService;

        return $this;
    }
}
