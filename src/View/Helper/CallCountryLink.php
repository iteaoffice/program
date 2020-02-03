<?php

/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Program
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/program for the canonical source repository
 */

declare(strict_types=1);

namespace Program\View\Helper;

use General\Entity\Country;
use General\ValueObject\Link\Link;
use General\View\Helper\AbstractLink;
use Program\Entity\Call\Call;
use Program\Entity\Call\Country as CallCountry;

/**
 * Class CallCountryLink
 * @package Program\View\Helper
 */
final class CallCountryLink extends AbstractLink
{
    public function __invoke(
        CallCountry $callCountry = null,
        string $action = 'view',
        string $show = 'name',
        Call $call = null,
        Country $country = null
    ): string {
        $callCountry ??= new CallCountry();

        if (!$this->hasAccess($callCountry, \Program\Acl\Assertion\Call\Country::class, $action)) {
            return '';
        }

        $routeParams = [];
        $showOptions = [];
        if (!$callCountry->isEmpty()) {
            $routeParams['id'] = $callCountry->getId();
            $showOptions['name'] = $callCountry->getCountry()->getCountry();
        }

        if (null !== $call) {
            $routeParams['call'] = $call->getId();
        }
        if (null !== $country) {
            $routeParams['country'] = $country->getId();
        }


        switch ($action) {
            case 'new':
                $linkParams = [
                    'icon' => 'fas fa-plus',
                    'route' => 'zfcadmin/call/country/new',
                    'text' => $showOptions[$show]
                        ?? sprintf(
                            $this->translator->translate('txt-add-country-information-for-for-%s-in-%s'),
                            $country->getCountry(),
                            (string)$call
                        )
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon' => 'far fa-edit',
                    'route' => 'zfcadmin/call/country/edit',
                    'text' => $showOptions[$show]
                        ?? sprintf(
                            $this->translator->translate('txt-edit-country-information-for-for-%s-in-%s'),
                            $callCountry->getCountry()->getCountry(),
                            (string)$callCountry->getCall()
                        )
                ];
                break;
            case 'view':
                $linkParams = [
                    'icon' => 'fas fa-link',
                    'route' => 'zfcadmin/call/country/view',
                    'text' => $showOptions[$show]
                        ?? sprintf(
                            $this->translator->translate('txt-view-country-information-for-for-%s-in-%s'),
                            $callCountry->getCountry()->getCountry(),
                            (string)$callCountry->getCall()
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
