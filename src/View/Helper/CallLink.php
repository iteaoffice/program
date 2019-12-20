<?php
/**
 * ITEA Office all rights reserved
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
        if (!$call->isEmpty()) {
            $routeParams['id'] = $call->getId();
            $showOptions['name'] = (string)$call;
            $showOptions['name-without-program'] = $call->getCall();
        }

        switch ($action) {
            case 'new':
                $linkParams = [
                    'icon' => 'fa-plus',
                    'route' => 'zfcadmin/call/new',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-new-program-call')
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon' => 'fa-pencil-square-o',
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
                    'icon' => 'fa-file-excel-o',
                    'route' => 'zfcadmin/call/export-size',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-export-call-size')
                ];
                break;
            case 'funding':
                $linkParams = [
                    'icon' => 'fa-eur',
                    'route' => 'zfcadmin/call/funding',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-call-funding')
                ];
                break;
            case 'download-funding':
                $linkParams = [
                    'icon' => 'fa-file-excel-o',
                    'route' => 'zfcadmin/call/download-funding',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-download-call-funding')
                ];
                break;
            case 'view-admin':
                $linkParams = [
                    'icon' => 'fa-link',
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
