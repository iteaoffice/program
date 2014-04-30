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

use Program\Entity\Program;

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
class ProgramLink extends LinkAbstract
{
    /**
     * @var Program
     */
    protected $program;

    /**
     * @param \Program\Entity\Program $program
     * @param                         $action
     * @param                         $show
     *
     * @return string
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function __invoke(Program $program = null, $action = 'view', $show = 'name')
    {
        $this->setProgram($program);
        $this->setAction($action);
        $this->setShow($show);

        /**
         * Set the non-standard options needed to give an other link value
         */
        $this->setShowOptions(
            array(
                'name' => $this->getProgram(),
            )
        );

        $this->addRouterParam('entity', 'program');

        if (!is_null($program)) {
            $this->addRouterParam('id', $this->getProgram()->getId());
        }

        return $this->createLink();
    }

    /**
     * @return Program
     */
    public function getProgram()
    {
        return $this->program;
    }

    /**
     * @param Program $program
     */
    public function setProgram($program)
    {
        $this->program = $program;
    }

    /**
     * Parse te action and fill the correct parameters
     */
    protected function parseAction()
    {
        switch ($this->getAction()) {
            case 'view-list':

                /**
                 * For a list in the front-end simply use the MatchedRouteName
                 */
                $this->setRouter($this->getRouteMatch()->getMatchedRouteName());
                $this->addRouterParam('docRef', $this->getRouteMatch()->getParam('docRef'));
                $this->addRouterParam('program', $this->getRouteMatch()->getParam('program'));

                $this->setText(sprintf(_("txt-view-program-%s"), $this->getProgram()));
                break;
            default:
                throw new \InvalidArgumentException(
                    sprintf("%s is an incorrect action for %s", $this->getAction(), __CLASS__)
                );
        }
    }
}
