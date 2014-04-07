<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Contact
 * @package     View
 * @subpackage  Helper
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */

namespace Program\View\Helper;

use Zend\View\HelperPluginManager;
use Zend\View\Helper\AbstractHelper;

use Program\Service\CallService;

/**
 * Class CallServiceProxy
 *
 * @package Program\View\Helper
 */
class CallServiceProxy extends AbstractHelper
{
    /**
     * @var CallService
     */
    protected $callService;

    /**
     * @param HelperPluginManager $helperPluginManager
     */
    public function __construct(HelperPluginManager $helperPluginManager)
    {
        $this->callService = $helperPluginManager->getServiceLocator()->get('program_call_service');
    }

    /**
     * @return CallService
     */
    public function __invoke()
    {
        return $this->callService;
    }
}
