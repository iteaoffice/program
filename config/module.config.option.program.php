<?php

/**
 * Program Options
 *
 * If you have a ./config/autoload/ directory set up for your project, you can
 * drop this config file in it and change the values as you wish.
 */

$location = defined('ITEAOFFICE_HOST') ? ITEAOFFICE_HOST : 'test';

$options = [
    'nda_template'   => __DIR__ . '/../../../../styles/' . $location . '/template/pdf/nda-template.pdf',
    'doa_template'   => __DIR__ . '/../../../../styles/' . $location . '/template/pdf/nda-template.pdf',
    'blank_template' => __DIR__ . '/../../../../styles/' . $location . '/template/pdf/blank-template-firstpage.pdf',
    'has_nda'        => true,
    'header_logo'    => __DIR__ . '/../../../../styles/' . $location . '/template/word/logo.png',
    'footer_image'   => __DIR__ . '/../../../../styles/' . $location . '/template/word/footer.png'
];

return [
    'program_option' => $options,
];
