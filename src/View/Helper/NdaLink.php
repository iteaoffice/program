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

use Contact\Entity\Contact;
use Program\Acl\Assertion\Nda as NdaAssertion;
use Program\Entity\Call\Call;
use Program\Entity\Nda;

/**
 * Class NdaLink
 * @package Program\View\Helper
 */
class NdaLink extends LinkAbstract
{
    /**
     * @param Nda|null $nda
     * @param string $action
     * @param string $show
     * @param Call|null $call
     * @param Contact|null $contact
     * @return string
     */
    public function __invoke(
        Nda $nda = null,
        $action = 'upload',
        $show = 'text',
        Call $call = null,
        Contact $contact = null
    ): string {
        $this->setNda($nda);
        $this->setCall($call);
        $this->setAction($action);
        $this->setShow($show);
        $this->setContact($contact);

        if (!$this->hasAccess($this->getNda(), NdaAssertion::class, $this->getAction())) {
            return '';
        }

        $this->addRouterParam('id', $this->getNda()->getId());
        $this->addRouterParam('callId', $this->getCall()->getId());
        $this->addRouterParam('contactId', $this->getContact()->getId());

        /*
         * Set the non-standard options needed to give an other link value
         */
        if (!is_null($this->getNda()->getId())) {
            $this->setShowOptions(
                [
                    'name' => $this->getNda()->parseFileName(),
                ]
            );
        }


        if (!is_null($this->getNda()->getId()) && !is_null($this->getNda()->getContentType())) {
            $this->addRouterParam('ext', $this->getNda()->getContentType()->getExtension());
        }

        return $this->createLink();
    }

    /**
     * Extract the relevant parameters based on the action.
     */
    public function parseAction(): void
    {
        switch ($this->getAction()) {
            case 'upload':
                $this->setRouter('community/program/nda/upload');
                $this->setText(sprintf($this->translate("txt-upload-nda-title")));


                if (!is_null($this->getCall()->getId())) {
                    $this->setText(sprintf($this->translate("txt-upload-nda-for-call-%s-title"), $this->getCall()));
                }

                break;
            case 'submit':
                $this->setRouter('community/program/nda/submit');
                $this->setText(sprintf($this->translate("txt-submit-nda-title")));


                if (!is_null($this->getCall()->getId())) {
                    $this->setText(sprintf($this->translate("txt-submit-nda-for-call-%s-title"), $this->getCall()));
                }

                break;
            case 'replace':
                $this->setRouter('community/program/nda/replace');
                $this->setText(sprintf($this->translate("txt-replace-nda-%s-title"), $this->getNda()));
                break;
            case 'render':
                $this->setRouter('community/program/nda/render');
                $this->setText(sprintf($this->translate("txt-render-general-nda-title")));

                if (!is_null($this->getCall()->getId())) {
                    $this->setText(sprintf($this->translate("txt-render-nda-for-call-%s-title"), $this->getCall()));
                }

                break;
            case 'download':
                $this->setRouter('community/program/nda/download');
                $this->setText(sprintf($this->translate("txt-download-nda-%s-title"), $this->getNda()));
                break;
            case 'approval-admin':
                $this->setRouter('zfcadmin/nda/approval');
                $this->setText($this->translate("txt-approval-doa"));
                break;
            case 'view-admin':
                $this->setRouter('zfcadmin/nda/view');

                $this->setText(
                    sprintf(
                        $this->translate("txt-view-nda-%s-title"),
                        $this->getNda()->parseFileName()
                    )
                );

                break;
            case 'edit-admin':
                $this->setRouter('zfcadmin/nda/edit');
                $this->setText(
                    sprintf(
                        $this->translate("txt-edit-nda-%s-title"),
                        $this->getNda()->parseFileName()
                    )
                );
                break;
            case 'upload-admin':
                $this->setRouter('zfcadmin/nda/upload');
                $this->setText(sprintf($this->translate("txt-upload-nda")));

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
