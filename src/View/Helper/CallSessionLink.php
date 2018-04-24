<?php

/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category   Program
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license    https://itea3.org/license.txt proprietary
 *
 * @link       https://itea3.org
 */

declare(strict_types=1);

namespace Program\View\Helper;

use Content\Entity\Route;
use Program\Entity\Call\Session;

/**
 * Class CallSessionLink
 *
 * @package Program\View\Helper
 */
class CallSessionLink extends AbstractLink
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @param Session $session
     * @param string  $action
     * @param string  $show
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function __invoke(
        Session $session = null,
        $action = 'view',
        $show = 'text'
    ): string {
        $this->session = $session;
        $this->setAction($action);
        $this->setShow($show);

        // Set the non-standard options needed to give an other link value
        $this->setShowOptions(
            [
                'name' => $this->getSession(),
            ]
        );

        $this->addRouterParam('id', $this->getSession()->getId());

        return $this->createLink();
    }

    /**
     * @return Session
     */
    private function getSession(): Session
    {
        if ($this->session === null) {
            $this->session = new Session();
        }

        return $this->session;
    }

    /**
     * Extract the relevant parameters based on the action.
     */
    public function parseAction(): void
    {
        switch ($this->getAction()) {
            case 'view':
                $this->addRouterParam('session', $this->getSession()->getId());
                $this->setRouter(Route::parseRouteName(Route::DEFAULT_ROUTE_HOME));
                $this->setText(sprintf($this->translate('txt-view-session-%s'), $this->getSession()->getSession()));
                break;
            case 'download':
                $this->setRouter('program/session/download');
                $this->setText(sprintf($this->translate('txt-download-session-%s'), $this->getSession()->getSession()));
                break;
            case 'view-admin':
                $this->setRouter('zfcadmin/session/view');
                $this->setText($this->getSession()->getSession());
                break;
            case 'edit-admin':
                $this->setRouter('zfcadmin/session/edit');
                $this->setText(
                    sprintf(
                        $this->translate('txt-edit-session-%s'),
                        $this->getSession()->getSession()
                    )
                );
                break;
            case 'new-admin':
                $this->setRouter('zfcadmin/session/new');
                $this->setText($this->translate('txt-new-session'));
                break;
            default:
                throw new \InvalidArgumentException(
                    sprintf(
                        '%s is an incorrect action for %s',
                        $this->getAction(),
                        __CLASS__
                    )
                );
        }
    }
}
