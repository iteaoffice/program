<?php

/**
 * ITEA Office all rights reserved
 *
 * @category   Program
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license    https://itea3.org/license.txt proprietary
 *
 * @link       https://itea3.org
 */

declare(strict_types=1);

namespace Program\View\Helper;

use Program\Entity\Call\Call;

/**
 * Class CallLink
 *
 * @package Program\View\Helper
 */
class CallLink extends AbstractLink
{
    public function __invoke(Call $call = null, $action = 'view', $show = 'name', array $classes = []): string
    {
        $this->setCall($call);
        $this->setAction($action);
        $this->setShow($show);

        $this->addClasses($classes);

        if (null !== $call) {
            $this->addRouterParam('id', $this->getCall()->getId());

            $this->setShowOptions(
                [
                    'name'                 => $this->getCall(),
                    'name-without-program' => $this->getCall()->getCall(),
                ]
            );
        }

        return $this->createLink();
    }

    public function parseAction(): void
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
            case 'size':
                $this->setRouter('zfcadmin/call/size');
                $this->setText(sprintf($this->translate("txt-call-size-%s"), $this->getCall()));
                break;
            case 'export-size':
                $this->setRouter('zfcadmin/call/export-size');
                $this->setText(sprintf($this->translate("txt-call-export-size-%s"), $this->getCall()));
                break;
            case 'funding':
                $this->setRouter('zfcadmin/call/funding');
                $this->setText(sprintf($this->translate("txt-call-funding-%s"), $this->getCall()));
                break;
            case 'download-funding':
                $this->setRouter('zfcadmin/call/download-funding');
                $this->setText(sprintf($this->translate("txt-download-funding-%s"), $this->getCall()));
                break;
            case 'view-admin':
                $this->setRouter('zfcadmin/call/view');
                $this->setText(sprintf($this->translate("txt-view-call-%s"), $this->getCall()));
                break;
            case 'list-admin':
                $this->setRouter('zfcadmin/call/list');
                $this->setText(sprintf($this->translate("txt-call-list")));
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
