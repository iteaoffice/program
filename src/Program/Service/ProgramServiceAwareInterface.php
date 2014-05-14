<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Program
 * @package     Service
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   2004-2014 Japaveh Webdesign
 * @license     http://solodb.net/license.txt proprietary
 * @link        http://solodb.net
 */
namespace Program\Service;

/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Program
 * @package     Service
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   2004-2014 Japaveh Webdesign
 * @license     http://solodb.net/license.txt proprietary
 * @link        http://solodb.net
 */
interface ProgramServiceAwareInterface
{
    /**
     * The program service
     *
     * @param ProgramService $programService
     */
    public function setProgramService(ProgramService $programService);

    /**
     * Get program service
     *
     * @return ProgramService
     */
    public function getProgramService();
}
