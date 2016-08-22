<?php

/**
 * ITEA Office copyright message placeholder.
 *
 * PHP Version 5
 *
 * @category    Program
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   2004-2015 ITEA Office
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/program for the canonical source repository
 */

namespace Program\Form;

use Doctrine\ORM\EntityManager;
use DoctrineORMModule\Form\Element\EntitySelect;
use Program\Entity\Program;
use Program\Service\CallService;
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
     *
     * @param EntityManager $entityManager
     * @param CallService   $callService
     */
    public function __construct(EntityManager $entityManager, CallService $callService)
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
