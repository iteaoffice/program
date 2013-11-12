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
 * Create a link to an program
 *
 * @category    Program
 * @package     View
 * @subpackage  Helper
 */
class ProgramLink extends AbstractHelper
{

    /**
     * @param \Program\Entity\Program $program
     * @param                         $action
     * @param                         $show
     *
     * @return string
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function __invoke(Entity\Program $program = null, $action = 'view', $show = 'name')
    {
        $translate = $this->view->plugin('translate');
        $url       = $this->view->plugin('url');
        $serverUrl = $this->view->plugin('serverUrl');
        $isAllowed = $this->view->plugin('isAllowed');

        $routeMatch = $this->view->getHelperPluginManager()->getServiceLocator()
            ->get('application')
            ->getMvcEvent()
            ->getRouteMatch();

        $params = array(
            'id'     => $program->getId(),
            'entity' => 'program'
        );

        switch ($action) {
            case 'new':
                $router  = 'zfcadmin/program-manager/new';
                $text    = sprintf($translate("txt-new-program"));
                $program = new Entity\Program();
                break;
            case 'edit':
                $router = 'zfcadmin/program-manager/edit';
                $text   = sprintf($translate("txt-edit-program-%s"), $program);
                break;
            case 'view':
                $router = 'program/view';
                $text   = sprintf($translate("txt-view-program-%s"), $program);
                break;
            case 'view-list':

                /**
                 * For a list in the front-end simply use the MatchedRouteName
                 */
                $router            = $routeMatch->getMatchedRouteName();
                $params['docRef']  = $routeMatch->getParam('docRef');
                $params['program'] = $program->getId();

                $text = sprintf($translate("txt-view-program-%s"), $program);
                break;
            case 'list':
                $router  = 'program/list';
                $text    = sprintf($translate("txt-list-program"));
                $program = new Entity\Program();
                break;
            default:
                throw new \Exception(sprintf("%s is an incorrect action for %s", $action, __CLASS__));
        }


        $classes     = array();
        $linkContent = array();

        switch ($show) {
            case 'icon':
                if ($action === 'edit') {
                    $linkContent[] = ' < i class="icon-pencil" ></i > ';
                } elseif ($action === 'delete') {
                    $linkContent[] = ' < i class="icon-remove" ></i > ';
                } else {
                    $linkContent[] = '<i class="icon-info-sign" ></i > ';
                }
                break;
            case 'button':
                $linkContent[] = ' < i class="icon-pencil icon-white" ></i > ' . $text;
                $classes[]     = "btn btn-primary";
                break;
            case 'name':
                $linkContent[] = $program;
                break;
            default:
                $linkContent[] = $program;
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
