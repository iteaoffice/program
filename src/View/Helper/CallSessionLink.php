<?php

/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category   Program
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license    https://itea3.org/license.txt proprietary
 *
 * @link       https://itea3.org
 */

declare(strict_types=1);

namespace Program\View\Helper;

use General\ValueObject\Link\Link;
use General\View\Helper\AbstractLink;
use Program\Entity\Call\Session;

/**
 * Class CallSessionLink
 *
 * @package Program\View\Helper
 */
final class CallSessionLink extends AbstractLink
{
    public function __invoke(
        Session $session = null,
        string $action = 'view',
        string $show = 'text'
    ): string {
        $session ??= new Session();

        $routeParams = [];
        $showOptions = [];
        if (! $session->isEmpty()) {
            $routeParams['id'] = $session->getId();
            $routeParams['session'] = $session->getId();
            $showOptions['name'] = $session->getSession();
        }

        switch ($action) {
            case 'download-pdf':
                $linkParams = [
                    'icon' => 'far fa-file-pdf',
                    'route' => 'community/program/session/download-pdf',
                    'text' => $showOptions[$show] ?? $this->translator->translate('txt-download-pdf-session-overview')
                ];
                break;
            case 'download-spreadsheet':
                $linkParams = [
                    'icon' => 'far fa-file-excel',
                    'route' => 'community/program/session/download-spreadsheet',
                    'text' => $showOptions[$show] ?? $this->translator->translate('txt-download-excel-session-overview')
                ];
                break;
            case 'download-document':
                $linkParams = [
                    'icon' => 'far fa-file-word',
                    'route' => 'community/program/session/download-document',
                    'text' => $showOptions[$show] ?? $this->translator->translate('txt-download-word-session-overview')
                ];
                break;
            case 'download':
                $linkParams = [
                    'icon' => 'fas fa-download',
                    'route' => 'community/program/session/download',
                    'text' => $showOptions[$show] ?? $this->translator->translate('txt-download-session-documents')
                ];
                break;
            case 'view-admin':
                $linkParams = [
                    'icon' => 'fas fa-link',
                    'route' => 'zfcadmin/session/view',
                    'text' => $showOptions[$show] ?? $session->getSession()
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon' => 'far fa-edit',
                    'route' => 'zfcadmin/session/edit',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-edit-session')
                ];
                break;
            case 'new':
                $linkParams = [
                    'icon' => 'fas fa-plus',
                    'route' => 'zfcadmin/session/new',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-new-session')
                ];
                break;
        }

        $linkParams['action'] = $action;
        $linkParams['show'] = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Link::fromArray($linkParams));
    }
}
