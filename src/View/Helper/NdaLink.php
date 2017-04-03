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

namespace Program\View\Helper;

use Doctrine\Common\Collections\ArrayCollection;
use Program\Acl\Assertion\Nda as NdaAssertion;
use Program\Entity\Call\Call;
use Program\Entity\Nda;

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
class NdaLink extends LinkAbstract
{
    /**
     * @var Nda
     */
    protected $nda;
    /**
     * @var Call;
     */
    protected $call;

    /**
     * @param Nda|null  $nda
     * @param string    $action
     * @param string    $show
     * @param Call|null $call
     *
     * @return string
     */
    public function __invoke(Nda $nda = null, $action = 'upload', $show = 'text', Call $call = null)
    {
        $this->setNda($nda);
        $this->setCall($call);
        $this->setAction($action);
        $this->setShow($show);
        if (! $this->hasAccess($this->getNda(), NdaAssertion::class, $this->getAction())) {
            return '';
        }

        if (! is_null($this->getNda())) {
            $this->addRouterParam('id', $this->getNda()->getId());
        }

        return $this->createLink();
    }

    /**
     * @return Nda
     */
    public function getNda()
    {
        if (is_null($this->nda)) {
            $this->nda = new Nda();
        }

        if (! is_null($this->getCall())) {
            $arrayCollection = new ArrayCollection([$this->getCall()]);
            $this->nda->setCall($arrayCollection);
        }

        return $this->nda;
    }

    /**
     * @param Nda $nda
     */
    public function setNda($nda)
    {
        $this->nda = $nda;
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
     * @return NdaLink
     */
    public function setCall($call)
    {
        $this->call = $call;

        return $this;
    }


    /**
     * Extract the relevant parameters based on the action.
     */
    public function parseAction()
    {
        switch ($this->getAction()) {
            case 'upload':
                $this->setRouter('community/program/nda/upload');
                if (! is_null($this->getCall())) {
                    $this->setText(sprintf($this->translate("txt-upload-nda-for-call-%s-title"), $this->getCall()));
                    $this->addRouterParam('id', $this->getCall()->getId());
                } elseif (! is_null($this->getNda()->getCall())) {
                    $this->setText(
                        sprintf(
                            $this->translate("txt-upload-nda-for-call-%s-title"),
                            $this->getNda()->getCall()
                        )
                    );
                    $this->addRouterParam('id', $this->getNda()->getCall()->getId());
                } else {
                    $this->setText(sprintf($this->translate("txt-upload-nda-title")));
                }
                break;
            case 'replace':
                $this->setRouter('community/program/nda/replace');
                $this->setText(sprintf($this->translate("txt-replace-nda-%s-title"), $this->getNda()));
                break;
            case 'render':
                $this->setRouter('community/program/nda/render');
                $this->setText(sprintf($this->translate("txt-render-general-nda-title")));
                /**
                 * Produce special texts for call-dedicated NDA's
                 */
                if ($call = $this->getNda()->getCall()) {
                    $this->setText(sprintf($this->translate("txt-render-nda-for-call-%s-title"), $call));
                    $this->addRouterParam('callId', $call->getId());
                } elseif (! is_null($this->getCall())) {
                    $this->setText(sprintf($this->translate("txt-render-nda-for-call-%s-title"), $this->getCall()));
                    $this->addRouterParam('callId', $this->getCall()->getId());
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
                        $this->translate("txt-view-nda-for-%s-for-%s-title"),
                        ! is_null($this->getNda()->getCall()) ? $this->getNda()->getCall() : 'LR',
                        $this->getNda()->getContact()->getDisplayName()
                    )
                );
                break;
            case 'edit-admin':
                $this->setRouter('zfcadmin/nda/edit');
                $this->setText(
                    sprintf(
                        $this->translate("txt-edit-nda-for-%s-for-%s-link-title"),
                        ! is_null($this->getNda()->getCall()) ? $this->getNda()->getCall() : 'LR',
                        $this->getNda()->getContact()->getDisplayName()
                    )
                );
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
