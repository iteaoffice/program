<?php

/**
 * ITEA Office copyright message placeholder
 *
 * @category    Program
 * @package     View
 * @subpackage  Helper
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Program\View\Helper;

use Zend\View\Helper\AbstractHelper;

use Program\Entity;

/**
 * Create a link to an call
 *
 * @category    Program
 * @package     View
 * @subpackage  Helper
 */
class CallLink extends AbstractHelper
{
    /**
     * @param Entity\Call\Call $call
     * @param string           $action
     * @param string           $show
     *
     * @return string
     * @throws \Exception
     */
    public function __invoke(Entity\Call\Call $call = null, $action = 'view', $show = 'name')
    {
        $translate = $this->view->plugin('translate');
        $url       = $this->view->plugin('url');
        $serverUrl = $this->view->plugin('serverUrl');

        $params = array(
            'id'     => $call->getId(),
            'entity' => 'Call\Call'
        );

        switch ($action) {
            case 'new':
                $router = 'zfcadmin/call-manager/new';
                $text   = sprintf($translate("txt-new-call"));
                $call   = new Entity\Program();
                break;
            case 'edit':
                $router = 'zfcadmin/call-manager/edit';
                $text   = sprintf($translate("txt-edit-call-%s"), $call);
                break;
            case 'view-list':

                /**
                 * For a list in the front-end we use a node (all-projects)
                 * This node should exist...
                 */
                $router           = 'route-content_entity_node';
                $params['call']   = $call->getId();
                $params['docRef'] = 'all-projects';

                $text = sprintf($translate("txt-view-call-%s"), $call);
                break;
            default:
                throw new \Exception(sprintf("%s is an incorrect action for %s", $action, __CLASS__));
        }

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
                $linkContent[] = $call;
                break;
            default:
                $linkContent[] = $call;
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
