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

use Organisation\Entity\Organisation;
use Program\Acl\Assertion\Doa as DoaAssertion;
use Program\Entity\Doa;
use Program\Entity\Program;

/**
 * Class DoaLink
 * @package Program\View\Helper
 */
class DoaLink extends LinkAbstract
{
    /**
     * @param Doa|null $doa
     * @param string $action
     * @param string $show
     * @param Organisation|null $organisation
     * @param Program|null $program
     *
     * @return string
     */
    public function __invoke(
        Doa $doa = null,
        $action = 'view',
        $show = 'text',
        Organisation $organisation = null,
        Program $program = null
    ): string {
        $this->setDoa($doa);
        $this->setOrganisation($organisation);
        $this->setProgram($program);
        $this->setAction($action);
        $this->setShow($show);
        if (!$this->hasAccess($this->getDoa(), DoaAssertion::class, $this->getAction())) {
            return 'Access denied';
        }

        // Set the non-standard options needed to give an other link value
        $this->setShowOptions([
            'name' => $this->getDoa(),
        ]);

        if (!is_null($this->getDoa())) {
            $this->addRouterParam('id', $this->getDoa()->getId());
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
                $this->setRouter('program/doa/upload');
                $this->addRouterParam('organisationId', $this->getOrganisation()->getId());
                $this->addRouterParam('programId', $this->getProgram()->getId());
                $this->setText(
                    sprintf(
                        $this->translate("txt-upload-doa-for-organisation-%s-in-program-%s-link-title"),
                        $this->getOrganisation(),
                        $this->getProgram()
                    )
                );
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
                    $this->setText(
                        sprintf(
                            $renderText,
                            $this->getDoa()->getOrganisation(),
                            $this->getDoa()->getProgram()
                        )
                    );
                    $this->addRouterParam('organisationId', $this->getDoa()->getOrganisation()->getId());
                    $this->addRouterParam('programId', $this->getDoa()->getProgram()->getId());
                }
                break;
            case 'replace':
                $this->setRouter('community/program/doa/replace');
                $this->setText(
                    sprintf(
                        _("txt-replace-doa-for-organisation-%s-in-program-%s-link-title"),
                        $this->getDoa()->getOrganisation(),
                        $this->getDoa()->getProgram()
                    )
                );
                break;
            case 'view':
                $this->setRouter('community/program/doa/view');
                $this->setText(
                    sprintf(
                        _("txt-view-doa-for-organisation-%s-in-program-%s-link-title"),
                        $this->getDoa()->getOrganisation(),
                        $this->getDoa()->getProgram()
                    )
                );
                break;
            case 'download':
                $this->setRouter('community/program/doa/download');
                $this->setText(
                    sprintf(
                        _("txt-download-doa-for-organisation-%s-in-program-%s-link-title"),
                        $this->getDoa()->getOrganisation(),
                        $this->getDoa()->getProgram()
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
