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

use Doctrine\Common\Collections\ArrayCollection;
use Program\Acl\Assertion\Nda as NdaAssertion;
use Program\Entity;
use Program\Entity\Call\Call;
use Program\Entity\Nda;

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
     * @param Nda    $nda
     * @param string $action
     * @param string $show
     * @param Call   $call   program_acl_assertion_nda
     *
     * @return string
     * @throws \Exception
     */
    public function __invoke(Nda $nda = null, $action = 'upload', $show = 'text', Call $call = null)
    {
        $this->setNda($nda);
        $this->setCall($call);
        $this->setAction($action);
        $this->setShow($show);
        if (!$this->hasAccess(
            $this->getNda(),
            NdaAssertion::class,
            $this->getAction()
        )
        ) {
            return '';
        }
        $this->addRouterParam('entity', 'Nda');
        if (!is_null($this->getNda())) {
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
            if (!is_null($this->getCall())) {
                $arrayCollection = new ArrayCollection([$this->getCall()]);
                $this->nda->setCall($arrayCollection);
            }
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
     */
    public function setCall($call)
    {
        $this->call = $call;
    }

    /**
     * Extract the relevant parameters based on the action
     *
     * @return void;
     */
    public function parseAction()
    {
        switch ($this->getAction()) {
            case 'upload':
                $this->setRouter('program/nda/upload');
                if (!is_null($this->getCall())) {
                    $this->setText(sprintf($this->translate("txt-upload-nda-for-call-%s-title"), $this->getCall()));
                    $this->addRouterParam('id', $this->getCall()->getId());
                } elseif (!is_null($this->getNda()->getCall())) {
                    $this->setText(
                        sprintf($this->translate("txt-upload-nda-for-call-%s-title"), $this->getNda()->getCall())
                    );
                    $this->addRouterParam('id', $this->getNda()->getCall()->getId());
                } else {
                    $this->setText(sprintf($this->translate("txt-upload-nda-title")));
                }
                break;
            case 'replace':
                $this->setRouter('program/nda/replace');
                $this->setText(sprintf($this->translate("txt-replace-nda-%s-title"), $this->getNda()));
                break;
            case 'render':
                $this->setRouter('program/nda/render');
                $this->setText(sprintf($this->translate("txt-render-general-nda-title")));
                /**
                 * Produce special texts for call-dedicated NDA's
                 */
                if ($call = $this->getNda()->getCall()) {
                    $this->setText(
                        sprintf($this->translate("txt-render-nda-for-call-%s-title"), $call)
                    );
                    $this->addRouterParam('id', $call->getId());
                } elseif (!is_null($this->getCall())) {
                    $this->setText(sprintf($this->translate("txt-render-nda-for-call-%s-title"), $this->getCall()));
                    $this->addRouterParam('id', $this->getCall()->getId());
                }
                break;
            case 'download':
                $this->setRouter('program/nda/download');
                $this->setText(sprintf($this->translate("txt-download-nda-%s-title"), $this->getNda()));
                break;
            default:
                throw new \InvalidArgumentException(
                    sprintf("%s is an incorrect action for %s", $this->getAction(), __CLASS__)
                );
        }
    }
}
