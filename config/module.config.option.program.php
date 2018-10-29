<?php
/**
 * Program Options
 *
 * If you have a ./config/autoload/ directory set up for your project, you can
 * drop this config file in it and change the values as you wish.
 */

$options = [
    'nda_template'        => __DIR__ . '/../../../../styles/' . (defined("ITEAOFFICE_HOST") ? ITEAOFFICE_HOST : 'test')
        . '/template/pdf/nda-template.pdf',
    'doa_template'        => __DIR__ . '/../../../../styles/' . (defined("ITEAOFFICE_HOST") ? ITEAOFFICE_HOST : 'test')
        . '/template/pdf/nda-template.pdf',
    'blank_template'      => __DIR__ . '/../../../../styles/' . (defined("ITEAOFFICE_HOST") ? ITEAOFFICE_HOST : 'test')
        . '/template/pdf/blank-template-firstpage.pdf',
    'has_nda'             => !(defined("ITEAOFFICE_HOST")
        && (defined("ITEAOFFICE_HOST") ? ITEAOFFICE_HOST : 'test') === 'aeneas'),
    'header_logo'         => __DIR__ . '/../../../../styles/' . (defined("ITEAOFFICE_HOST") ? ITEAOFFICE_HOST : 'test')
        . '/template/word/logo.png',
    'footer_image'         => __DIR__ . '/../../../../styles/' . (defined("ITEAOFFICE_HOST") ? ITEAOFFICE_HOST : 'test')
        . '/template/word/footer.png'
];
/**
 * You do not need to edit below this line
 */
return [
    'program_option' => $options,
];
