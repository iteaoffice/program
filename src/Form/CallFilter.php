<?php

/**
 * ITEA Office copyright message placeholder.
 *
 * PHP Version 5
 *
 * @category    Program
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   2004-2016 ITEA Office
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/program for the canonical source repository
 */

namespace Program\Form;

use Zend\Form\Fieldset;
use Zend\Form\Form;

/**
 * Jield copyright message placeholder.
 *
 * @category  Program
 *
 * @author    Johan van der Heide <info@jield.nl>
 * @copyright Copyright (c) 2004-2015 Jield (http://jield.nl)
 */
class CallFilter extends Form
{
    /**
     * CallFilter constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setAttribute('method', 'get');
        $this->setAttribute('action', '');

        $filterFieldset = new Fieldset('filter');

        $filterFieldset->add([
            'type'       => 'Zend\Form\Element\Text',
            'name'       => 'search',
            'attributes' => [
                'class'       => 'form-control',
                'placeholder' => _('txt-search'),
            ],
        ]);

        $this->add($filterFieldset);

        $this->add([
            'type'       => 'Zend\Form\Element\Submit',
            'name'       => 'submit',
            'attributes' => [
                'id'    => 'submit',
                'class' => 'btn btn-primary',
                'value' => _('txt-filter'),
            ],
        ]);

        $this->add([
            'type'       => 'Zend\Form\Element\Submit',
            'name'       => 'clear',
            'attributes' => [
                'id'    => 'cancel',
                'class' => 'btn btn-warning',
                'value' => _('txt-cancel'),
            ],
        ]);
    }
}
