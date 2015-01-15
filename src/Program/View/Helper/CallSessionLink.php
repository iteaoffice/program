<?php

/**
 * ITEA Office copyright message placeholder
 *
 * PHP version 5
 *
 * @category   Program
 * @package    View
 * @subpackage Helper
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2014 ITEA Office
 * @license    http://debranova.org/license.txt proprietary
 * @link       http://debranova.org
 */
namespace Program\View\Helper;

use Program\Entity\Call\Session;

/**
 * Create a link to an project
 *
 * @category   Project
 * @package    View
 * @subpackage Helper
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @license    http://debranova.org/licence.txt proprietary
 * @link       http://debranova.org
 */
class CallSessionLink extends LinkAbstract
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @param Session $session
     * @param string  $action
     * @param string  $show
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function __invoke(
        Session $session = null,
        $action = 'view',
        $show = 'text'
    ) {
        $this->setSession($session);
        $this->setAction($action);
        $this->setShow($show);
        /**
         * Set the non-standard options needed to give an other link value
         */
        $this->setShowOptions(
            [
                'name' => $this->getSession(),
            ]
        );
        $this->addRouterParam('entity', 'Call\Session');
        if (!is_null($this->getSession()->getId())) {
            $this->addRouterParam('id', $this->getSession()->getId());
        }

        return $this->createLink();
    }

    /**
     * @return Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param Session $session
     */
    public function setSession($session)
    {
        $this->session = $session;
    }

    /**
     * Extract the relevant parameters based on the action
     *
     * @return void;
     */
    public function parseAction()
    {
        switch ($this->getAction()) {
            case 'view':
                $this->addRouterParam('session', $this->getSession()->getId());
                $this->setRouter(
                    'route-'.str_replace(
                        'doctrineormmodule_proxy___cg___',
                        '',
                        $this->getSession()->get("underscore_full_entity_name")
                    )
                );
                $this->setText(sprintf($this->translate("txt-view-session-%s"), $this->getSession()->getSession()));
                break;
            default:
                throw new \InvalidArgumentException(
                    sprintf("%s is an incorrect action for %s", $this->getAction(), __CLASS__)
                );
        }
    }
}
