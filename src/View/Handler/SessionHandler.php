<?php
/**
 * ITEA Office all rights reserved
 *
 * @category   News
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  Copyright (c) 2004-2018 ITEA Office (http://itea3.org)
 */
declare(strict_types=1);

namespace Program\View\Handler;

use Content\Entity\Content;
use Content\Navigation\Service\UpdateNavigationService;
use Program\Entity\Call\Session;
use Program\Service\ProgramService;
use Zend\Authentication\AuthenticationService;
use Zend\Http\Response;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mvc\Application;
use Zend\View\HelperPluginManager;
use ZfcTwig\View\TwigRenderer;

/**
 * Class SessionHandler
 * @package Program\View\Handler
 */
final class SessionHandler extends AbstractHandler
{
    /**
     * @var ProgramService
     */
    private $programService;

    public function __construct(
        Application             $application,
        HelperPluginManager     $helperPluginManager,
        TwigRenderer            $renderer,
        AuthenticationService   $authenticationService,
        UpdateNavigationService $updateNavigationService,
        TranslatorInterface     $translator,
        ProgramService          $programService
    )
    {
        parent::__construct($application,
            $helperPluginManager,
            $renderer,
            $authenticationService,
            $updateNavigationService,
            $translator
        );
        $this->programService = $programService;
    }

    /**
     * @param Content $content
     *
     * @return null|string
     * @throws \Exception
     */
    public function __invoke(Content $content): ?string
    {
        $params  = $this->extractContentParam($content);

        switch ($content->getHandler()->getHandler()) {
            case 'session_idea':
                /** @var Session $session */
                $session = $this->programService->find(Session::class, $params['id']);

                if ($session === null) {
                    $this->response->setStatusCode(Response::STATUS_CODE_404);
                    return 'The selected session cannot be found';
                }

                $this->getHeadTitle()->append($this->translate("txt-session"));
                $this->getHeadTitle()->append($session->getSession());

                return $this->renderer->render('cms/call/session', [
                    'session' => $session
                ]);

            default:
                return sprintf(
                    'No handler available for <code>%s</code> in class <code>%s</code>',
                    $content->getHandler()->getHandler(),
                    __CLASS__
                );
        }
    }
}
