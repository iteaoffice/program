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
    'nda_template'        => __DIR__ . '/../../../../styles/' . DEBRANOVA_HOST . '/template/pdf/nda-template.pdf',
    'doa_template'        => __DIR__ . '/../../../../styles/' . DEBRANOVA_HOST . '/template/pdf/nda-template.pdf',
    'blank_template'      => __DIR__ . '/../../../../styles/' . DEBRANOVA_HOST
        . '/template/pdf/blank-template-firstpage.pdf',
    'has_nda'             => !(defined("DEBRANOVA_HOST") && DEBRANOVA_HOST === 'artemisia'),
    'country_color'       => '#00a651',
    'country_color_faded' => '#005C00',
    'require_membership'  => (defined("DEBRANOVA_HOST") && DEBRANOVA_HOST === 'penta'),
];
/**
 * You do not need to edit below this line
 */
return [
    'program_option' => $options,
];
