<?php

/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Program
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/program for the canonical source repository
 */

declare(strict_types=1);

namespace Program\Form;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use DoctrineORMModule\Form\Element\EntityMultiCheckbox;
use Program\Entity\Call\Call;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\Form\Element;

/**
* Class SessionFilter
 * @package Program\Form
 */
final class SessionFilter extends Form
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct();
        $this->setAttribute('method', 'get');
        $this->setAttribute('action', '');

        $filterFieldset = new Fieldset('filter');

        $filterFieldset->add([
            'type'       => Element\Text::class,
            'name'       => 'search',
            'attributes' => [
                'class'       => 'form-control',
                'placeholder' => _('txt-search'),
            ],
        ]);

        $filterFieldset->add([
            'type'    => EntityMultiCheckbox::class,
            'name'    => 'call',
            'options' => [
                'target_class'   => Call::class,
                'find_method'    => [
                    'name'   => 'findBy',
                    'params' => [
                        'criteria' => [],
                        'orderBy'  => ['id' => Criteria::DESC],
                    ]
                ],
                'inline'         => true,
                'object_manager' => $entityManager,
                'label'          => _("txt-call"),
            ],
        ]);

        $this->add($filterFieldset);

        $this->add([
            'type'       => Element\Submit::class,
            'name'       => 'submit',
            'attributes' => [
                'id'    => 'submit',
                'class' => 'btn btn-primary',
                'value' => _('txt-filter'),
            ],
        ]);

        $this->add([
            'type'       => Element\Submit::class,
            'name'       => 'clear',
            'attributes' => [
                'id'    => 'cancel',
                'class' => 'btn btn-warning',
                'value' => _('txt-cancel'),
            ],
        ]);
    }
}
