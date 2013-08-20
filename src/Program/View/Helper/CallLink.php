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
class CallLink extends AbstractHelper
{

    /**
     * @param  \Program\Entity\Program $call
     * @param                          $action
     * @param                          $show
     *
     * @return string
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function __invoke(Entity\Call $call = null, $action = 'view', $show = 'name')
    {
        $translate = $this->view->plugin('translate');
        $url       = $this->view->plugin('url');
        $serverUrl = $this->view->plugin('serverUrl');
        $isAllowed = $this->view->plugin('isAllowed');

        switch ($action) {
            case 'new':
                $router = 'zfcadmin/program-manager/new';
                $text   = sprintf($translate("txt-new-area"));
                $call   = new Entity\Program();
                break;
            case 'edit':
                $router = 'zfcadmin/program-manager/edit';
                $text   = sprintf($translate("txt-edit-program-%s"), $call);
                break;
            case 'view':
                $router = 'program/call';
                $text   = sprintf($translate("txt-view-program-%s"), $call);
                break;
            default:
                throw new \Exception(sprintf("%s is an incorrect action for %s", $action, __CLASS__));
        }

        if (is_null($call)) {
            throw new \RuntimeException(
                sprintf(
                    "Area needs to be an instance of %s, %s given in %s",
                    "Program\Entity\Program",
                    get_class($call),
                    __CLASS__
                )
            );
        }

        $params = array(
            'id'     => $call->getId(),
            'entity' => 'program'
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
