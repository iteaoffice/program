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
    'nda_template'          => __DIR__ . '/../../../../styles/itea/template/pdf/nda-template.pdf',
    'doa_template'          => __DIR__ . '/../../../../styles/itea/template/pdf/nda-template.pdf',
    'has_nda'               => !(defined("DEBRANOVA_HOST") && DEBRANOVA_HOST === 'artemisia'),
    'country_color'         => '#00a651',
    'country_color_faded'   => '#005C00',
];
/**
 * You do not need to edit below this line
 */
return [
    'program_option' => $options,
];
