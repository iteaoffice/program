<?php

/**
*
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        https://itea3.org
 */

declare(strict_types=1);

namespace Program\Controller;

use Application\Service\AssertionService;
use BjyAuthorize\Controller\Plugin\IsAllowed;
use Program\Controller\Plugin\ProgramPdf;
use Program\Controller\Plugin\SessionDocument;
use Program\Controller\Plugin\SessionPdf;
use Program\Controller\Plugin\SessionSpreadsheet;
use Program\Entity\Call\Session;
use Program\Service\ProgramService;
use Project\Acl\Assertion\Idea\Idea;
use Laminas\Http\Headers;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use ZipArchive;

use function file_get_contents;
use function filesize;
use function str_replace;
use function stream_get_contents;
use function strlen;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

/**
 * @method SessionPdf sessionPdf(Session $session)
 * @method SessionSpreadsheet sessionSpreadsheet(Session $session)
 * @method SessionDocument sessionDocument(Session $session)
 * @method IsAllowed isAllowed($resource, $privilege)
 */
final class SessionController extends AbstractActionController
{
    private ProgramService $programService;
    private AssertionService $assertionService;

    public function __construct(ProgramService $programService, AssertionService $assertionService)
    {
        $this->programService = $programService;
        $this->assertionService = $assertionService;
    }

    public function downloadPdfAction(): Response
    {
        /** @var Session $session */
        $session = $this->programService->find(Session::class, (int)$this->params('id'));

        /** @var Response $response */
        $response = $this->getResponse();

        if (null === $session) {
            return $response->setStatusCode(Response::STATUS_CODE_404);
        }

        /** @var ProgramPdf $sessionPdf */
        $sessionPdf = $this->sessionPdf($session);

        $response->getHeaders()
            ->addHeaderLine('Content-Disposition', 'attachment; filename="Session_' . $session->getId() . '.pdf"')
            ->addHeaderLine('Content-Type: application/pdf')
            ->addHeaderLine('Content-Length', strlen($sessionPdf->getPDFData()));
        $response->setContent($sessionPdf->getPDFData());

        return $response;
    }

    public function downloadSpreadsheetAction(): Response
    {
        /** @var Session $session */
        $session = $this->programService->find(Session::class, (int)$this->params('id'));

        return $this->sessionSpreadsheet($session)->parseResponse();
    }

    public function downloadDocumentAction(): Response
    {
        /** @var Session $session */
        $session = $this->programService->find(Session::class, (int)$this->params('id'));

        return $this->sessionDocument($session)->parseResponse();
    }

    public function downloadAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();
        /** @var Session $session */
        $session = $this->programService->find(Session::class, (int)$this->params('id'));

        if ($session === null) {
            return $response->setStatusCode(Response::STATUS_CODE_404);
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'zip');
        $zip = new ZipArchive();

        $zip->open($tempFile);
        foreach ($session->getIdeaSession() as $ideaSession) {
            //Do a check to see if the user has access to the idea
            $this->assertionService->addResource($ideaSession->getIdea(), Idea::class);

            if (! $this->isAllowed($ideaSession->getIdea(), 'view')) {
                continue;
            }

            $dir = str_replace(':', '', $ideaSession->getIdea()->parseName());
            //$zip->addEmptyDir($dir);
            foreach ($ideaSession->getDocuments() as $document) {
                $zip->addFromString(
                    $dir . '/' . $document->getFilename(),
                    stream_get_contents($document->getObject()->first()->getObject())
                );
            }
            foreach ($ideaSession->getImages() as $image) {
                $zip->addFromString(
                    $dir . '/' . $image->getImage(),
                    stream_get_contents($image->getObject()->first()->getObject())
                );
            }
        }
        $zip->close();
        $content = file_get_contents($tempFile);
        $contentLength = filesize($tempFile);
        unlink($tempFile);

        // Prepare the response
        $response->setContent($content);
        $response->setStatusCode(Response::STATUS_CODE_200);
        $headers = new Headers();
        $headers->addHeaders(
            [
                'Content-Disposition' => 'attachment; filename="' . $session->getSession() . '.zip"',
                'Content-Type'        => 'application/zip',
                'Content-Length'      => $contentLength,
                'Expires'             => '0',
                'Cache-Control'       => 'must-revalidate',
                'Pragma'              => 'public',
            ]
        );
        $response->setHeaders($headers);

        return $response;
    }
}
