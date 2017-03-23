<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

namespace Program\Form;

use Contact\Service\ContactService;
use Doctrine\Common\Collections\ArrayCollection;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 *
 */
class NdaApproval extends Form implements InputFilterProviderInterface
{
    /**
     * @param ArrayCollection $ndas
     * @param ContactService $contactService
     */
    public function __construct(ArrayCollection $ndas, ContactService $contactService)
    {
        parent::__construct();
        $this->setAttribute('method', 'post');

        $this->setAttribute('action', '');

        $this->add(
            [
                'type'       => 'Zend\Form\Element\Checkbox',
                'name'       => 'sendMail',
                'attributes' => [
                    'id' => 'send-mail-checkbox',
                ]
            ]
        );

        /*
         * Create a fieldSet per NDA (and program)
         */
        foreach ($ndas as $nda) {
            $ndaFieldset = new Fieldset('nda_' . $nda->getId());

            $ndaFieldset->add(
                [
                    'type'       => 'Zend\Form\Element\Date',
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
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'submit',
                'attributes' => [
                    'class' => "btn btn-primary",
                    'value' => _("txt-update"),
                ],
            ]
        );
        $this->add(
            [
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'cancel',
                'attributes' => [
                    'class' => "btn btn-warning",
                    'value' => _("txt-cancel"),
                ],
            ]
        );
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return [];
    }
}
