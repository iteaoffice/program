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

declare(strict_types=1);

namespace Program\Controller;

use Program\Entity;
use Program\Entity\Doa;
use Program\Form\UploadDoa;
use Zend\Validator\File\FilesSize;
use Zend\Validator\File\MimeType;
use Zend\View\Model\ViewModel;

/**
 * Class DoaController
 *
 * @package Program\Controller
 */
class DoaController extends ProgramAbstractController
{
    /**
     * @return array|ViewModel
     */
    public function viewAction()
    {
        $doa = $this->getProgramService()->findEntityById(Doa::class, $this->params('id'));
        if (null === $doa || count($doa->getObject()) === 0) {
            return $this->notFoundAction();
        }

        return new ViewModel(['doa' => $doa]);
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function uploadAction()
    {
        $organisation = $this->getOrganisationService()->findOrganisationById($this->params('organisationId'));
        $program = $this->getProgramService()->findProgramById($this->params('programId'));
        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );
        $form = new UploadDoa();
        $form->setData($data);
        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('community');
            }

            if ($form->isValid()) {
                $fileData = $this->params()->fromFiles();
                //Create a article object element
                $doaObject = new Entity\DoaObject();
                $doaObject->setObject(file_get_contents($fileData['file']['tmp_name']));
                $fileSizeValidator = new FilesSize(PHP_INT_MAX);
                $fileSizeValidator->isValid($fileData['file']);
                $doa = new Entity\Doa();
                $doa->setSize($fileSizeValidator->size);

                $fileTypeValidator = new MimeType();
                $fileTypeValidator->isValid($fileData['file']);
                $doa->setContentType($this->getGeneralService()->findContentTypeByContentTypeName($fileTypeValidator->type));

                $doa->setContact($this->zfcUserAuthentication()->getIdentity());
                $doa->setOrganisation($organisation);
                $doa->setProgram($program);
                $doaObject->setDoa($doa);
                $this->getProgramService()->newEntity($doaObject);
                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(
                        sprintf(
                            $this->translate("txt-doa-for-organisation-%s-in-program-%s-has-been-uploaded"),
                            $organisation,
                            $program
                        )
                    );

                return $this->redirect()
                    ->toRoute('community/program/doa/view', ['id' => $doaObject->getDoa()->getId()]);
            }
        }

        return new ViewModel(
            [
                'organisationService' => $this->getOrganisationService(),
                'organisation'        => $organisation,
                'program'             => $program,
                'form'                => $form,
            ]
        );
    }

    /**
     * @return array|\Zend\Http\Response|ViewModel
     */
    public function replaceAction()
    {
        /** @var Doa $doa */
        $doa = $this->getProgramService()->findEntityById(Doa::class, $this->params('id'));
        if (null === $doa || count($doa->getObject()) === 0) {
            return $this->notFoundAction();
        }
        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );
        $form = new UploadDoa();
        $form->setData($data);
        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('program/doa/view', ['id' => $doa->getId()]);
            }

            if ($form->isValid()) {
                $fileData = $this->params()->fromFiles();
                /*
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

                $fileTypeValidator = new MimeType();
                $fileTypeValidator->isValid($fileData['file']);
                $doa->setContentType($this->getGeneralService()->findContentTypeByContentTypeName($fileTypeValidator->type));

                $programDoaObject->setDoa($doa);
                $this->getProgramService()->newEntity($programDoaObject);
                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(
                        sprintf(
                            _("txt-doa-for-organisation-%s-in-program-%s-has-been-uploaded"),
                            $doa->getOrganisation(),
                            $doa->getProgram()
                        )
                    );

                return $this->redirect()->toRoute('program/doa/view', ['id' => $doa->getId()]);
            }
        }

        return new ViewModel(
            [
                'doa'  => $doa,
                'form' => $form,
            ]
        );
    }

    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function renderAction()
    {
        $organisation = $this->getOrganisationService()->findOrganisationById($this->params('organisationId'));
        $program = $this->getProgramService()->findProgramById($this->params('programId'));

        //Create an empty Doa object
        $programDoa = new Doa();
        $programDoa->setContact($this->zfcUserAuthentication()->getIdentity());
        $programDoa->setOrganisation($organisation);
        $programDoa->setProgram($program);
        $renderProjectDoa = $this->renderDoa($programDoa);
        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 36000))
            ->addHeaderLine("Cache-Control: max-age=36000, must-revalidate")->addHeaderLine("Pragma: public")
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
     * @return array|\Zend\Stdlib\ResponseInterface
     */
    public function downloadAction()
    {
        set_time_limit(0);
        $doa = $this->getProgramService()->findEntityById(Doa::class, $this->params('id'));
        if (null === $doa || count($doa->getObject()) === 0) {
            return $this->notFoundAction();
        }
        /*
         * Due to the BLOB issue, we treat this as an array and we need to capture the first element
         */
        $object = $doa->getObject()->first()->getObject();
        $response = $this->getResponse();
        $response->setContent(stream_get_contents($object));
        $response->getHeaders()->addHeaderLine('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 36000))
            ->addHeaderLine("Cache-Control: max-age=36000, must-revalidate")->addHeaderLine(
                'Content-Disposition',
                'attachment; filename="' . $doa->parseFileName() . '.' . $doa->getContentType()->getExtension() . '"'
            )
            ->addHeaderLine("Pragma: public")->addHeaderLine(
                'Content-Type: ' . $doa->getContentType()
                    ->getContentType()
            )->addHeaderLine('Content-Length: ' . $doa->getSize());

        return $this->response;
    }
}
