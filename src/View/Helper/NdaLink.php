<?php

/**
 * ITEA Office all rights reserved
 *
 * @category   Program
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license    https://itea3.org/license.txt proprietary
 *
 * @link       https://itea3.org
 */

declare(strict_types=1);

namespace Program\View\Helper;

use Contact\Entity\Contact;
use General\ValueObject\Link\Link;
use General\View\Helper\AbstractLink;
use Program\Acl\Assertion\Nda as NdaAssertion;
use Program\Entity\Call\Call;
use Program\Entity\Nda;

/**
 * Class NdaLink
 *
 * @package Program\View\Helper
 */
final class NdaLink extends AbstractLink
{
    public function __invoke(
        Nda $nda = null,
        string $action = 'submit',
        string $show = 'text',
        Call $call = null,
        Contact $contact = null
    ): string {
        $nda ??= new Nda();

        if (! $this->hasAccess($nda, NdaAssertion::class, $action)) {
            return '';
        }

        $routeParams = [];
        $showOptions = [];
        if (! $nda->isEmpty()) {
            $routeParams['id'] = $nda->getId();
            $showOptions['name'] = $nda->parseFileName();

            if (null !== $nda->getContentType()) {
                $routeParams['ext'] = $nda->getContentType()->getExtension();
            }
        }

        if (null !== $call) {
            $routeParams['callId'] = $call->getId();
        }
        if (null !== $contact) {
            $routeParams['contactId'] = $contact->getId();
        }


        switch ($action) {
            case 'submit':
                $text = $this->translator->translate('txt-submit-nda-title');
                if (null !== $call) {
                    $text = sprintf($this->translator->translate('txt-submit-nda-for-call-%s-title'), $call->getCall());
                }
                $linkParams = [
                    'icon' => 'fa-file-o',
                    'route' => 'community/program/nda/submit',
                    'text' => $showOptions[$show] ?? $text
                ];
                break;
            case 'replace':
                $linkParams = [
                    'icon' => 'fa-refresh',
                    'route' => 'community/program/nda/replace',
                    'text' => $showOptions[$show] ?? $this->translator->translate('txt-replace-nda-title')
                ];
                break;
            case 'render':
                $text = $this->translator->translate('txt-render-general-nda-title');
                if (null !== $call) {
                    $text = sprintf($this->translator->translate('txt-render-nda-for-call-%s-title'), $call->getCall());
                }
                $linkParams = [
                    'icon' => 'fa-file-pdf-o',
                    'route' => 'community/program/nda/render',
                    'text' => $showOptions[$show] ?? $text
                ];
                break;
            case 'render-admin':
                $text = $this->translator->translate('txt-render-general-nda-title');
                if (null !== $call) {
                    $text = sprintf($this->translator->translate('txt-render-nda-for-call-%s-title'), $call->getCall());
                }
                $linkParams = [
                    'icon' => 'fa-file-pdf-o',
                    'route' => 'zfcadmin/nda/render',
                    'text' => $showOptions[$show] ?? $text
                ];
                break;
            case 'download':
                $linkParams = [
                    'icon' => 'fa-file-pdf-o',
                    'route' => 'community/program/nda/download',
                    'text' => $showOptions[$show] ?? $this->translator->translate('txt-download-nda-title')
                ];
                break;
            case 'view-admin':
                $linkParams = [
                    'icon' => 'fa-link',
                    'route' => 'zfcadmin/nda/view',
                    'text' => $showOptions[$show] ?? $this->translator->translate('txt-view-nda')
                ];
                break;
            case 'edit-admin':
                $linkParams = [
                    'icon' => 'fa-pencil-square-o',
                    'route' => 'zfcadmin/nda/edit',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-edit-nda')
                ];
                break;
            case 'upload':
                $linkParams = [
                    'icon' => 'fa-upload',
                    'route' => 'zfcadmin/nda/upload',
                    'text' => $showOptions[$show]
                        ?? $this->translator->translate('txt-upload-nda')
                ];
                break;
        }

        $linkParams['action'] = $action;
        $linkParams['show'] = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Link::fromArray($linkParams));
    }
}
