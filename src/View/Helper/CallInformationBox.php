<?php

/**
 * ITEA Office all rights reserved
 *
 * @category   Program
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license    https://itea3.org/license.txt proprietary
 *
 * @link       https://itea3.org
 */

declare(strict_types=1);

namespace Program\View\Helper;

use DateTime;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Router\RouteStackInterface;
use Laminas\View\Helper\AbstractHelper;
use Program\Entity\Call\Call;
use Program\Service\CallService;
use Program\ValueObject\Calls;

use function sprintf;

final class CallInformationBox extends AbstractHelper
{
    private CallService $callService;
    private TranslatorInterface $translator;
    private RouteStackInterface $router;

    public function __construct(CallService $callService, TranslatorInterface $translator, RouteStackInterface $router)
    {
        $this->callService = $callService;
        $this->translator  = $translator;
        $this->router      = $router;
    }


    public function __invoke(Calls $calls): string
    {
        $return = '';

        /** @var Call $call */
        foreach ($calls->toArray() as $call) {
            $contents   = [
                CallService::PO_NOT_OPEN  => '%call% for Project Outlines will <strong>open</strong> %diff% from now (<strong>%time%</strong>)',
                CallService::PO_OPEN      => '%call% for Project Outlines will <strong>close</strong> %diff% from now (deadline: <strong>%time%</strong>)',
                CallService::PO_CLOSED    => '%call% for Project Outlines closed %diff% ago (deadline: %time%)',
                CallService::FPP_NOT_OPEN => '%call% for Full Project Proposals will <strong>open</strong> %diff% from now (deadline: <strong>%time%</strong>)',
                CallService::FPP_OPEN     => '%call% for Full Project Proposals will <strong>close</strong> %diff% from now (deadline: <strong>%time%</strong>)',
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
            /** @var DateTime $referenceDate */
            $referenceDate  = $callStatus->getReferenceDate();
            $today          = new DateTime();
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
                    $referenceDate->format('l, d F Y H:i T'),
                ],
                $contents[$result]
            );
            $alert   = '<div class="alert alert-%s">%s%s</div>';

            $type = 'info';

            $link = '';
            if ($this->callService->isOpen($call)) {
                $link = sprintf(
                    '<br><a class="btn btn-primary my-2" href="%s">%s %s</a>',
                    $this->router->assemble(['callId' => $call->getId()], ['name' => 'community/project/new']),
                    '<i class="fa fa-plus"></i>',
                    sprintf($this->translator->translate("txt-create-project-in-%s"), $call->getCall())
                );
            }


            $return .= sprintf($alert, $type, $content, $link);
        }

        return $return;
    }
}
