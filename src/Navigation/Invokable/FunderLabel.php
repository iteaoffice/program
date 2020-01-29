<?php

/**
*
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

declare(strict_types=1);

namespace Program\Navigation\Invokable;

use General\Navigation\Invokable\AbstractNavigationInvokable;
use Program\Entity\Funder;
use Laminas\Navigation\Page\Mvc;

/**
 * Class FunderLabel
 *
 * @package Program\Navigation\Invokable
 */
final class FunderLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        $label = $this->translate('txt-nav-view');

        if ($this->getEntities()->containsKey(Funder::class)) {
            /** @var Funder $funder */
            $funder = $this->getEntities()->get(Funder::class);
            $page->setParams(
                array_merge(
                    $page->getParams(),
                    [
                        'id' => $funder->getId(),
                    ]
                )
            );
            $label = (string)$funder;
        }
        $page->set('label', $label);
    }
}
