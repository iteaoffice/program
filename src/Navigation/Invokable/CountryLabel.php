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
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

namespace Program\Navigation\Invokable;

use Admin\Navigation\Invokable\AbstractNavigationInvokable;
use Program\Entity\Call\Call;
use Program\Entity\Call\Country;
use Zend\Navigation\Page\Mvc;

/**
 * Class CountryLabel
 *
 * @package Program\Navigation\Invokable
 */
class CountryLabel extends AbstractNavigationInvokable
{
    /**
     * Set the Project navigation label
     *
     * @param Mvc $page
     *
     * @return void;
     */
    public function __invoke(Mvc $page)
    {
        if ($this->getEntities()->containsKey(Country::class)) {
            /** @var Country $country */
            $country = $this->getEntities()->get(Country::class);
            $this->getEntities()->set(Call::class, $country->getCall());
            $page->setParams(
                array_merge(
                    $page->getParams(),
                    [
                        'id' => $country->getId(),
                    ]
                )
            );
            $label = (string)sprintf("txt-country-%s", $country);
        } else {
            $label = $this->translate('txt-nav-view');
        }
        $page->set('label', $label);
    }
}
