<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Contact
 * @package     View
 * @subpackage  Helper
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */

namespace Program\View\Helper;

use Zend\View\HelperPluginManager;
use Zend\View\Helper\AbstractHelper;

use Program\Service\ProgramService;

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
