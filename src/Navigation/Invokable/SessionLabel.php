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
use Program\Entity\Call\Session;
use Laminas\Navigation\Page\Mvc;

/**
 * Class SessionLabel
 *
 * @package Program\Navigation\Invokable
 */
final class SessionLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        $label = $this->translate('txt-nav-view');

        if ($this->getEntities()->containsKey(Session::class)) {
            /** @var Session $session */
            $session = $this->getEntities()->get(Session::class);
            $page->setParams(array_merge($page->getParams(), ['id' => $session->getId()]));
            $label = $session->getSession();
        }
        $page->set('label', $label);
    }
}
