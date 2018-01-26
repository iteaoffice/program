<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Program
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2018 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

declare(strict_types=1);

namespace Program;

use Program\Service\FormService;
use Program\Service\ProgramService;
use Project\Service\IdeaService;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory;

return [
    ConfigAbstractFactory::class => [
        Controller\SessionManagerController::class => [
            ProgramService::class, IdeaService::class, FormService::class, TranslatorInterface::class
        ],
    ]
];