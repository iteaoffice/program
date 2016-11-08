<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Program
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Program\View\Helper;

use Program\Acl\Assertion\Funder as FunderAssertion;
use Program\Entity\Funder;

/**
 * Create a link to an funder.
 *
 * @category    Funder
 */
class FunderLink extends LinkAbstract
{
    /**
     * @var Funder
     */
    protected $funder;

    /**
     * @param Funder $funder
     * @param string $action
     * @param string $show
     * @param null   $page
     * @param null   $alternativeShow
     *
     * @return string
     *
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function __invoke(
        Funder $funder = null,
        $action = 'view',
        $show = 'name',
        $page = null,
        $alternativeShow = null
    ) {
        $this->setFunder($funder);
        $this->setAction($action);
        $this->setShow($show);
        $this->setPage($page);

        if (! $this->hasAccess($this->getFunder(), FunderAssertion::class, $this->getAction())) {
            return '';
        }

        /*
         * If the alternativeShow is not null, use it an otherwise take the page
         */
        if (! is_null($alternativeShow)) {
            $this->setAlternativeShow($alternativeShow);
        } else {
            $this->setAlternativeShow($page);
        }

        if (! is_null($this->getFunder()->getContact())) {
            $this->setShowOptions(
                [
                    'name' => $this->getFunder()->getContact()->getDisplayName(),
                ]
            );
        }

        $this->addRouterParam('id', $this->getFunder()->getId());

        return $this->createLink();
    }

    /**
     * @return Funder
     */
    public function getFunder()
    {
        if (is_null($this->funder)) {
            $this->funder = new Funder();
        }

        return $this->funder;
    }

    /**
     * @param Funder $funder
     */
    public function setFunder($funder)
    {
        $this->funder = $funder;
    }

    /**
     * @throws \Exception
     */
    public function parseAction()
    {
        switch ($this->getAction()) {
            case 'new':
                $this->setRouter('zfcadmin/funder/new');
                $this->setText($this->translate("txt-new-funder"));
                break;
            case 'list':
                $this->setRouter('zfcadmin/funder/list');
                $this->setText($this->translate("txt-list-funders"));

                break;
            case 'edit':
                $this->setRouter('zfcadmin/funder/edit');
                $this->setText(
                    sprintf(
                        $this->translate("txt-edit-funder-%s"),
                        $this->getFunder()->getContact()->getDisplayName()
                    )
                );
                break;
            case 'view-admin':
                $this->setRouter('zfcadmin/funder/view');
                $this->setText(
                    sprintf(
                        $this->translate("txt-view-funder-%s"),
                        $this->getFunder()->getContact()->getDisplayName()
                    )
                );
                break;
            default:
                throw new \Exception(sprintf("%s is an incorrect action for %s", $this->getAction(), __CLASS__));
        }
    }
}
