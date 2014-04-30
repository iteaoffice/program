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

use Program\Service\ProgramService;
use Zend\View\Helper\AbstractHelper;
use Zend\View\HelperPluginManager;

/**
 * Class ContactHandler
 * @package Contact\View\Helper
 */
class ProgramServiceProxy extends AbstractHelper
{
    /**
     * @var ProgramService
     */
    protected $programService;

    /**
     * @param HelperPluginManager $helperPluginManager
     */
    public function __construct(HelperPluginManager $helperPluginManager)
    {
        $this->programService = $helperPluginManager->getServiceLocator()->get('program_program_service');
    }

    /**
     * @return ProgramService
     */
    public function __invoke()
    {
        return $this->programService;
    }
}
