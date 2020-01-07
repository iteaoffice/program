<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Program\Form;

use Doctrine\Common\Collections\ArrayCollection;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Date;
use Laminas\Form\Element\Submit;
use Laminas\Form\Fieldset;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;

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
