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
use Program\Entity\Funder;
use Zend\Navigation\Page\Mvc;

/**
 * Class FunderLabel
 *
 * @package Program\Navigation\Invokable
 */
class FunderLabel extends AbstractNavigationInvokable
{
    /**
     * Set the Project navigation label
     *
     * @param Mvc $page
     *
     * @return void;
     */
    public function __invoke(Mvc $page): void
    {
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
        } else {
            $label = $this->translate('txt-nav-view');
        }
        $page->set('label', $label);
    }
}
