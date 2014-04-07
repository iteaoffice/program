<?php

/**
 * ITEA Office copyright message placeholder
 *
 * @category    Affiliation
 * @package     View
 * @subpackage  Helper
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Program\View\Helper;

use Zend\View\Helper\AbstractHelper;

use Program\Entity;
use Affiliation\Entity\Affiliation;

/**
 * Create a link to an affiliation
 *
 * @category    Affiliation
 * @package     View
 * @subpackage  Helper
 */
class DoaLink extends AbstractHelper
{

    /**
     * @param Entity\Doa  $doa
     * @param string      $action
     * @param string      $show
     * @param Affiliation $affiliation
     *
     * @return string
     * @throws \Exception
     */
    public function __invoke(Entity\Doa $doa = null, $action = 'view', $show = 'name', Affiliation $affiliation = null)
    {
        $translate = $this->view->plugin('translate');
        $url       = $this->view->plugin('url');
        $serverUrl = $this->view->plugin('serverUrl');
        $isAllowed = $this->view->plugin('isAllowed');

        switch ($action) {
            case 'upload':
                $router = 'program/doa/upload';
                $text   = sprintf($translate("txt-upload-doa-for-organisation-%s-in-program-%s-link-title"),
                    $affiliation->getOrganisation(),
                    $affiliation->getProject()->getCall()->getProgram()
                );
                break;
            case 'render':
                $router = 'program/doa/render';
                $text   = sprintf($translate("txt-render-doa-for-organisation-%s-in-program-%s-link-title"),
                    $affiliation->getOrganisation(),
                    $affiliation->getProject()->getCall()->getProgram()
                );
                break;
            case 'download':
                $router = 'program/doa/download';
                $text   = sprintf($translate("txt-download-doa-for-organisation-%s-in-program-%s-link-title"),
                    $doa->getOrganisation(),
                    $doa->getProgram()
                );
                break;
            default:
                throw new \Exception(sprintf("%s is an incorrect action for %s", $action, __CLASS__));
        }

        $params = array(
            'id'             => (!is_null($doa) ? $doa->getId() : null),
            'affiliation-id' => (!is_null($affiliation) ? $affiliation->getId() : null),
            'entity'         => 'doa'
        );

        $classes     = array();
        $linkContent = array();

        switch ($show) {
            case 'icon':
                if ($action === 'edit') {
                    $linkContent[] = '<span class="glyphicon glyphicon-edit"></span>';
                } elseif ($action === 'download') {
                    $linkContent[] = '<span class="glyphicon glyphicon-download"></span>';
                } else {
                    $linkContent[] = '<span class="glyphicon glyphicon-info-sign"></span>';
                }
                break;
            case 'button':
                $linkContent[] = '<span class="glyphicon glyphicon-info"></span> ' . $text;
                $classes[]     = "btn btn-primary";
                break;
            case 'text':
                $linkContent[] = $text;
                break;
            default:
                $linkContent[] = $affiliation;
                break;
        }

        $uri = '<a href="%s" title="%s" class="%s">%s</a>';

        return sprintf(
            $uri,
            $serverUrl->__invoke() . $url($router, $params),
            $text,
            implode($classes),
            implode($linkContent)
        );
    }
}
