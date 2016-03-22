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

use Affiliation\Service\AffiliationService;
use Content\Entity\Content;
use General\View\Helper\CountryMap;
use Program\Entity\Call\Call;
use Program\Entity\Call\Session;
use Program\Entity\Program;
use Program\Options\ModuleOptions;
use Program\Service\CallService;
use Program\Service\ProgramService;
use Project\Service\ProjectService;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\AbstractHelper;
use Zend\View\HelperPluginManager;
use ZfcTwig\View\TwigRenderer;

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
class ProgramHandler extends AbstractHelper implements ServiceLocatorAwareInterface
{
    /**
     * @var HelperPluginManager
     */
    protected $serviceLocator;
    /**
     * @var Session
     */
    protected $session;

    /**
     * @param Content $content
     *
     * @return string
     */
    public function __invoke(Content $content)
    {
        $this->extractContentParam($content);

        switch ($content->getHandler()->getHandler()) {
            case 'programcall_selector':
                return $this->parseCallSelector(!$this->getCallService()->isEmpty() ? $this->getCallService()->getCall()
                    : null, !$this->getProgramService()->isEmpty() ? $this->getProgramService()->getProgram() : null);
            /*
             * Shows the title , not included in the "programcall_info"
             * to allow some separation of content from the title
             */
            case 'programcall_title':
                return $this->parseProgramcallTitle(!$this->getCallService()->isEmpty() ? $this->getCallService()
                    ->getCall() : null);

            case 'programcall_project':
                return $this->parseProgramcallProjectList(!$this->getCallService()->isEmpty() ? $this->getCallService()
                    ->getCall() : null);

            /*
             * Info sheet with statistics
             */
            case 'programcall_info':
                return $this->parseProgramcallInfo(
                    !$this->getCallService()->isEmpty() ? $this->getCallService()
                    ->getCall() : null,
                    !$this->getProgramService()->isEmpty() ? $this->getProgramService()->getProgram() : null
                );

            case 'programcall_session':
                return $this->parseSessionOverview($this->getSession());

            /*
             * Map of the countries in which projects of the current call are being highlighted
             */
            case 'programcall_map':
                return $this->parseProgramcallMap(!$this->getCallService()->isEmpty() ? $this->getCallService()
                    ->getCall() : null);

            default:
                return sprintf(
                    "No handler available for <code>%s</code> in class <code>%s</code>",
                    $content->getHandler()->getHandler(),
                    __CLASS__
                );
        }
    }

    /**
     * @param $sessionId
     */
    public function setSessionId($sessionId)
    {
        $this->session = $this->getCallService()->findEntityById('Call\Session', $sessionId);
    }

    /**
     * @param Content $content
     */
    public function extractContentParam(Content $content)
    {
        if (!is_null($this->getRouteMatch()->getParam('docRef'))) {
            $this->getCallService()->setCall($this->getCallService()
                ->findEntityByDocRef('Call\Call', $this->getRouteMatch()->getParam('docRef')));
            if (!$this->getCallService()->isEmpty()) {
                $this->setCallId($this->getCallService()->getCall()->getId());
            }
        }

        foreach ($content->getContentParam() as $param) {
            /*
             * When the parameterId is 0 (so we want to get the article from the URL
             */
            switch ($param->getParameter()->getParam()) {
                case 'session':
                    if (!is_null($sessionId = $this->getRouteMatch()->getParam($param->getParameter()->getParam()))) {
                        $this->setSessionId($sessionId);
                    } else {
                        $this->setSessionId($param->getParameterId());
                    }
                    break;

                case 'call':
                    if (!is_null($callId = $this->getRouteMatch()->getParam($param->getParameter()->getParam()))) {
                        $this->setCallId($callId);
                    }
                    break;

                case 'program':
                    if (!is_null($programId = $this->getRouteMatch()->getParam($param->getParameter()->getParam()))) {
                        $this->setProgramId($programId);
                    }
                    break;
            }
        }
    }

    /**
     * @return RouteMatch
     */
    public function getRouteMatch()
    {
        return $this->getServiceLocator()->get('application')->getMvcEvent()->getRouteMatch();
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

    /**
     * @param $callId
     */
    public function setCallId($callId)
    {
        $this->getCallService()->setCallId($callId);
    }

    /**
     * @return CallService
     */
    public function getCallService()
    {
        return $this->getServiceLocator()->get(CallService::class);
    }

    /**
     * @return AffiliationService
     */
    public function getAffiliationService()
    {
        return $this->getServiceLocator()->get(AffiliationService::class);
    }

    /**
     * @param $programId
     */
    public function setProgramId($programId)
    {
        $this->getProgramService()->setProgramId($programId);
    }

    /**
     * @return ProgramService
     */
    public function getProgramService()
    {
        return $this->getServiceLocator()->get(ProgramService::class);
    }

    /**
     * @return ModuleOptions
     */
    public function getModuleOptions()
    {
        return $this->getServiceLocator()->get(ModuleOptions::class);
    }

    /**
     * @param Call $call
     *
     * @return string
     */
    public function parseCallTitle(Call $call)
    {
        return $this->getZfcTwigRenderer()->render('program/partial/entity/programcall-title', [
            'call' => $call,
        ]);
    }

    /**
     * @param Call $call
     * @param Program $program
     *
     * @return string
     */
    public function parseCallSelector(Call $call = null, Program $program = null)
    {
        $displayName = (DEBRANOVA_HOST == 'artemisia' ? 'name-without-program' : 'name');

        return $this->getZfcTwigRenderer()->render('program/partial/call-selector', [
            'displayNameCall'   => $displayName,
            'calls'             => $this->getCallService()->findNonEmptyCalls($program),
            'callId'            => !is_null($call) ? $call->getId() : null,
            'selectedProgramId' => !is_null($program) ? $program->getId() : null,
        ]);
    }

    /**
     * @return string
     */
    public function parseProgramcallMap($call)
    {
        $countries = $this->getCallService()->findCountryByCall($call);
        $options = $this->getModuleOptions();
        $mapOptions = [
            'clickable' => true,
            'colorMin'  => $options->getCountryColorFaded(),
            'colorMax'  => $options->getCountryColor(),
            'focusOn'   => ['x' => 0.5, 'y' => 0.5, 'scale' => 1.1], // Slight zoom
            'height'    => '400px',
        ];

        foreach ($countries as $country) {
            $affiliations = $this->getAffiliationService()->findAmountOfAffiliationByCountryAndCall($country, $call);
            $mapOptions['tipData'][$country->getCd()] = [
                'title' => $country->getCountry(),
                'data'  => [
                    [$this->translate('txt-partners') => $affiliations],
                ],
            ];
        }

        /**
         * @var $countryMap CountryMap
         */
        $countryMap = $this->serviceLocator->get('countryMap');

        return $countryMap($countries, null, $mapOptions);
    }

    /**
     * @return string
     */
    public function parseProgramcallProjectList($call)
    {
        $whichProjects
            = $this->getProjectModuleOptions()->getProjectHasVersions()
            ? ProjectService::WHICH_ONLY_ACTIVE : ProjectService::WHICH_ALL;

        return $this->getZfcTwigRenderer()->render('program/partial/list/project', [
            'call'     => $this->getCallService(),
            'projects' => $this->getCallService()->getProjectService()->findProjectsByCall($call, $whichProjects)
                ->getQuery()->getResult(),
        ]);
    }

    /**
     * @return string
     */
    public function parseProgramcallTitle()
    {
        return $this->getZfcTwigRenderer()->render('program/partial/entity/programcall-title', [
            'call' => $this->getCallService(),
        ]);
    }

    /**
     * @param Call $call
     * @param Program $program
     *
     * @return string
     */
    public function parseProgramcallInfo(Call $call = null, Program $program = null)
    {
        $arr = $this->getCallService()->findProjectAndPartners();

        return $this->getZfcTwigRenderer()->render('program/partial/entity/programcall-info', [
            'call'             => $call,
            'projects'         => $arr['0']['projects'],
            'partners'         => $arr['0']['partners'],
            'funding_eu'       => $arr['0']['funding_eu'],
            'funding_national' => $arr['0']['funding_national'],
        ]);
    }

    /**
     * @return TwigRenderer
     */
    public function getZfcTwigRenderer()
    {
        return $this->getServiceLocator()->get('ZfcTwigRenderer');
    }

    /**
     * @param Session $session
     *
     * @return string
     */
    public function parseSessionOverview(Session $session)
    {
        return $this->getZfcTwigRenderer()->render('program/partial/entity/session', ['session' => $session]);
    }

    /**
     * @return \Project\Options\ModuleOptions
     */
    public function getProjectModuleOptions()
    {
        return $this->getServiceLocator()->get(\Project\Options\ModuleOptions::class);
    }

    /**
     * @return Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param Session $session
     *
     * @return $this;
     */
    public function setSession($session)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * @param $string
     *
     * @return string
     */
    public function translate($string)
    {
        return $this->serviceLocator->get('translate')->__invoke($string);
    }
}