<?php

/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Program
 * @package     Service
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
namespace Program\Builder;

use Contact\Entity\Contact as ContactEntity;
use Program\Entity\Call as Call;

/**
 * Class Nda
 * @package Program\Builder
 */
class Nda extends \Builder_Nda
{
    /**
     * Class constructor
     *
     * Fill the parent elements with the contact and the call
     *
     * @param ContactEntity $contact
     * @param Call          $call
     */
    public function __construct(ContactEntity $contact, $call = null)
    {
        $this->_objContact = new \Contact($contact->getId());
        if (!is_null($call)) {
            $this->_objProgramcall = new \Programcall($call->getId());
        }
    }

    /**
     * @return \Builder_Nda_Pdf
     */
    public function getPdf()
    {
        $pdf = new \Builder_Nda_Pdf($this);

        return $pdf->generate();
    }
}
