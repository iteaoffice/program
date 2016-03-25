<?php

/**
 * ITEA Office copyright message placeholder.
 *
 * @category   Program
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2015 ITEA Office
 * @license    https://itea3.org/license.txt proprietary
 *
 * @link       https://itea3.org
 */

namespace Program\View\Helper;

use Program\Entity\Call\Call;
use Program\Service\CallService;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\AbstractHelper;
use Zend\View\HelperPluginManager;

/**
 * Create a link to an project.
 *
 * @category   Program
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @license    https://itea3.org/licence.txt proprietary
 *
 * @link       https://itea3.org
 */
class CallInformationBox extends AbstractHelper
{
    /**
     * @var HelperPluginManager
     */
    protected $serviceLocator;

    /**
     * @param Call $call
     *
     * @return string
     *
     * @throws \Exception
     */
    public function __invoke(Call $call)
    {
        $contents = [
            CallService::PO_NOT_OPEN  => "%call% for Project Outlines will open %diff% from now (%time%)",
            CallService::PO_OPEN      => "%call% for Project Outlines will close %diff% from now (deadline: %time%)",
            CallService::PO_GRACE     => "%call% for Project Outlines closed %diff% ago (deadline: %time%), but a grace period for accepting Project Outlines is still open",
            CallService::PO_CLOSED    => "%call% for Project Outlines closed %diff% ago (deadline: %time%)",
            CallService::FPP_NOT_OPEN => "%call% for Full Project Proposals will open %diff% from now (deadline: %time%)",
            CallService::FPP_OPEN     => "%call% for Full Project Proposals will close %diff% from now (deadline: %time%)",
            CallService::FPP_GRACE    => "%call% for Full Project Proposals will close %diff% from now (deadline: %time%), but a grace period for accepting Project Outlines is still open",
            CallService::FPP_CLOSED   => "%call% for Full Project Proposals closed %diff% ago (deadline: %time%)",
        ];
        $callStatus = $this->getCallService()->setCall($call)->getCallStatus();
        /*
         * Return null when we have an undefined status
         */
        if ($callStatus->result === CallService::UNDEFINED) {
            return '';
        }
        $result = $callStatus->result;
        /** @var \DateTime $referenceDate */
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
        $content = str_replace([
            '%call%',
            '%diff%',
            '%time%',
        ], [
            $call,
            $dateDifference->format($format),
            $referenceDate->format('l, d F Y H:i:s T'),
        ], $contents[$result]);
        $alert = '<div class="alert alert-%s"><strong>%s</strong><br>%s</div>';

        switch ($callStatus->result) {
            case CallService::PO_GRACE:
            case CallService::FPP_GRACE:
                $type = 'warning';
                break;
            default:
                $type = 'info';
                break;

        }

        return sprintf($alert, $type, $result, $content);
    }

    /**
     * @return CallService
     */
    public function getCallService()
    {
        return $this->getServiceLocator()->get(CallService::class);
    }

    /**
     * Get the service locator.
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator->getServiceLocator();
    }

    /**
     * Set the service locator.
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return AbstractHelper
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }
}
