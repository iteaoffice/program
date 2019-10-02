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
use Program\Entity\Call\Call;
use Program\Entity\Call\Country as CallCountry;

/**
 * Class CallCountryLink
 * @package Program\View\Helper
 */
class CallCountryLink extends AbstractLink
{
    /**
     * @param CallCountry|null $callCountry
     * @param string $action
     * @param string $show
     * @param Call|null $call
     * @param Country|null $country
     *
     * @return string
     */
    public function __invoke(
        CallCountry $callCountry = null,
        $action = 'view',
        $show = 'name',
        Call $call = null,
        Country $country = null
    ): string {
        $this->setCallCountry($callCountry);
        $this->setCountry($country);
        $this->setCall($call);
        $this->setAction($action);
        $this->setShow($show);

        if (!$this->hasAccess(
            $this->getCallCountry(),
            \Program\Acl\Assertion\Call\Country::class,
            $this->getAction()
        )) {
            return '';
        }
        /*
         * Set the non-standard options needed to give an other link value
         */
        $this->setShowOptions(
            [
                'name' => $this->getCountry()->getId(),
            ]
        );

        $this->addRouterParam('id', $this->getCallCountry()->getId());
        $this->addRouterParam('country', $this->getCountry()->getId());
        $this->addRouterParam('call', $this->getCall()->getId());

        return $this->createLink();
    }


    /**
     * Extract the relevant parameters based on the action.
     */
    public function parseAction(): void
    {
        switch ($this->getAction()) {
            case 'new-admin':
                $this->setRouter('zfcadmin/call/country/new');
                $this->setText(
                    sprintf(
                        $this->translate("txt-add-country-information-for-for-%s-in-%s"),
                        $this->getCallCountry()->getCountry(),
                        $this->getCallCountry()->getCall()
                    )
                );
                break;
            case 'view-admin':
                $this->setRouter('zfcadmin/call/country/view');
                $this->setText(
                    sprintf(
                        $this->translate("txt-view-country-information-for-for-%s-in-%s"),
                        $this->getCallCountry()->getCountry(),
                        $this->getCallCountry()->getCall()
                    )
                );
                break;
            case 'edit-admin':
                $this->setRouter('zfcadmin/call/country/edit');
                $this->setText(
                    sprintf(
                        $this->translate("txt-edit-country-information-for-for-%s-in-%s"),
                        $this->getCallCountry()->getCountry(),
                        $this->getCallCountry()->getCall()
                    )
                );
                break;
            default:
                throw new \InvalidArgumentException(
                    sprintf(
                        "%s is an incorrect action for %s",
                        $this->getAction(),
                        __CLASS__
                    )
                );
        }
    }
}
