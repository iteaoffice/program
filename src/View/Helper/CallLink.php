<?php

/**
 * ITEA Office copyright message placeholder.
 *
 * @category   Program
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2015 ITEA Office
 * @license    https://itea3.org/license.txt proprietary
 *
 * @link       https://itea3.org
 */

namespace Program\View\Helper;

use Program\Entity\Call\Call;

/**
 * Create a link to an project.
 *
 * @category   Program
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @license    https://itea3.org/licence.txt proprietary
 *
 * @link       https://itea3.org
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
     *
     * @throws \Exception
     */
    public function __invoke(Call $call = null, $action = 'view', $show = 'name')
    {
        $this->setCall($call);
        $this->setAction($action);
        $this->setShow($show);

        /*
         * Set the non-standard options needed to give an other link value
         */
        if (!is_null($call)) {
            $this->addRouterParam('id', $this->getCall()->getId());

            $this->setShowOptions([
                'name'                 => $this->getCall(),
                'name-without-program' => $this->getCall()->getCall(),
            ]);
        }
        $this->addRouterParam('entity', 'call');

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
     *
     * @return CallLink
     */
    public function setCall($call)
    {
        $this->call = $call;

        return $this;
    }

    /**
     * Parse te action and fill the correct parameters.
     */
    public function parseAction()
    {
        switch ($this->getAction()) {
            case 'new':
                $this->setRouter('zfcadmin/call/new');
                $this->setText($this->translate("txt-new-program-call"));
                break;
            case 'edit':
                $this->setRouter('zfcadmin/call/edit');
                $this->setText(sprintf($this->translate("txt-edit-call-%s"), $this->getCall()));
                break;
            case 'view-admin':
                $this->setRouter('zfcadmin/call/view');
                $this->setText(sprintf($this->translate("txt-view-call-%s"), $this->getCall()));
                break;
            case 'list-admin':
                $this->setRouter('zfcadmin/call/list');
                $this->setText(sprintf($this->translate("txt-call-list")));
                break;
            case 'external':
                $this->addRouterParam('docRef', $this->getCall()->getDocRef());
                $this->setRouter('route-program_entity_call_call');
                $this->addRouterParam('call', $this->getCall()->getId());
                $this->setText(sprintf($this->translate("txt-view-call-%s"), $this->getCall()));
                break;
            case 'view-list':
                /*
                 * For a list in the front-end simply use the MatchedRouteName
                 */
                $this->addRouterParam('docRef', $this->getRouteMatch()->getParam('docRef'));
                $this->setRouter($this->getRouteMatch()->getMatchedRouteName());
                $this->addRouterParam('call', $this->getCall()->getId());
                $this->setText(sprintf($this->translate("txt-view-call-%s"), $this->getCall()));
                break;

            default:
                throw new \InvalidArgumentException(sprintf(
                    "%s is an incorrect action for %s",
                    $this->getAction(),
                    __CLASS__
                ));
        }
    }
}