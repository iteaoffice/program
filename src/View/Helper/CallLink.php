<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Program\View\Helper;

use General\ValueObject\Link\Link;
use General\View\Helper\AbstractLink;
use Program\Entity\Call\Call;

/**
 * Class ProgramLink
 * @package Program\View\Helper
 */
final class CallLink extends AbstractLink
{
    public function __invoke(
        Call $call = null,
        string $action = 'view',
        string $show = 'name'
    ): string {
        $call ??= new Call();

        $routeParams = [];
        $showOptions = [];
        if (! $call->isEmpty()) {
            $routeParams['id'] = $call->getId();
            $showOptions['name'] = (string)$call;
            $showOptions['name-without-program'] = $call->getCall();
        }

        switch ($action) {
            case 'new':
                $linkParams = [
                    'icon' => 'fas fa-plus',
                    'route' => 'zfcadmin/call/new',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-new-program-call')
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon' => 'far fa-edit',
                    'route' => 'zfcadmin/call/edit',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-edit-program-call')
                ];
                break;
            case 'size':
                $linkParams = [
                    'icon' => 'fa-bar-chart',
                    'route' => 'zfcadmin/call/size',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-call-size')
                ];
                break;
            case 'export-size':
                $linkParams = [
                    'icon' => 'far fa-file-excel',
                    'route' => 'zfcadmin/call/export-size',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-export-call-size')
                ];
                break;
            case 'funding':
                $linkParams = [
                    'icon' => 'fas fa-euro-sign',
                    'route' => 'zfcadmin/call/funding',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-call-funding')
                ];
                break;
            case 'download-funding':
                $linkParams = [
                    'icon' => 'far fa-file-excel',
                    'route' => 'zfcadmin/call/download-funding',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-download-call-funding')
                ];
                break;
            case 'view-admin':
                $linkParams = [
                    'icon' => 'fas fa-link',
                    'route' => 'zfcadmin/call/view',
                    'text' => $showOptions[$show] ?? (string)$call
                ];
                break;
        }

        $linkParams['action'] = $action;
        $linkParams['show'] = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Link::fromArray($linkParams));
    }
}
