<?php

/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Program
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/program for the canonical source repository
 */

namespace Program\View\Helper;

use General\Entity\Country;
use Program\Entity\Call\Call;
use Program\Entity\Call\Country as CallCountry;

/**
 * Create a link to an program.
 *
 * @category   Program
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @license    https://itea3.org/licence.txt proprietary
 *
 * @link       https://itea3.org
 */
class CallCountryLink extends LinkAbstract
{
    /**
     * @param CallCountry|null $callCountry
     * @param string           $action
     * @param string           $show
     * @param Call|null        $call
     * @param Country|null     $country
     *
     * @return string
     */
    public function __invoke(
        CallCountry $callCountry = null,
        $action = 'view',
        $show = 'name',
        Call $call = null,
        Country $country = null
    ) {
        $this->setCallCountry($callCountry);
        $this->setCountry($country);
        $this->setCall($call);
        $this->setAction($action);
        $this->setShow($show);
        /*
         * Set the non-standard options needed to give an other link value
         */
        $this->setShowOptions(
            [
                'name' => $this->getCountry()->getId(),
            ]
        );

//        if (!$this->hasAccess($this->getCountry(), CountryAssertion::class, $this->getAction())) {
//            return '';
//        }

        $this->addRouterParam('id', $this->getCallCountry()->getId());
        $this->addRouterParam('country', $this->getCountry()->getId());
        $this->addRouterParam('call', $this->getCall()->getId());

        return $this->createLink();
    }


    /**
     * Extract the relevant parameters based on the action.
     */
    public function parseAction()
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
