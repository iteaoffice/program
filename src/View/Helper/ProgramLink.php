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
use Program\Entity\Program;

/**
 * Class ProgramLink
 * @package Program\View\Helper
 */
final class ProgramLink extends AbstractLink
{
    public function __invoke(
        Program $program = null,
        string $action = 'view',
        string $show = 'name'
    ): string {
        $program ??= new Program();

        $routeParams = [];
        $showOptions = [];
        if (! $program->isEmpty()) {
            $routeParams['id'] = $program->getId();
            $showOptions['name'] = $program->getProgram();
        }

        switch ($action) {
            case 'new':
                $linkParams = [
                    'icon' => 'fas fa-plus',
                    'route' => 'zfcadmin/program/new',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-new-program')
                ];
                break;
            case 'size':
                $linkParams = [
                    'icon' => 'fa-bar-chart',
                    'route' => 'zfcadmin/program/size',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-program-size')
                ];
                break;
            case 'export-size':
                $linkParams = [
                    'icon' => 'far fa-file-excel',
                    'route' => 'zfcadmin/program/export-size',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-export-program-size')
                ];
                break;
            case 'view':
                $linkParams = [
                    'icon' => 'fas fa-link',
                    'route' => 'zfcadmin/program/view',
                    'text' => $showOptions[$show] ?? $program->getProgram()
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon' => 'far fa-edit',
                    'route' => 'zfcadmin/program/edit',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-edit-program')
                ];
                break;
        }

        $linkParams['action'] = $action;
        $linkParams['show'] = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Link::fromArray($linkParams));
    }
}
