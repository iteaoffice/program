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

use Contact\Entity\Contact;
use General\Service\GeneralService;
use Organisation\Service\OrganisationService;
use Program\Controller\Plugin\RenderDoa;
use Program\Entity;
use Program\Form\UploadDoa;
use Program\Service\ProgramService;
use setasign\Fpdi\TcpdfFpdi;
use Zend\Http\Response;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Zend\Mvc\Plugin\Identity\Identity;
use Zend\Validator\File\FilesSize;
use Zend\Validator\File\MimeType;
use Zend\View\Model\ViewModel;

/**
 * Class DoaController
 *
 * @package Program\Controller
 * @method Identity|Contact identity()
 * @method FlashMessenger flashMessenger()
 * @method RenderDoa|TcpdfFpdi renderDoa(Entity\Doa $doa)
 */
final class DoaController extends AbstractActionController
{
    /**
     * @var ProgramService
     */
    protected $programService;
    /**
     * @var OrganisationService
     */
    protected $organisationService;
    /**
     * @var GeneralService
     */
    protected $generalService;
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * DoaController constructor.
     *
     * @param ProgramService      $programService
     * @param OrganisationService $organisationService
     * @param GeneralService      $generalService
     * @param TranslatorInterface $translator
     */
    public function __construct(
        ProgramService $programService,
        OrganisationService $organisationService,
        GeneralService $generalService,
        TranslatorInterface $translator
    ) {
        $this->programService = $programService;
        $this->organisationService = $organisationService;
        $this->generalService = $generalService;
        $this->translator = $translator;
    }


    /**
     * @return ViewModel
     */
    public function viewAction(): ViewModel
    {
        /** @var Entity\Doa $doa */
        $doa = $this->programService->find(Entity\Doa::class, (int)$this->params('id'));

        if (null === $doa || \count($doa->getObject()) === 0) {
            return $this->notFoundAction();
        }

        return new ViewModel(['doa' => $doa]);
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function uploadAction()
    {
        $organisation = $this->organisationService->findOrganisationById((int)$this->params('organisationId'));
        $program = $this->programService->findProgramById((int)$this->params('programId'));

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
                $doa->setContentType(
                    $this->generalService->findContentTypeByContentTypeName($fileTypeValidator->type)
                );

                $doa->setContact($this->identity());
                $doa->setOrganisation($organisation);
                $doa->setProgram($program);
                $doaObject->setDoa($doa);
                $this->programService->save($doaObject);
                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(
                        sprintf(
                            $this->translator->translate("txt-doa-for-organisation-%s-in-program-%s-has-been-uploaded"),
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
                'organisationService' => $this->organisationService,
                'organisation'        => $organisation,
                'program'             => $program,
                'form'                => $form,
            ]
        );
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function replaceAction()
    {
        /** @var Entity\Doa $doa */
        $doa = $this->programService->find(Entity\Doa::class, (int)$this->params('id'));

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
                    $this->programService->delete($object);
                }
                //Create a article object element
                $programDoaObject = new Entity\DoaObject();
                $programDoaObject->setObject(\file_get_contents($fileData['file']['tmp_name']));
                $fileSizeValidator = new FilesSize(PHP_INT_MAX);
                $fileSizeValidator->isValid($fileData['file']);
                $doa->setSize($fileSizeValidator->size);
                $doa->setContact($this->identity());

                $fileTypeValidator = new MimeType();
                $fileTypeValidator->isValid($fileData['file']);
                $doa->setContentType(
                    $this->generalService->findContentTypeByContentTypeName($fileTypeValidator->type)
                );

                $programDoaObject->setDoa($doa);
                $this->programService->save($programDoaObject);
                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(
                        sprintf(
                            $this->translator->translate("txt-doa-for-organisation-%s-in-program-%s-has-been-uploaded"),
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
     * @return Response
     */
    public function renderAction(): Response
    {
        $organisation = $this->organisationService->findOrganisationById((int)$this->params('organisationId'));
        $program = $this->programService->findProgramById((int)$this->params('programId'));

        /** @var Response $response */
        $response = $this->getResponse();

        //Create an empty Doa object
        $programDoa = new Entity\Doa();
        $programDoa->setContact($this->identity());
        $programDoa->setOrganisation($organisation);
        $programDoa->setProgram($program);
        $renderProjectDoa = $this->renderDoa($programDoa);

        $response->getHeaders()
            ->addHeaderLine('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 36000))
            ->addHeaderLine('Cache-Control: max-age=36000, must-revalidate')
            ->addHeaderLine('Pragma: public')
            ->addHeaderLine(
                'Content-Disposition',
                'attachment; filename="' . $programDoa->parseFileName() . '.pdf"'
            )
            ->addHeaderLine('Content-Type: application/pdf')
            ->addHeaderLine('Content-Length', \strlen($renderProjectDoa->getPDFData()));
        $response->setContent($renderProjectDoa->getPDFData());

        return $response;
    }

    /**
     * @return Response
     */
    public function downloadAction(): Response
    {
        /** @var Entity\Doa $doa */
        $doa = $this->programService->find(Entity\Doa::class, (int)$this->params('id'));

        /** @var Response $response */
        $response = $this->getResponse();

        if (null === $doa || \count($doa->getObject()) === 0) {
            return $response->setStatusCode(Response::STATUS_CODE_404);
        }
        /*
         * Due to the BLOB issue, we treat this as an array and we need to capture the first element
         */
        $object = $doa->getObject()->first()->getObject();

        $response->setContent(stream_get_contents($object));
        $response->getHeaders()
            ->addHeaderLine('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 36000))
            ->addHeaderLine('Cache-Control: max-age=36000, must-revalidate')
            ->addHeaderLine(
                'Content-Disposition',
                'attachment; filename="' . $doa->parseFileName() . '.' . $doa->getContentType()->getExtension() . '"'
            )
            ->addHeaderLine('Pragma: public')
            ->addHeaderLine('Content-Type: ' . $doa->getContentType()->getContentType())
            ->addHeaderLine('Content-Length: ' . $doa->getSize());

        return $response;
    }
}
