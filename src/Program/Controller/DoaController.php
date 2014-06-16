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

use Program\Entity;
use Program\Entity\Doa;
use Program\Form\UploadDoa;
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
class DoaController extends ProgramAbstractController
{
    /**
     * @return ViewModel
     */
    public function viewAction()
    {
        $doa = $this->getProgramService()->findEntityById(
            'Doa',
            $this->getEvent()->getRouteMatch()->getParam('id')
        );

        if (is_null($doa) || sizeof($doa->getObject()) === 0) {
            return $this->notFoundAction();
        }

        return new ViewModel(array('doa' => $doa));
    }

    /**
     * Upload a DOA for a program (based on the affiliation, to be sure that the organisation is
     * active in at least a project in the database)
     *
     * @return ViewModel
     */
    public function uploadAction()
    {
        $organisationService = $this->getOrganisationService()->setOrganisationId(
            $this->getEvent()->getRouteMatch()->getParam('organisation-id')
        );
        $program             = $this->getProgramService()->findEntityById(
            'Program',
            $this->getEvent()->getRouteMatch()->getParam('program-id')
        );

        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );

        $form = new UploadDoa();
        $form->setData($data);

        if ($this->getRequest()->isPost() && $form->isValid()) {
            if (!isset($data['cancel'])) {
                $fileData = $this->params()->fromFiles();

                //Create a article object element
                $doaObject = new Entity\DoaObject();
                $doaObject->setObject(file_get_contents($fileData['file']['tmp_name']));

                $fileSizeValidator = new FilesSize(PHP_INT_MAX);
                $fileSizeValidator->isValid($fileData['file']);

                $doa = new Entity\Doa();
                $doa->setSize($fileSizeValidator->size);
                $doa->setContentType(
                    $this->getGeneralService()->findContentTypeByContentTypeName($fileData['file']['type'])
                );

                $doa->setContact($this->zfcUserAuthentication()->getIdentity());
                $doa->setOrganisation($organisationService->getOrganisation());
                $doa->setProgram($program);

                $doaObject->setDoa($doa);

                $this->getProgramService()->newEntity($doaObject);

                $this->flashMessenger()->setNamespace('success')->addMessage(
                    sprintf(
                        _("txt-doa-for-organisation-%s-in-program-%s-has-been-uploaded"),
                        $organisationService->getOrganisation(),
                        $program
                    )
                );
                $this->redirect()->toRoute(
                    'community/program/doa/view',
                    array('id' => $doaObject->getId())
                );
            }
        }

        return new ViewModel(
            array(
                'organisationService' => $organisationService,
                'program'             => $program,
                'form'                => $form
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

        $doa = $this->getProgramService()->findEntityById(
            'Doa',
            $this->getEvent()->getRouteMatch()->getParam('id')
        );

        if (is_null($doa) || sizeof($doa->getObject()) === 0) {
            return $this->notFoundAction();
        }

        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );

        $form = new UploadDoa();
        $form->setData($data);

        if ($this->getRequest()->isPost()) {
            if (!isset($data['cancel']) && $form->isValid()) {
                $fileData = $this->params()->fromFiles();

                /**
                 * Remove the current entity
                 */
                foreach ($doa->getObject() as $object) {
                    $this->getProgramService()->removeEntity($object);
                }

                //Create a article object element
                $programDoaObject = new Entity\DoaObject();
                $programDoaObject->setObject(file_get_contents($fileData['file']['tmp_name']));

                $fileSizeValidator = new FilesSize(PHP_INT_MAX);
                $fileSizeValidator->isValid($fileData['file']);

                $doa->setSize($fileSizeValidator->size);
                $doa->setContact($this->zfcUserAuthentication()->getIdentity());
                $doa->setContentType(
                    $this->getGeneralService()->findContentTypeByContentTypeName($fileData['file']['type'])
                );

                $programDoaObject->setDoa($doa);

                $this->getProgramService()->newEntity($programDoaObject);

                $this->flashMessenger()->setNamespace('success')->addMessage(
                    sprintf(
                        _("txt-doa-for-organisation-%s-in-program-%s-has-been-uploaded"),
                        $doa->getOrganisation(),
                        $doa->getProgram()
                    )
                );
            }

            $this->redirect()->toRoute(
                'program/doa/view',
                array('id' => $doa->getId())
            );
        }

        return new ViewModel(
            array(
                'doa'  => $doa,
                'form' => $form
            )
        );
    }

    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function renderAction()
    {
        $organisationService = $this->getOrganisationService()->setOrganisationId(
            $this->getEvent()->getRouteMatch()->getParam('organisation-id')
        );
        $program             = $this->getProgramService()->findEntityById(
            'Program',
            $this->getEvent()->getRouteMatch()->getParam('program-id')
        );

        //Create an empty Doa object
        $programDoa = new Doa();
        $programDoa->setContact($this->zfcUserAuthentication()->getIdentity());
        $programDoa->setOrganisation($organisationService->getOrganisation());
        $programDoa->setProgram($program);

        $renderProjectDoa = $this->renderProgramDoa()->renderForDoa($programDoa);

        $response = $this->getResponse();
        $response->getHeaders()
                 ->addHeaderLine('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 36000))
                 ->addHeaderLine("Cache-Control: max-age=36000, must-revalidate")
                 ->addHeaderLine("Pragma: public")
                 ->addHeaderLine(
                     'Content-Disposition',
                     'attachment; filename="' . $programDoa->parseFileName() . '.pdf"'
                 )
                 ->addHeaderLine('Content-Type: application/pdf')
                 ->addHeaderLine('Content-Length', strlen($renderProjectDoa->getPDFData()));

        $response->setContent($renderProjectDoa->getPDFData());

        return $response;
    }

    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function downloadAction()
    {
        set_time_limit(0);

        $doa = $this->getProgramService()->findEntityById('Doa', $this->getEvent()->getRouteMatch()->getParam('id'));

        if (is_null($doa) || sizeof($doa->getObject()) === 0) {
            return $this->notFoundAction();
        }

        /**
         * Due to the BLOB issue, we treat this as an array and we need to capture the first element
         */
        $object   = $doa->getObject()->first()->getObject();
        $response = $this->getResponse();
        $response->setContent(stream_get_contents($object));

        $response->getHeaders()
                 ->addHeaderLine('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 36000))
                 ->addHeaderLine("Cache-Control: max-age=36000, must-revalidate")
                 ->addHeaderLine(
                     'Content-Disposition',
                     'attachment; filename="' . $doa->parseFileName() . '.' .
                     $doa->getContentType()->getExtension() . '"'
                 )
                 ->addHeaderLine("Pragma: public")
                 ->addHeaderLine('Content-Type: ' . $doa->getContentType()->getContentType())
                 ->addHeaderLine('Content-Length: ' . $doa->getSize());

        return $this->response;
    }
}
