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
use Program\Entity\Funder;

/**
 * Class FunderLink
 * @package Program\View\Helper
 */
final class FunderLink extends AbstractLink
{
    public function __invoke(
        Funder $funder = null,
        string $action = 'view',
        string $show = 'name'
    ): string {
        $funder ??= new Funder();

        $routeParams = [];
        $showOptions = [];
        if (! $funder->isEmpty()) {
            $routeParams['id'] = $funder->getId();
            $showOptions['name'] = $funder->getContact()->parseFullName();
        }

        switch ($action) {
            case 'new':
                $linkParams = [
                    'icon' => 'fas fa-plus',
                    'route' => 'zfcadmin/funder/new',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-new-funder')
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon' => 'far fa-edit',
                    'route' => 'zfcadmin/funder/edit',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-edit-funder')
                ];
                break;
            case 'view':
                $linkParams = [
                    'icon' => 'fasfa-user',
                    'route' => 'zfcadmin/funder/view',
                    'text' => $showOptions[$show] ?? $funder->getContact()->parseFullName()
                ];
                break;
        }

        $linkParams['action'] = $action;
        $linkParams['show'] = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Link::fromArray($linkParams));
    }
}
