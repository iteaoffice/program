<?php
/**
*
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

declare(strict_types=1);

namespace Program\Form;

use Doctrine\ORM\EntityManager;
use DoctrineORMModule\Form\Element\EntitySelect;
use Program\Entity\Call\Call;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;

/**
 * Class FundingFilter
 *
 * @package Program\Form
 */
final class FundingFilter extends Form implements InputFilterProviderInterface
{
    public function __construct(EntityManager $entityManager, \stdClass $minMaxYear)
    {
        parent::__construct();
        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '');
        $this->setAttribute('class', 'form-horizontal');

        $callSelector = new EntitySelect();
        $callSelector->setName('call');
        $callSelector->setOptions(
            [
                'label'        => _('txt-program-call'),
                'target_class' => Call::class,

                'object_manager' => $entityManager,
            ]
        );
        $this->add($callSelector);

        $year = range($minMaxYear->minYear, $minMaxYear->maxYear);

        $yearSelector = new Select();
        $yearSelector->setName('year');
        $yearSelector->setOptions(
            [
                'label'              => _('txt-year'),
                'empty_item_label'   => _('txt-all-year'),
                'display_empty_item' => true,
                'value_options'      => array_combine($year, $year),
            ]
        );
        $this->add($yearSelector);

        $this->add(
            [
                'type'       => Submit::class,
                'name'       => 'submit',
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'value' => _('txt-submit'),
                ],
            ]
        );
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'call' => [
                'required' => false,
            ],
        ];
    }
}
