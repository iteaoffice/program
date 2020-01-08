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

namespace Program\View\Handler;

use Program\Service\ProgramService;
use Laminas\Authentication\AuthenticationService;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Mvc\Application;
use Laminas\View\HelperPluginManager;
use ZfcTwig\View\TwigRenderer;
use General\View\Handler\AbstractHandler;

/**
 * Class ProgramHandler
 *
 * @package Program\View\Helper
 */
final class ProgramHandler extends AbstractHandler
{
    private ProgramService $programService;

    public function __construct(
        Application $application,
        HelperPluginManager $helperPluginManager,
        TwigRenderer $renderer,
        AuthenticationService $authenticationService,
        TranslatorInterface $translator,
        ProgramService $programService
    ) {
        parent::__construct(
            $application,
            $helperPluginManager,
            $renderer,
            $authenticationService,
            $translator
        );
        $this->programService = $programService;
    }

    public function __invoke(): self
    {
        return $this;
    }

    public function parseProgramBanner(): string
    {
        return $this->renderer->render(
            'cms/program/banner-frontpage',
            ['programData' => $this->programService->findProgramData()]
        );
    }
}
