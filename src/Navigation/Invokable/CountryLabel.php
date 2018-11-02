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

declare(strict_types=1);

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
final class CountryLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        $label = $this->translate('txt-nav-view');

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
            $label = sprintf("txt-country-%s", $country);
        }
        $page->set('label', $label);
    }
}
