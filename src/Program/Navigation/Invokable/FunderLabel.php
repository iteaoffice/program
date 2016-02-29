<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * PHP Version 5
 *
 * @category    Program
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   2004-2016 ITEA Office
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/program for the canonical source repository
 */
namespace Program\Navigation\Invokable;

use Admin\Navigation\Invokable\AbstractNavigationInvokable;
use Program\Entity\Funder;
use Zend\Navigation\Page\AbstractPage;

/**
 * Class FunderLabel
 *
 * @package Funder\Navigation\Invokable
 */
class FunderLabel extends AbstractNavigationInvokable
{
    /**
     * Parse a Funder navigation label
     *
     * @param AbstractPage $page
     * @param Funder       $funder
     *
     * @return string
     */
    public function __invoke(AbstractPage $page, $funder = null)
    {
        if ($funder instanceof Funder) {
            return sprintf("%s in %s", $funder->getContact()->getDisplayName(), $funder->getCountry());
        } else { // Generic fallback
            return $this->translate('txt-nav-view');
        }
    }
}
