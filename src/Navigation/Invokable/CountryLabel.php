<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Program\Navigation\Invokable;

use General\Navigation\Invokable\AbstractNavigationInvokable;
use Program\Entity\Call\Call;
use Program\Entity\Call\Country;
use Laminas\Navigation\Page\Mvc;

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
