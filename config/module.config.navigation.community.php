<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */
declare(strict_types=1);

return [
    'navigation' => [
        'community2' => [
            // And finally, here is where we define our page hierarchy
            'call' => [
                'order' => 10,
                'label' => _("txt-call-index-page"),
                'id'    => 'callindex',
                'route' => 'community/call/index',
            ],
        ],
    ],
];