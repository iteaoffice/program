<?php

/**
 * ITEA Office all rights reserved
 *
 * @category   Program
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license    https://itea3.org/license.txt proprietary
 *
 * @link       https://itea3.org
 */

declare(strict_types=1);

namespace Program\View\Helper;

use Program\Service\CallService;
use Program\ValueObject\Calls;
use Zend\View\Helper\AbstractHelper;

final class CallInformationBox extends AbstractHelper
{
    /**
     * @var CallService
     */
    private $callService;

    public function __construct(CallService $callService)
    {
        $this->callService = $callService;
    }

    public function __invoke(Calls $calls): string
    {
        $return = '';

        foreach ($calls->toArray() as $call) {
            $contents = [
                CallService::PO_NOT_OPEN  => '%call% for Project Outlines will open %diff% from now (%time%)',
                CallService::PO_OPEN      => '%call% for Project Outlines will close %diff% from now (deadline: %time%)',
                CallService::PO_CLOSED    => '%call% for Project Outlines closed %diff% ago (deadline: %time%)',
                CallService::FPP_NOT_OPEN => '%call% for Full Project Proposals will open %diff% from now (deadline: %time%)',
                CallService::FPP_OPEN     => '%call% for Full Project Proposals will close %diff% from now (deadline: %time%)',
                CallService::FPP_CLOSED   => '%call% for Full Project Proposals closed %diff% ago (deadline: %time%)',
            ];
            $callStatus = $this->callService->getCallStatus($call);
            /*
             * Return null when we have an undefined status
             */
            if ($callStatus->getResult() === CallService::UNDEFINED) {
                return '';
            }
            $result = $callStatus->getResult();
            /** @var \DateTime $referenceDate */
            $referenceDate = $callStatus->getReferenceDate();
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
                [
                    '%call%',
                    '%diff%',
                    '%time%',
                ],
                [
                    $call,
                    $dateDifference->format($format),
                    $referenceDate->format('l, d F Y H:i:s T'),
                ],
                $contents[$result]
            );
            $alert = '<div class="alert alert-%s">%s</div>';

            $type = 'info';

            $return .= \sprintf($alert, $type, $content);
        }

        return $return;
    }
}
