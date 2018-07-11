<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        https://itea3.org
 */

declare(strict_types=1);

namespace Program\Controller;

use Application\Twig\ParseSizeExtension;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Program\Entity\Call\Session;
use Program\Form\SessionFilter;
use Program\Service\FormService;
use Program\Service\ProgramService;
use Project\Entity\Idea\Session as IdeaSession;
use Project\Service\IdeaService;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Zend\Paginator\Paginator;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Class SessionManagerController
 *
 * @package Program\Controller
 *
 * @method Plugin\GetFilter getProgramFilter()
 * @method FlashMessenger flashMessenger()
 */
final class SessionManagerController extends AbstractActionController
{
    /**
     * @var ProgramService
     */
    private $programService;

    /**
     * @var IdeaService
     */
    private $ideaService;

    /**
     * @var FormService
     */
    private $formService;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * SessionManagerController constructor.
     *
     * @param ProgramService      $programService
     * @param IdeaService         $ideaService
     * @param FormService         $formService
     * @param TranslatorInterface $translator
     * @param EntityManager       $entityManager
     */
    public function __construct(
        ProgramService      $programService,
        IdeaService         $ideaService,
        FormService         $formService,
        TranslatorInterface $translator,
        EntityManager       $entityManager
    ) {
        $this->programService = $programService;
        $this->ideaService    = $ideaService;
        $this->formService    = $formService;
        $this->translator     = $translator;
        $this->entityManager  = $entityManager;
    }

    /**
     * @return ViewModel
     */
    public function listAction()
    {
        $page         = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getProgramFilter();
        $contactQuery = $this->programService->findFiltered(Session::class, $filterPlugin->getFilter());

        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($contactQuery, false)));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 20);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(\ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new SessionFilter($this->entityManager);
        $form->setData(['filter' => $filterPlugin->getFilter()]);

        return new ViewModel([
            'paginator'     => $paginator,
            'form'          => $form,
            'encodedFilter' => \urlencode($filterPlugin->getHash()),
            'order'         => $filterPlugin->getOrder(),
            'direction'     => $filterPlugin->getDirection(),
        ]);
    }

    /**
     * @return ViewModel
     */
    public function viewAction(): ViewModel
    {
        /** @var Session $session */
        $session = $this->programService->find(Session::class, (int)$this->params('id'));

        if (null === $session) {
            return $this->notFoundAction();
        }

        $orderedIdeaSessions = [];
        foreach ($session->getIdeaSession() as $ideaSession) {
            $files = [];
            foreach ($ideaSession->getDocuments() as $document) {
                $files[$document->getDateUpdated()->format('U').'|'.$document->getId()] = [
                    'object' => $document,
                    'type'   => 'document'
                ];
            }
            foreach ($ideaSession->getImages() as $image) {
                $date = $image->getDateUpdated() ?? $image->getDateCreated();
                $files[$date->format('U').'|'.$image->getId()] = [
                    'object' => $image,
                    'type'   => 'image'
                ];
            }
            \krsort($files);
            $orderedIdeaSessions[$ideaSession->getIdea()->getNumber()] = [
                'session' => $ideaSession,
                'files'   => $files
            ];
        }
        \ksort($orderedIdeaSessions);

        return new ViewModel([
            'session'             => $session,
            'orderedIdeaSessions' => $orderedIdeaSessions,
            'ideaService'         => $this->ideaService
        ]);
    }

    /**
     * @return Response|ViewModel
     */
    public function newAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $data = $request->getPost()->toArray();

        $session = new Session();
        $form = $this->formService->prepare($session, $data);
        $form->remove('delete');

        if ($request->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/session/list');
            }

            if ($form->isValid()) {
                /** @var Session $session */
                $session = $form->getData();
                $this->programService->save($session);

                return $this->redirect()->toRoute('zfcadmin/session/edit', ['id' => $session->getId()]);
            }
        }

        return new ViewModel([
            'form' => $form
        ]);
    }

    /**
     * @return ViewModel|Response
     */
    public function editAction()
    {
        /** @var Session $session */
        $session = $this->programService->find(Session::class, (int) $this->params('id'));

        if ($session === null) {
            return $this->notFoundAction();
        }

        /** @var Request $request */
        $request = $this->getRequest();
        $data = $request->getPost()->toArray();
        // Set to empty array to allow removing all ideas
        if ($request->isPost() && !isset($data['program_entity_call_session']['ideaSession'])) {
            $data['program_entity_call_session']['ideaSession'] = [];
        }
        $form = $this->formService->prepare($session, $data);

        if ($request->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/session/list');
            }

            if (isset($data['delete'])) {
                $this->programService->delete($session);
                $this->flashMessenger()->setNamespace('success')->addMessage(
                    sprintf(
                        $this->translator->translate("txt-session-has-successfully-been-deleted")
                    )
                );

                return $this->redirect()->toRoute('zfcadmin/session/list');
            }

            if ($form->isValid()) {
                /** @var Session $session */
                $session = $form->getData();

                foreach ($session->getIdeaSession() as $ideaSession) {
                    $ideaSession->setSession($session);
                }
                $this->programService->save($session);
                return $this->redirect()->toRoute('zfcadmin/session/view', ['id' => $session->getId()]);
            }
        }

        return new ViewModel([
            'form'  => $form,
            'ideas' => $session->getCall()->getIdea()
        ]);
    }

    /**
     * @return Response|ViewModel
     */
    public function downloadAction()
    {
        /** @var Response $response */
        $response = $this->getResponse();
        /** @var Session $session */
        $session  = $this->programService->find(Session::class, (int) $this->params('id'));

        if ($session === null) {
            return $this->notFoundAction();
        }

        $tempFile = \tempnam(\sys_get_temp_dir(), 'zip');
        $zip      = new \ZipArchive();

        $zip->open($tempFile);
        foreach ($session->getIdeaSession() as $ideaSession) {
            $dir = \str_replace(':', '', $ideaSession->getIdea()->parseName());
            $zip->addEmptyDir($dir);
            foreach ($ideaSession->getDocuments() as $document) {
                $zip->addFromString(
                    $dir.'/'.$document->getFilename(),
                    \stream_get_contents($document->getObject()->first()->getObject())
                );
            }
            foreach ($ideaSession->getImages() as $image) {
                $zip->addFromString(
                    $dir.'/'.$image->getImage(),
                    \stream_get_contents($image->getObject()->first()->getObject())
                );
            }
        }
        $zip->close();
        $content       = \file_get_contents($tempFile);
        $contentLength = \filesize($tempFile);
        \unlink($tempFile);

        // Prepare the response
        $response->setContent($content);
        $response->setStatusCode(Response::STATUS_CODE_200);
        $headers = new Headers();
        $headers->addHeaders([
            'Content-Disposition' => 'attachment; filename="'.$session->getSession().'.zip"',
            'Content-Type'        => 'application/zip',
            'Content-Length'      => $contentLength,
            'Expires'             => '0',
            'Cache-Control'       => 'must-revalidate',
            'Pragma'              => 'public',
        ]);
        $response->setHeaders($headers);

        return $response;
    }

    public function uploadAction(): Response
    {
        /** @var Request $request */
        $request =  $this->getRequest();
        /** @var IdeaSession $ideaSession */
        $ideaSession = $this->ideaService->findEntityById(IdeaSession::class, (int) $this->params('id'));
        $data        = $request->getFiles()->toArray();
        $errors      = [];

        foreach ($data['file'] as $fileData) {
            try {
                $this->ideaService->addFileToIdea($ideaSession->getIdea(), $fileData, null, $ideaSession);
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }
        /** @var Response $response */
        $response = $this->getResponse();
        if (!empty($errors)) {
            $response->setStatusCode(501);
            $response->setContent(\implode(', ', $errors));
        }
        return $response;
    }

    public function ideaFilesAction(): JsonModel
    {
        /** @var IdeaSession $ideaSession */
        $ideaSession = $this->ideaService->findEntityById(IdeaSession::class, (int) $this->params('id'));
        $data        = [];
        $sizeParser  = new ParseSizeExtension();

        foreach ($ideaSession->getDocuments() as $document) {
            $data[$document->getDateUpdated()->format('U').'|'.$document->getId()] = [
                'name'         => $document->parseFileName(),
                'size'         => $sizeParser->processFilter($document->getSize()),
                'download-url' => $this->url()
                    ->fromRoute('community/idea/document/download', ['id' => $document->getId()]),
                'delete-url'   => $this->url()
                    ->fromRoute('community/idea/document/delete', ['id' => $document->getId()])
            ];
        }
        foreach ($ideaSession->getImages() as $image) {
            $date = $image->getDateUpdated() ?? $image->getDateCreated();
            $data[$date->format('U').'|'.$image->getId()] = [
                'name'         => $image->getImage(),
                'size'         => $sizeParser->processFilter($image->getSize()),
                'delete-url'   => $this->url()
                    ->fromRoute('community/idea/image/delete', ['id' => $image->getId()])
            ];
        }
        \krsort($data);

        return new JsonModel([
            'files' => \array_values($data)
        ]);
    }
}
