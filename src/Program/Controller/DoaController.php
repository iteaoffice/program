<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Affiliation
 * @package     Controller
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Program\Controller;

use Zend\View\Model\ViewModel;
use Zend\Validator\File\FilesSize;

use Program\Form\UploadDoa;
use Program\Entity;
use Program\Entity\Doa;

/**
 * @category    Program
 * @package     Controller
 */
class DoaController extends ProgramAbstractController
{

    /**
     * Upload a DOA for a program (based on the affiliation, to be sure that the organisation is
     * active in at least a project in the database)
     *
     * @return ViewModel
     */
    public function uploadAction()
    {
        $affiliationService = $this->getAffiliationService()->setAffiliationId(
            $this->getEvent()->getRouteMatch()->getParam('affiliation-id')
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
                $doa->setOrganisation($affiliationService->getAffiliation()->getOrganisation());
                $doa->setProgram($affiliationService->getAffiliation()->getProject()->getCall()->getProgram());

                $doaObject->setDoa($doa);

                $this->getProgramService()->newEntity($doaObject);

                $this->flashMessenger()->setNamespace('success')->addMessage(
                    sprintf(_("txt-doa-for-organisation-%s-in-program-%s-has-been-uploaded"),
                        $affiliationService->getAffiliation()->getOrganisation(),
                        $affiliationService->getAffiliation()->getProject()->getCall()->getProgram()
                    )
                );
            }

            $this->redirect()->toRoute('community/affiliation/affiliation',
                array('id' => $affiliationService->getAffiliation()->getId()),
                array('fragment' => 'details')
            );
        }

        return new ViewModel(array(
            'affiliationService' => $affiliationService,
            'form'               => $form
        ));
    }

    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function renderAction()
    {
        $affiliationService = $this->getAffiliationService()->setAffiliationId(
            $this->getEvent()->getRouteMatch()->getParam('affiliation-id')
        );

        //Create an empty Doa object
        $programDoa = new Doa();
        $programDoa->setContact($this->zfcUserAuthentication()->getIdentity());
        $programDoa->setOrganisation($affiliationService->getAffiliation()->getOrganisation());
        $programDoa->setProgram($affiliationService->getAffiliation()->getProject()->getCall()->getProgram());

        $renderProjectDoa = $this->renderProgramDoa()->renderDoa($programDoa);

        $response = $this->getResponse();
        $response->getHeaders()
            ->addHeaderLine('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 36000))
            ->addHeaderLine("Cache-Control: max-age=36000, must-revalidate")
            ->addHeaderLine("Pragma: public")
            ->addHeaderLine('Content-Disposition', 'attachment; filename="' . $programDoa->parseFileName() . '.pdf"')
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
