<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Program
 * @package     Form
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
namespace Program\Form;

use Zend\Form\Form;
use Zend\ServiceManager\ServiceManager;

/**
 *
 */
class CreateProgram extends Form
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * Class constructor
     *
     * @param ServiceManager $serviceManager
     */
    public function __construct(ServiceManager $serviceManager)
    {
        parent::__construct('create_area');

        $this->setAttribute('method', 'post');

        $this->serviceManager = $serviceManager;

        $entityManager = $this->serviceManager->get('doctrine.entitymanager.orm_default');

        $areaFieldset = new AreaFieldset($entityManager);
        $areaFieldset->setUseAsBaseFieldset(true);
        $this->add($areaFieldset);

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Csrf',
                'name' => 'csrf'
            )
        );

        $this->add(
            array(
                'name'       => 'submit',
                'attributes' => array(
                    'type'  => 'submit',
                    'class' => "btn btn-primary",
                    'value' => "txt-submit"
                )
            )
        );
    }
}
