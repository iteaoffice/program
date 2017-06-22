<?php
/**
 * ITEA Office all rights reserved
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

use Program\Entity\Program;

/**
 * Class ProgramLink
 * @package Program\View\Helper
 */
class ProgramLink extends LinkAbstract
{
    /**
     * @param \Program\Entity\Program $program
     * @param                         $action
     * @param                         $show
     *
     * @return string
     *
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function __invoke(Program $program = null, $action = 'view', $show = 'name'): string
    {
        $this->setProgram($program);
        $this->setAction($action);
        $this->setShow($show);
        /*
         * Set the non-standard options needed to give an other link value
         */
        $this->setShowOptions(
            [
                'name' => $this->getProgram(),

            ]
        );

        if (!is_null($program)) {
            $this->addRouterParam('id', $this->getProgram()->getId());
        }

        return $this->createLink();
    }

    /**
     * Parse te action and fill the correct parameters.
     */
    public function parseAction(): void
    {
        switch ($this->getAction()) {
            case 'new':
                $this->setRouter('zfcadmin/program/new');
                $this->setText($this->translate("txt-new-program"));
                break;
            case 'size':
                $this->setRouter('zfcadmin/program/size');
                $this->setText(sprintf($this->translate("txt-program-size-%s"), $this->getProgram()));
                break;
            case 'view-admin':
                $this->setRouter('zfcadmin/program/view');
                $this->setText(sprintf($this->translate("txt-view-program-%s"), $this->getProgram()));
                break;
            case 'edit-admin':
                $this->setRouter('zfcadmin/program/edit');
                $this->setText(sprintf($this->translate("txt-edit-program-%s"), $this->getProgram()));
                break;
            case 'list-admin':
                $this->setRouter('zfcadmin/program/list');
                $this->setText(sprintf($this->translate("txt-list-programs")));
                break;
            case 'edit':
                $this->setRouter('zfcadmin/program/edit');
                $this->setText(sprintf($this->translate("txt-edit-program-%s"), $this->getProgram()));
                break;
            case 'view-list':
                /*
                 * For a list in the front-end simply use the MatchedRouteName
                 */
                $this->setRouter($this->getRouteMatch()->getMatchedRouteName());
                $this->addRouterParam('docRef', $this->getRouteMatch()->getParam('docRef'));
                $this->addRouterParam('program', $this->getProgram()->getId());
                $this->setText(sprintf(_("txt-view-program-%s"), $this->getProgram()));
                break;
            default:
                throw new \InvalidArgumentException(
                    sprintf(
                        "%s is an incorrect action for %s",
                        $this->getAction(),
                        __CLASS__
                    )
                );
        }
    }
}
