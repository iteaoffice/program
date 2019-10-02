<?php
/**
*
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

declare(strict_types=1);

namespace Program\Navigation\Invokable;

use Admin\Navigation\Invokable\AbstractNavigationInvokable;
use Program\Entity\Call\Call;
use Program\Entity\Nda;
use Zend\Navigation\Page\Mvc;

/**
 * Class UploadNdaLabel
 *
 * @package Program\Navigation\Invokable
 */
final class UploadNdaLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        $label = $this->translate('txt-nav-nda');

        if ($this->getEntities()->containsKey(Call::class)) {
            /** @var Nda $nda */
            $call = $this->getEntities()->get(Call::class);

            $label = sprintf($this->translate("txt-view-nda-for-call-%s"), $call);
        }
        $page->set('label', $label);
    }
}
