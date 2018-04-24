<?php

/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Program
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/program for the canonical source repository
 */

declare(strict_types=1);

namespace Program\Form;

use Doctrine\ORM\EntityManager;
use DoctrineORMModule\Form\Element\EntitySelect;
use Program\Entity\Program;
use Zend\Form\Fieldset;
use Zend\Form\Form;

/**
 * Class ForecastSelect
 *
 * @package Program\Form
 */
class SizeSelect extends Form
{
    /**
     * SizeSelect constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct();

        $this->setAttribute('action', '');

        $filterFieldset = new Fieldset('filter');

        $programSelector = new EntitySelect();
        $programSelector->setName('program');
        $programSelector->setAttribute('class', 'form-control');
        $programSelector->setAttribute('label', 'txt-select-a-program');
        $programSelector->setOptions(
            [
                'target_class'   => Program::class,
                'inline'         => true,
                'object_manager' => $entityManager,
            ]
        );
        $filterFieldset->add($programSelector);

        $this->add($filterFieldset);

        $this->add(
            [
                'type' => 'Zend\Form\Element\Csrf',
                'name' => 'csrf',
            ]
        );

        $this->add(
            [
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'submit',
                'attributes' => [
                    'id'    => 'submit',
                    'class' => 'btn btn-primary',
                    'value' => _('txt-filter'),
                ],
            ]
        );

        $this->add(
            [
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'clear',
                'attributes' => [
                    'id'    => 'cancel',
                    'class' => 'btn btn-warning',
                    'value' => _('txt-cancel'),
                ],
            ]
        );
    }
}
