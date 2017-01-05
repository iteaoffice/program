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

namespace Program\Navigation\Invokable;

use Admin\Navigation\Invokable\AbstractNavigationInvokable;
use Program\Entity\Call\Call;
use Program\Entity\Nda;
use Zend\Navigation\Page\Mvc;

/**
 * Class ProjectLabel
 *
 * @package Project\Navigation\Invokable
 */
class UploadNdaLabel extends AbstractNavigationInvokable
{
    /**
     * @param Mvc $page
     *
     * @return void;
     */
    public function __invoke(Mvc $page)
    {
        if ($this->getEntities()->containsKey(Call::class)) {
            /** @var Nda $nda */
            $call = $this->getEntities()->get(Call::class);

            $label = (string)$this->translate(sprintf("txt-view-nda-for-call-%s", $call));
        } else {
            $label = $this->translate('txt-nav-nda');
        }
        $page->set('label', $label);
    }
}
