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
use Program\Entity\Nda;
use Laminas\Navigation\Page\Mvc;

/**
 * Class NdaLabel
 *
 * @package Program\Navigation\Invokable
 */
final class NdaLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        $label = $this->translate('txt-nav-nda');

        if ($this->getEntities()->containsKey(Nda::class)) {
            /** @var Nda $nda */
            $nda = $this->getEntities()->get(Nda::class);

            if (! $nda->getCall()->isEmpty()) {
                $page->setParams(
                    \array_merge(
                        $page->getParams(),
                        [
                            'id'     => $nda->getId(),
                            'callId' => $nda->getCall()->first()->getId(),
                        ]
                    )
                );
                $label = (string)$nda;
            } else {
                $page->setParams(
                    \array_merge(
                        $page->getParams(),
                        [
                            'id' => $nda->getId(),
                        ]
                    )
                );
                $label = (string)$nda;
            }
        }
        $page->set('label', $label);
    }
}
