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
use Program\Entity\Program;
use Laminas\Navigation\Page\Mvc;

/**
 * Class ProgramLabel
 *
 * @package Program\Navigation\Invokable
 */
final class ProgramLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        $label = $this->translate('txt-nav-view');

        if ($this->getEntities()->containsKey(Program::class)) {
            /** @var Program $program */
            $program = $this->getEntities()->get(Program::class);
            $page->setParams(
                array_merge(
                    $page->getParams(),
                    [
                        'id' => $program->getId(),
                    ]
                )
            );
            $label = (string)$program;
        }
        $page->set('label', $label);
    }
}
