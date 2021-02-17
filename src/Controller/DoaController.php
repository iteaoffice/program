<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
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
use setasign\Fpdi\Tcpdf\Fpdi;
use setasign\Fpdi\TcpdfFpdi;
use Laminas\Http\Response;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\Mvc\Plugin\Identity\Identity;
use Laminas\Validator\File\FilesSize;
use Laminas\Validator\File\MimeType;
use Laminas\View\Model\ViewModel;

use function array_merge_recursive;
use function count;
use function file_get_contents;
use function strlen;

/**
 * @method Identity|Contact identity()
 * @method FlashMessenger flashMessenger()
 * @method RenderDoa|Fpdi renderDoa(Entity\Doa $doa)
 */
final class DoaController extends AbstractActionController
{
    private ProgramService $programService;
    private OrganisationService $organisationService;
    private GeneralService $generalService;
    private TranslatorInterface $translator;

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

    public function viewAction(): ViewModel
    {
        /** @var Entity\Doa $doa */
        $doa = $this->programService->find(Entity\Doa::class, (int)$this->params('id'));

        if (null === $doa || ! $doa->hasObject()) {
            return $this->notFoundAction();
        }

        return new ViewModel(['doa' => $doa]);
    }

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
                $this->flashMessenger()->addSuccessMessage(
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

    public function replaceAction()
    {
        /** @var Entity\Doa $doa */
        $doa = $this->programService->find(Entity\Doa::class, (int)$this->params('id'));

        if (null === $doa || ! $doa->hasObject()) {
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

                $doaObject = $doa->getObject();
                if (null === $doaObject) {
                    $doaObject = new Entity\DoaObject();
                    $doaObject->setDoa($doa);
                }

                $doaObject->setObject(file_get_contents($fileData['file']['tmp_name']));
                $fileSizeValidator = new FilesSize(PHP_INT_MAX);
                $fileSizeValidator->isValid($fileData['file']);
                $doa->setSize($fileSizeValidator->size);
                $doa->setContact($this->identity());

                $fileTypeValidator = new MimeType();
                $fileTypeValidator->isValid($fileData['file']);
                $doa->setContentType(
                    $this->generalService->findContentTypeByContentTypeName($fileTypeValidator->type)
                );

                $this->programService->save($doaObject);
                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate("txt-doa-for-organisation-%s-in-program-%s-has-been-replaced-successfully"),
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
            ->addHeaderLine('Pragma: public')
            ->addHeaderLine(
                'Content-Disposition',
                'attachment; filename="' . $programDoa->parseFileName() . '.pdf"'
            )
            ->addHeaderLine('Content-Type: application/pdf')
            ->addHeaderLine('Content-Length', strlen($renderProjectDoa->getPDFData()));
        $response->setContent($renderProjectDoa->getPDFData());

        return $response;
    }

    public function downloadAction(): Response
    {
        /** @var Entity\Doa $doa */
        $doa = $this->programService->find(Entity\Doa::class, (int)$this->params('id'));

        /** @var Response $response */
        $response = $this->getResponse();

        if (null === $doa || ! $doa->hasObject()) {
            return $response->setStatusCode(Response::STATUS_CODE_404);
        }
        /*
         * Due to the BLOB issue, we treat this as an array and we need to capture the first element
         */
        $object = $doa->getObject()->getObject();

        $response->setContent(stream_get_contents($object));
        $response->getHeaders()
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
