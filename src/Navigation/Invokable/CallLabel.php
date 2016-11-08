<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * PHP Version 5
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   2004-2016 ITEA Office
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

namespace Program\Navigation\Invokable;

use Admin\Navigation\Invokable\AbstractNavigationInvokable;
use Program\Entity\Call\Call;
use Zend\Navigation\Page\Mvc;

/**
 * Class CallLabel
 *
 * @package Program\Navigation\Invokable
 */
class CallLabel extends AbstractNavigationInvokable
{
    /**
     * Set the Project navigation label
     *
     * @param Mvc $page
     *
     * @return void;
     */
    public function __invoke(Mvc $page)
    {
        if ($this->getEntities()->containsKey(Call::class)) {
            /** @var Call $call */
            $call = $this->getEntities()->get(Call::class);
            $page->setParams(
                array_merge(
                    $page->getParams(),
                    [
                    'id' => $call->getId(),
                    ]
                )
            );
            $label = (string)$call;
        } else {
            $label = $this->translate('txt-nav-view');
        }
        $page->set('label', $label);
    }
}
