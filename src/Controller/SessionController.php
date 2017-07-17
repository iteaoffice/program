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

use Program\Entity\Call\Session;

/**
 * Class SessionController
 *
 * @package Program\Controller
 */
class SessionController extends ProgramAbstractController
{
    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function downloadAction()
    {
        $session = $this->getProgramService()->findEntityById(Session::class, $this->params('id'));

        $renderSession = $this->renderSession($session);

        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 36000))
            ->addHeaderLine("Cache-Control: max-age=36000, must-revalidate")->addHeaderLine("Pragma: public")
            ->addHeaderLine('Content-Disposition', 'attachment; filename="Session_' . $session->getId() . '.pdf"')
            ->addHeaderLine('Content-Type: application/pdf')
            ->addHeaderLine('Content-Length', strlen($renderSession->getPDFData()));
        $response->setContent($renderSession->getPDFData());

        return $response;
    }
}
