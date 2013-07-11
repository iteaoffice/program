<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Program
 * @package     Service
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
namespace Program\Service;

use Program\Service\FormService;

interface FormServiceAwareInterface
{
    /**
     * Get formService.
     *
     * @return FormService.
     */
    public function getFormService();

    /**
     * Set formService.
     *
     * @param FormService the value to set.
     */
    public function setFormService($formService);
}
