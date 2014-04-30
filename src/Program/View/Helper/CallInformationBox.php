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

use Program\Entity\Call\Call;
use Program\Service\CallService;
use Zend\View\Helper\AbstractHelper;

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
class CallInformationBox extends AbstractHelper
{
    /**
     * @param Call $call
     *
     * @return string
     * @throws \Exception
     */
    public function __invoke(Call $call)
    {
        $translate = $this->view->plugin('translate');

        $contents = array(
            CallService::PO_NOT_OPEN  => "%call% for Project Outlines will open %diff% from now (%time%)",
            CallService::PO_OPEN      => "%call% for Project Outlines will close %diff% from now (deadline: %time%)",
            CallService::PO_CLOSED    => "%call% for Project Outlines closed %diff% ago (deadline: %time%)",
            CallService::FPP_NOT_OPEN => "%call% for Full Project Proposals will open %diff% from now (deadline: %time%)",
            CallService::FPP_OPEN     => "%call% for Full Project Proposals will close %diff% from now (deadline: %time%)",
            CallService::FPP_CLOSED   => "%call% for Full Project Proposals closed %diff% ago (deadline: %time%)"
        );

        $callService = $this->view->getHelperPluginManager()->getServiceLocator()->get('program_call_service');
        $callService->setCall($call);

        $callStatus = $callService->getCallStatus();

        /**
         * Return null when we have an undefined status
         */
        if ($callStatus->result === CallService::UNDEFINED) {
            return null;
        }

        $result        = $callStatus->result;
        $referenceDate = $callStatus->referenceDate;

        $today = new \DateTime();

        $dateDifference = $referenceDate->diff($today);
        if ($dateDifference->days > 7) {
            $format = '%a days';
        } elseif ($dateDifference->days > 0) {
            $format = '%a days and %h hours';
        } elseif ($dateDifference->h > 0) {
            $format = '%h hours and %i minutes';
        } else {
            $format = '%i minutes';
        }

        $content = str_replace(
            array(
                '%call%',
                '%diff%',
                '%time%'
            ),
            array(
                $call,
                $dateDifference->format($format),
                $referenceDate->format('l, d F Y H:i:s T')
            ),
            $contents[$result]
        );

        $alert = '<div class="alert alert-info"><strong>%s</strong><br>%s</div>';

        return sprintf(
            $alert,
            $result,
            $content
        );
    }
}
