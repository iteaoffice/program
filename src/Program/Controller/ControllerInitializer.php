<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Program
 * @package     Controller
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   2004-2014 Japaveh Webdesign
 * @license     http://solodb.net/license.txt proprietary
 * @link        http://solodb.net
 */
namespace Program\Controller;

use Program\Service\CallService;
use Program\Service\CallServiceAwareInterface;
use Program\Service\FormService;
use Program\Service\FormServiceAwareInterface;
use Program\Service\ProgramService;
use Program\Service\ProgramServiceAwareInterface;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Program
 * @package     Controller
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   2004-2014 Japaveh Webdesign
 * @license     http://solodb.net/license.txt proprietary
 * @link        http://solodb.net
 */
class ControllerInitializer implements InitializerInterface
{
    /**
     * @param                                           $instance
     * @param ServiceLocatorInterface|ControllerManager $serviceLocator
     *
     * @return $this
     */
    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        /**
         * @var $sm ServiceLocatorInterface
         */
        $sm = $serviceLocator->getServiceLocator();

        if ($instance instanceof FormServiceAwareInterface) {
            /**
             * @var $formService FormService
             */
            $formService = $sm->get('content_form_service');
            $instance->setFormService($formService);
        }

        if ($instance instanceof ProgramServiceAwareInterface) {
            /**
             * @var $programService ProgramService
             */
            $programService = $sm->get('program_program_service');
            $instance->setProgramService($programService);
        }

        if ($instance instanceof CallServiceAwareInterface) {
            /**
             * @var $callService CallService
             */
            $callService = $sm->get('program_call_service');
            $instance->setCallService($callService);
        }
    }
}
