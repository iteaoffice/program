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
 * Create a link to an nda
 *
 * @category    Program
 * @package     View
 * @subpackage  Helper
 */
class NdaLink extends AbstractHelper
{

    /**
     * @param Entity\Nda       $nda
     * @param string           $action
     * @param string           $show
     * @param Entity\Call\Call $call
     *
     * @return string
     * @throws \Exception
     */
    public function __invoke(Entity\Nda $nda = null, $action = 'view', $show = 'name', Entity\Call\Call $call = null)
    {
        $translate = $this->view->plugin('translate');
        $url       = $this->view->plugin('url');
        $serverUrl = $this->view->plugin('serverUrl');

        switch ($action) {

            case 'view-call':

                if (is_null($call)) {
                    throw new \Exception(sprintf("A call is needed to create a call DNA in %s", __CLASS__));
                }

                $router = 'program/view-nda-call';
                $nda    = new Entity\Nda();
                $text   = sprintf($translate("txt-create-nda-for-call-%s"), $call);
                break;

            case 'render-call':

                if (is_null($call)) {
                    throw new \Exception(sprintf("A call is needed to create a call DNA in %s", __CLASS__));
                }

                $router = 'program/render-nda-call';
                $nda    = new Entity\Nda();
                $text   = sprintf($translate("txt-render-nda-for-call-%s"), $call);
                break;
            default:
                throw new \Exception(sprintf("%s is an incorrect action for %s", $action, __CLASS__));
        }

        $params = array(
            'id'     => $nda->getId(),
            'entity' => 'nda',
            'call'   => (!is_null($call) ? $call->getId() : null)
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
            case 'text':
                $linkContent[] = $text;
                break;
            default:
                $linkContent[] = $text;
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
