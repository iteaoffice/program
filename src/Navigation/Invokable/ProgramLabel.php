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
use Program\Entity\Program;
use Zend\Navigation\Page\Mvc;

/**
 * Class CallLabel
 *
 * @package Program\Navigation\Invokable
 */
class ProgramLabel extends AbstractNavigationInvokable
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
        if ($this->getEntities()->containsKey(Program::class)) {
            /** @var Program $program */
            $program = $this->getEntities()->get(Program::class);
            $page->setParams(
                array_merge(
                    $page->getParams(),
                    [
                    'id' => $program->getId(),
                    ]
                )
            );
            $label = (string)$program;
        } else {
            $label = $this->translate('txt-nav-view');
        }
        $page->set('label', $label);
    }
}
