<?php

/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Program
 * @package     View
 * @subpackage  Helper
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
namespace Program\View\Helper;

use Zend\View\Helper\AbstractHelper;

use Program\Entity;

/**
 * Create a link to an domain
 *
 * @category    Program
 * @package     View
 * @subpackage  Helper
 */
class DomainLink extends AbstractHelper
{

    /**
     * @param \Program\Entity\Domain  $domain
     * @param                         $action
     * @param                         $show
     *
     * @return string
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function __invoke(Entity\Domain $domain = null, $action = 'view', $show = 'name')
    {
        $translate = $this->view->plugin('translate');
        $url       = $this->view->plugin('url');
        $serverUrl = $this->view->plugin('serverUrl');
        $isAllowed = $this->view->plugin('isAllowed');

        switch ($action) {
            case 'new':
                $router = 'zfcadmin/program-manager/new';
                $text   = sprintf($translate("txt-new-domain"));
                $domain = new Entity\Domain();
                break;
            case 'edit':
                $router = 'zfcadmin/program-manager/edit';
                $text   = sprintf($translate("txt-edit-domain-%s"), $domain);
                break;
            case 'view':
                $router = 'program/view';
                $text   = sprintf($translate("txt-view-domain-%s"), $domain);
                break;
            case 'list':
                $router = 'program/list';
                $text   = sprintf($translate("txt-list-domains"));
                $domain = new Entity\Domain();
                break;
            default:
                throw new \Exception(sprintf("%s is an incorrect action for %s", $action, __CLASS__));
        }


        $params = array(
            'id'     => $domain->getId(),
            'entity' => 'domain'
        );

        $classes     = array();
        $linkContent = array();

        switch ($show) {
            case 'icon':
                if ($action === 'edit') {
                    $linkContent[] = '<i class="icon-pencil"></i>';
                } elseif ($action === 'delete') {
                    $linkContent[] = '<i class="icon-remove"></i>';
                } else {
                    $linkContent[] = '<i class="icon-info-sign"></i>';
                }
                break;
            case 'button':
                $linkContent[] = '<i class="icon-pencil icon-white"></i> ' . $text;
                $classes[]     = "btn btn-primary";
                break;
            case 'name':
                $linkContent[] = $domain;
                break;
            default:
                $linkContent[] = $domain;
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
