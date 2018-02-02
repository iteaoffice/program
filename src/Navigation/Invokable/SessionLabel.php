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
use Program\Entity\Call\Session;
use Zend\Navigation\Page\Mvc;

/**
 * Class SessionLabel
 * @package Program\Navigation\Invokable
 */
class SessionLabel extends AbstractNavigationInvokable
{
    /**
     * Set the session navigation label
     *
     * @param Mvc $page
     *
     * @return void;
     */
    public function __invoke(Mvc $page): void
    {
        if ($this->getEntities()->containsKey(Session::class)) {
            /** @var Session $session */
            $session = $this->getEntities()->get(Session::class);
            $page->setParams(array_merge($page->getParams(), ['id' => $session->getId()]));
            $label = $session->getSession();
        } else {
            $label = $this->translate('txt-nav-view');
        }
        $page->set('label', $label);
    }
}