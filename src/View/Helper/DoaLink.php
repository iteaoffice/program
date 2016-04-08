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

use Organisation\Entity\Organisation;
use Program\Acl\Assertion\Doa as DoaAssertion;
use Program\Entity\Doa;
use Program\Entity\Program;

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
class DoaLink extends LinkAbstract
{
    /**
     * @param Doa          $doa
     * @param string       $action
     * @param string       $show
     * @param Organisation $organisation
     * @param Program      $program
     *
     * @return string
     *
     * @throws \Exception
     */
    public function __invoke(
        Doa $doa = null,
        $action = 'view',
        $show = 'text',
        Organisation $organisation = null,
        Program $program = null
    ) {
        $this->setDoa($doa);
        $this->setOrganisation($organisation);
        $this->setProgram($program);
        $this->setAction($action);
        $this->setShow($show);
        if (!$this->hasAccess($this->getDoa(), DoaAssertion::class, $this->getAction())) {
            return 'Access denied';
        }
        $this->addRouterParam('entity', 'Doa');
        if (!is_null($this->getDoa())) {
            $this->addRouterParam('id', $this->getDoa()->getId());
        }

        return $this->createLink();
    }


    /**
     * Extract the relevant parameters based on the action.
     */
    public function parseAction()
    {
        switch ($this->getAction()) {
            case 'upload':
                $this->setRouter('program/doa/upload');
                $this->addRouterParam('organisationId', $this->getOrganisation()->getId());
                $this->addRouterParam('programId', $this->getProgram()->getId());
                $this->setText(sprintf(
                    $this->translate("txt-upload-doa-for-organisation-%s-in-program-%s-link-title"),
                    $this->getOrganisation(),
                    $this->getProgram()
                ));
                break;
            case 'render':
                $this->setRouter('program/doa/render');
                /*
                 * The $doa can be null, we then use the $organisation and $program to produce the link
                 */
                $renderText = _("txt-render-doa-for-organisation-%s-in-program-%s-link-title");
                if (is_null($this->getDoa()->getId())) {
                    $this->setText(sprintf($renderText, $this->getOrganisation(), $this->getProgram()));
                    $this->addRouterParam('organisationId', $this->getOrganisation()->getId());
                    $this->addRouterParam('programId', $this->getProgram()->getId());
                } else {
                    $this->setText(sprintf(
                        $renderText,
                        $this->getDoa()->getOrganisation(),
                        $this->getDoa()->getProgram()
                    ));
                    $this->addRouterParam('organisationId', $this->getDoa()->getOrganisation()->getId());
                    $this->addRouterParam('programId', $this->getDoa()->getProgram()->getId());
                }
                break;
            case 'replace':
                $this->setRouter('program/doa/replace');
                $this->setText(sprintf(
                    _("txt-replace-doa-for-organisation-%s-in-program-%s-link-title"),
                    $this->getDoa()->getOrganisation(),
                    $this->getDoa()->getProgram()
                ));
                break;
            case 'download':
                $this->setRouter('program/doa/download');
                $this->setText(sprintf(
                    _("txt-download-doa-for-organisation-%s-in-program-%s-link-title"),
                    $this->getDoa()->getOrganisation(),
                    $this->getDoa()->getProgram()
                ));
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
