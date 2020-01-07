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

namespace Program\Controller\Plugin;

use Program\Entity\Call\Session;
use Program\Options\ModuleOptions;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

/***
 * Class RenderSession
 *
 * @package Program\Controller\Plugin
 */
final class RenderSession extends AbstractPlugin
{
    private ModuleOptions $moduleOptions;
    private TranslatorInterface $translator;

    public function __construct(ModuleOptions $moduleOptions, TranslatorInterface $translator)
    {
        $this->moduleOptions = $moduleOptions;
        $this->translator = $translator;
    }

    public function __invoke(Session $session): ProgramPdf
    {
        $pdf = new ProgramPdf();
        $pdf->setTemplate($this->moduleOptions->getBlankTemplate());
        $pdf->AddPage();
        $pdf->SetFontSize(8);
        $pdf->SetTopMargin(30);

        $pdf->writeHTMLCell(
            0,
            0,
            '',
            '',
            '<h1 style="color: #00a651">' . $session->getSession() . '</h1>',
            0,
            1,
            0,
            true,
            '',
            true
        );
        $pdf->writeHTMLCell(
            0,
            0,
            '',
            '',
            $session->getDate()->format('d-m-Y'),
            0,
            1,
            0,
            true,
            '',
            true
        );
        $pdf->Ln();
        $pdf->Line(10, 42, 200, 42, ['color' => [0, 166, 81]]);

        $pdf->Ln();

        //Funding information
        $header = [
            $this->translator->translate('txt-time'),
            $this->translator->translate('txt-no'),
            $this->translator->translate('txt-idea'),
            $this->translator->translate('txt-title'),
            $this->translator->translate('txt-presenter'),
        ];

        $pitches = [];

        foreach ($session->getIdeaSession() as $ideaSession) {
            $pitches[] = [
                $ideaSession->getSchedule(),
                $ideaSession->getIdea()->getNumber(),
                $ideaSession->getIdea()->getIdea(),
                $ideaSession->getIdea()->getTitle(),
                $ideaSession->getIdea()->getContact()->getDisplayName(),
            ];
        }

        $pdf->coloredTable($header, $pitches, [15, 10, 25, 110, 30]);

        return $pdf;
    }
}
