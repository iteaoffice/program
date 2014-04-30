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

use Program\Entity;
use Zend\View\Helper\AbstractHelper;

/**
 * Create a link to an program
 *
 * @category    Program
 * @package     View
 * @subpackage  Helper
 */
class TechnologyLink extends AbstractHelper
{
    /**
     * @param \Program\Entity\Technology $technology
     * @param                            $action
     * @param                            $show
     *
     * @return string
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function __invoke(Entity\Technology $technology = null, $action = 'view', $show = 'name')
    {
        $translate = $this->view->plugin('translate');
        $url       = $this->view->plugin('url');
        $serverUrl = $this->view->plugin('serverUrl');
        $isAllowed = $this->view->plugin('isAllowed');

        switch ($action) {
            case 'new':
                $router     = 'zfcadmin/program-manager/new';
                $text       = sprintf($translate("txt-new-technology"));
                $technology = new Entity\Technology();
                break;
            case 'edit':
                $router = 'zfcadmin/program-manager/edit';
                $text   = sprintf($translate("txt-edit-technology-%s"), $technology);
                break;
            case 'view':
                $router = 'program/view';
                $text   = sprintf($translate("txt-view-technology-%s"), $technology);
                break;
            case 'list':
                $router     = 'program/list';
                $text       = sprintf($translate("txt-list-technologys"));
                $technology = new Entity\Technology();
                break;
            default:
                throw new \Exception(sprintf("%s is an incorrect action for %s", $action, __CLASS__));
        }

        $params = array(
            'id'     => $technology->getId(),
            'entity' => 'technology'
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
                $linkContent[] = $technology;
                break;
            default:
                $linkContent[] = $technology;
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
