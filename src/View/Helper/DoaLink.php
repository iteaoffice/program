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
use Organisation\Entity\Organisation;
use Program\Acl\Assertion\Doa as DoaAssertion;
use Program\Entity\Doa;
use Program\Entity\Program;

/**
 * Class DoaLink
 *
 * @package Program\View\Helper
 */
final class DoaLink extends AbstractLink
{
    public function __invoke(
        Doa $doa = null,
        string $action = 'view',
        string $show = 'text',
        Organisation $organisation = null,
        Program $program = null
    ): string {
        $doa ??= new Doa();


        if (! $this->hasAccess($doa, DoaAssertion::class, $action)) {
            return '';
        }

        $routeParams = [];
        $showOptions = [];
        if (! $doa->isEmpty()) {
            $routeParams['id'] = $doa->getId();
            $showOptions['name'] = $doa->parseFileName();
            $routeParams['organisationId'] = $doa->getOrganisation()->getId();
            $routeParams['programId'] = $doa->getProgram()->getId();
        }

        if (null !== $organisation) {
            $routeParams['organisationId'] = $organisation->getId();
        }
        if (null !== $program) {
            $routeParams['programId'] = $program->getId();
        }

        switch ($action) {
            case 'upload':
                $linkParams = [
                    'icon' => 'fas fa-upload',
                    'route' => 'program/doa/upload',
                    'text' => $showOptions[$show]
                        ?? sprintf(
                            $this->translator->translate('txt-upload-doa-for-organisation-%s-in-program-%s-link-title'),
                            $organisation->getOrganisation(),
                            $program->getProgram()
                        )
                ];
                break;
            case 'replace':
                $linkParams = [
                    'icon' => 'fa-refresh',
                    'route' => 'community/program/doa/replace',
                    'text' => $showOptions[$show]
                        ?? sprintf(
                            $this->translator->translate('txt-replace-doa-for-organisation-%s-in-program-%s-link-title'),
                            $doa->getOrganisation()->getOrganisation(),
                            $doa->getProgram()->getProgram()
                        )
                ];
                break;
            case 'view':
                $linkParams = [
                    'icon' => 'far fa-file',
                    'route' => 'community/program/doa/view',
                    'text' => $showOptions[$show]
                        ?? sprintf(
                            $this->translator->translate('txt-view-doa-for-organisation-%s-in-program-%s-link-title'),
                            $doa->getOrganisation()->getOrganisation(),
                            $doa->getProgram()->getProgram()
                        )
                ];
                break;
            case 'download':
                $linkParams = [
                    'icon' => 'fas fa-download',
                    'route' => 'community/program/doa/download',
                    'text' => $showOptions[$show]
                        ?? sprintf(
                            $this->translator->translate('txt-download-doa-for-organisation-%s-in-program-%s-link-title'),
                            $doa->getOrganisation()->getOrganisation(),
                            $doa->getProgram()->getProgram()
                        )
                ];
                break;
        }

        $linkParams['action'] = $action;
        $linkParams['show'] = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Link::fromArray($linkParams));
    }
}
