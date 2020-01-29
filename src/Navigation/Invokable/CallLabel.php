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
use Program\Entity\Call\Call;
use Laminas\Navigation\Page\Mvc;

/**
 * Class CallLabel
 *
 * @package Program\Navigation\Invokable
 */
final class CallLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        $label = $this->translate('txt-nav-view');

        if ($this->getEntities()->containsKey(Call::class)) {
            /** @var Call $call */
            $call = $this->getEntities()->get(Call::class);
            $page->setParams(
                array_merge(
                    $page->getParams(),
                    [
                        'id' => $call->getId(),
                    ]
                )
            );
            $label = (string)$call;
        }
        $page->set('label', $label);
    }
}
