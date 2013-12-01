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

        $routeMatch = $this->view->getHelperPluginManager()->getServiceLocator()
            ->get('application')
            ->getMvcEvent()
            ->getRouteMatch();

        $params = array(
            'id'     => $call->getId(),
            'entity' => 'call\call'
        );


        $isAllowed = $this->view->plugin('isAllowed');

        switch ($action) {
            case 'new':
                $router = 'zfcadmin/call-manager/new';
                $text   = sprintf($translate("txt-new-area"));
                $call   = new Entity\Program();
                break;
            case 'edit':
                $router = 'zfcadmin/call-manager/edit';
                $text   = sprintf($translate("txt-edit-call-%s"), $call);
                break;
            case 'view-list':

                /**
                 * For a list in the front-end simply use the MatchedRouteName
                 */
                $router           = $routeMatch->getMatchedRouteName();
                $params['docRef'] = $routeMatch->getParam('docRef');
                $params['call']   = $call->getId();

                $text = sprintf($translate("txt-view-call-%s"), $call);
                break;
            case 'view':

                /**
                 * For a list in the front-end simply use the MatchedRouteName
                 */
                $router         = 'route-' . $call->get("underscore_full_entity_name");
                $params['call'] = $call->getId();

                $text = sprintf($translate("txt-view-call-%s"), $call);
                break;
            case 'list':
                $router = 'program/list';
                $text   = sprintf($translate("txt-list-calls"));
                $call   = new Entity\Call\Call();
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
