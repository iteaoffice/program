<?php

/**
 * Jield BV all rights reserved.
 *
 * @category    Equipment
 *
 * @author      Dr. ir. Johan van der Heide <info@jield.nl>
 * @copyright   Copyright (c) 2004-2017 Jield BV (https://jield.nl)
 */

declare(strict_types=1);

namespace Program\Form\View\Helper;

use Laminas\Form\ElementInterface;
use Zf3Bootstrap4\Form\View\Helper\FormElement;

/**
 * Class CallSelect
 *
 * @package Admin\Form\View\Helper
 */
final class CallFormElement extends FormElement
{
    public function __invoke(ElementInterface $element = null, bool $inline = false)
    {
        $this->view->headLink()->appendStylesheet('/assets/css/bootstrap-select.min.css');
        $this->view->headLink()->appendStylesheet('/assets/css/ajax-bootstrap-select.min.css');
        $this->view->headScript()->appendFile(
            '/assets/js/bootstrap-select.min.js',
            'text/javascript'
        );
        $this->view->headScript()->appendFile(
            '/assets/js/ajax-bootstrap-select.min.js',
            'text/javascript'
        );
        $this->view->inlineScript()->appendScript(
            "$('.selectpicker-call').selectpicker();",
            'text/javascript'
        );


        if ($element) {
            return $this->render($element);
        }

        return $this;
    }

    public function render(ElementInterface $element): string
    {
        $element->setValueOptions($element->getValueOptions());

        $element->setAttribute('class', 'form-control selectpicker selectpicker-call');
        $element->setAttribute('data-live-search', 'true');

        $element->setValue($element->getValue());

        return parent::render($element);
    }
}
