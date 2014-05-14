<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Call
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
 * @category    Call
 * @package     Service
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   2004-2014 Japaveh Webdesign
 * @license     http://solodb.net/license.txt proprietary
 * @link        http://solodb.net
 */
interface CallServiceAwareInterface
{
    /**
     * The call service
     *
     * @param CallService $callService
     */
    public function setCallService(CallService $callService);

    /**
     * Get call service
     *
     * @return CallService
     */
    public function getCallService();
}
