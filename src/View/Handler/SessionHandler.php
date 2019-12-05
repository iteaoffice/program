<?php
/**
 * ITEA Office all rights reserved
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  Copyright (c) 2019 ITEA Office (https://itea3.org)
 */
declare(strict_types=1);

namespace Program\View\Handler;

use Content\Entity\Content;
use Program\Entity\Call\Session;
use Program\Service\ProgramService;
use Project\Service\IdeaService;
use Zend\Authentication\AuthenticationService;
use Zend\Http\Response;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mvc\Application;
use Zend\View\HelperPluginManager;
use ZfcTwig\View\TwigRenderer;

/**
 * Class SessionHandler
 *
 * @package Program\View\Handler
 */
final class SessionHandler extends AbstractHandler
{
    private ProgramService $programService;
    private IdeaService $ideaService;

    public function __construct(
        Application $application,
        HelperPluginManager $helperPluginManager,
        TwigRenderer $renderer,
        AuthenticationService $authenticationService,
        TranslatorInterface $translator,
        ProgramService $programService,
        IdeaService $ideaService
    ) {
        parent::__construct(
            $application,
            $helperPluginManager,
            $renderer,
            $authenticationService,
            $translator
        );
        $this->programService = $programService;
        $this->ideaService = $ideaService;
    }

    public function __invoke(Content $content): ?string
    {
        $params = $this->extractContentParam($content);

        /** @var Session $session */
        $session = $this->programService->find(Session::class, (int)$params['id']);

        if ($session === null) {
            $this->response->setStatusCode(Response::STATUS_CODE_404);
            return 'The selected session cannot be found';
        }

        $this->getHeadTitle()->append($this->translate('txt-session'));
        $this->getHeadTitle()->append($session->getSession());

        return $this->renderer->render(
            'cms/call/session',
            [
                'session'     => $session,
                'ideaService' => $this->ideaService
            ]
        );
    }
}
