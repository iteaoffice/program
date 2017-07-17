<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

declare(strict_types=1);

namespace Program\Navigation\Invokable;

use Admin\Navigation\Invokable\AbstractNavigationInvokable;
use Program\Entity\Nda;
use Zend\Navigation\Page\Mvc;

/**
 * Class ProjectLabel
 *
 * @package Project\Navigation\Invokable
 */
class NdaLabel extends AbstractNavigationInvokable
{
    /**
     * @param Mvc $page
     *
     * @return void;
     */
    public function __invoke(Mvc $page): void
    {
        if ($this->getEntities()->containsKey(Nda::class)) {
            /** @var Nda $nda */
            $nda = $this->getEntities()->get(Nda::class);

            if (!is_null($nda->getCall())) {
                $page->setParams(
                    array_merge(
                        $page->getParams(),
                        [
                            'id'     => $nda->getId(),
                            'callId' => !is_null($nda->getCall()) ?: $nda->getCall()->getId(),
                        ]
                    )
                );
                $label = (string)$nda;
            } else {
                $page->setParams(
                    array_merge(
                        $page->getParams(),
                        [
                            'id' => $nda->getId(),
                        ]
                    )
                );
                $label = (string)$nda;
            }
        } else {
            $label = $this->translate('txt-nav-nda');
        }
        $page->set('label', $label);
    }
}
