<?php

/**
 * ITEA Office copyright message placeholder
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

use Program\Entity\Call\Call;

/**
 * Create a link to an project
 *
 * @category   Program
 * @package    View
 * @subpackage Helper
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @license    http://debranova.org/licence.txt proprietary
 * @link       http://debranova.org
 */
class CallLink extends LinkAbstract
{
    /**
     * @var Call
     */
    protected $call;

    /**
     * @param Call   $call
     * @param string $action
     * @param string $show
     *
     * @return string
     * @throws \Exception
     */
    public function __invoke(Call $call, $action = 'view', $show = 'name')
    {
        $this->setCall($call);
        $this->setAction($action);
        $this->setShow($show);

        /**
         * Set the non-standard options needed to give an other link value
         */
        $this->setShowOptions(
            array(
                'name' => $this->getCall(),
            )
        );

        $this->addRouterParam('entity', 'Call\Call');
        $this->addRouterParam('id', $this->getCall()->getId());

        return $this->createLink();
    }

    /**
     * @return Call
     */
    public function getCall()
    {
        return $this->call;
    }

    /**
     * @param Call $call
     */
    public function setCall($call)
    {
        $this->call = $call;
    }

    /**
     * Parse te action and fill the correct parameters
     */
    protected function parseAction()
    {
        switch ($this->getAction()) {
            case 'view-list':

                /**
                 * For a list in the front-end simply use the MatchedRouteName
                 */
                $this->setRouter('route-content_entity_node');
                $this->addRouterParam('docRef', 'all-projects');
                $this->addRouterParam('call', $this->getCall()->getId());

                $this->setText(sprintf(_("txt-view-call-%s"), $this->getCall()));
                break;
            default:
                throw new \InvalidArgumentException(
                    sprintf("%s is an incorrect action for %s", $this->getAction(), __CLASS__)
                );
        }
    }
}
