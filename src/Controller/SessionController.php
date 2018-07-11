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

use Program\Controller\Plugin\ProgramPdf;
use Program\Controller\Plugin\SessionSpreadsheet;
use Program\Controller\Plugin\SessionDocument;
use Program\Controller\Plugin\SessionPdf;
use Program\Entity\Call\Session;
use Program\Service\ProgramService;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * Class SessionController
 *
 * @package Program\Controller
 * @method SessionPdf sessionPdf(Session $session)
 * @method SessionSpreadsheet sessionSpreadsheet(Session $session)
 * @method SessionDocument sessionDocument(Session $session)
 */
final class SessionController extends AbstractActionController
{
    /**
     * @var ProgramService
     */
    private $programService;

    public function __construct(ProgramService $programService)
    {
        $this->programService = $programService;
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

        $response->getHeaders()->addHeaderLine('Expires: ' . \gmdate('D, d M Y H:i:s \G\M\T', time() + 36000))
            ->addHeaderLine('Cache-Control: max-age=36000, must-revalidate')->addHeaderLine('Pragma: public')
            ->addHeaderLine('Content-Disposition', 'attachment; filename="Session_' . $session->getId() . '.pdf"')
            ->addHeaderLine('Content-Type: application/pdf')
            ->addHeaderLine('Content-Length', \strlen($sessionPdf->getPDFData()));
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
}
