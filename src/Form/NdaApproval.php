<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Program\Form;

use Doctrine\Common\Collections\ArrayCollection;
use Zend\Form\Element\Checkbox;
use Zend\Form\Element\Csrf;
use Zend\Form\Element\Date;
use Zend\Form\Element\Submit;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Class NdaApproval
 *
 * @package Program\Form
 */
final class NdaApproval extends Form implements InputFilterProviderInterface
{
    public function __construct(ArrayCollection $ndas)
    {
        parent::__construct();
        $this->setAttribute('method', 'post');

        $this->setAttribute('action', '');

        $this->add(
            [
                'type'       => Checkbox::class,
                'name'       => 'sendMail',
                'attributes' => [
                    'id' => 'send-mail-checkbox',
                ]
            ]
        );

        foreach ($ndas as $nda) {
            $ndaFieldset = new Fieldset('nda_' . $nda->getId());

            $ndaFieldset->add(
                [
                    'type'       => Date::class,
                    'name'       => 'dateSigned',
                    'attributes' => [
                        'class'    => 'form-control',
                        'id'       => 'dateSigned-' . $nda->getId(),
                        'required' => true,
                    ],
                ]
            );

            $this->add($ndaFieldset);
        }
        $this->add(
            [
                'type' => Csrf::class,
                'name' => 'csrf',
            ]
        );
        $this->add(
            [
                'type'       => Submit::class,
                'name'       => 'submit',
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'value' => _('txt-update'),
                ],
            ]
        );
        $this->add(
            [
                'type'       => Submit::class,
                'name'       => 'cancel',
                'attributes' => [
                    'class' => 'btn btn-warning',
                    'value' => _('txt-cancel'),
                ],
            ]
        );
    }

    public function getInputFilterSpecification(): array
    {
        return [];
    }
}
