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

use Admin\Navigation\Invokable\AbstractNavigationInvokable;
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
