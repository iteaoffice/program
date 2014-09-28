<?php
/**
 * Program Options
 *
 * If you have a ./config/autoload/ directory set up for your project, you can
 * drop this config file in it and change the values as you wish.
 */
$options = [
    /**
     * Indicate here if a project has versions
     */
    'nda_template' => __DIR__ . '/../../../../styles/itea/template/pdf/nda-template.pdf',
    'doa_template' => __DIR__ . '/../../../../styles/itea/template/pdf/nda-template.pdf',
];
/**
 * You do not need to edit below this line
 */
return [
    'program-option' => $options,
];
