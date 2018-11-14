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
use Program\Entity\Program;
use Zend\Navigation\Page\Mvc;

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
