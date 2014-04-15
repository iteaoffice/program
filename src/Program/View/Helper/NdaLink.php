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

        $isAllowed = $this->view->plugin('isAllowed');

        /**
         * Add the resource on the fly
         */
        if (is_null($nda)) {
            $nda = new Entity\Nda();
        }

        $auth      = $this->view->getHelperPluginManager()->getServiceLocator()->get('BjyAuthorize\Service\Authorize');
        $assertion = $this->view->getHelperPluginManager()->getServiceLocator()->get('program_acl_assertion_nda');

        if (!is_null($nda) && !$auth->getAcl()->hasResource($nda)) {
            $auth->getAcl()->addResource($nda);
            $auth->getAcl()->allow(array(), $nda, array(), $assertion);
        }

        if (!is_null($nda) && !$isAllowed($nda, $action)) {
            return $action . ' is not possible for ' . $nda;
        }

        $params = array(
            'id'     => $nda->getId(),
            'entity' => 'nda',
        );

        switch ($action) {
            case 'view':
                $router = 'program/nda/view';
                if (!is_null($call) || !is_null($nda->getCall())) {
                    $text = sprintf($translate("txt-view-nda-for-call-%s"), $call);
                } else {
                    $text = sprintf($translate("txt-view-nda"));
                }
                break;
            case 'replace':
                $router = 'program/nda/replace';
                $text   = sprintf($translate("txt-replace-nda-%s"), $nda);
                break;
            case 'render':
                $router = 'program/nda/render';
                $text   = sprintf($translate("txt-render-general-nda"));

                /**
                 * Produce special texts for call-dedicated NDA's
                 */
                if (!is_null($nda->getCall())) {
                    $text              = sprintf($translate("txt-render-nda-for-call-%s"), $nda->getCall());
                    $params['call-id'] = $nda->getCall()->getId();
                } elseif (!is_null($call)) {
                    $text              = sprintf($translate("txt-render-nda-for-call-%s"), $call);
                    $params['call-id'] = $call->getId();
                }

                break;
            case 'download':
                $router = 'program/nda/download';
                $text   = sprintf($translate("txt-download-nda-%s"), $nda);
                break;
            default:
                throw new \Exception(sprintf("%s is an incorrect action for %s", $action, __CLASS__));
        }


        $classes     = array();
        $linkContent = array();

        switch ($show) {
            case 'icon':
                if ($action === 'edit') {
                    $linkContent[] = '<span class="glyphicon glyphicon-edit"></span>';
                } elseif ($action === 'download') {
                    $linkContent[] = '<span class="glyphicon glyphicon-download"></span>';
                } elseif ($action === 'replace') {
                    $linkContent[] = '<span class="glyphicon glyphicon-repeat"></span>';
                } else {
                    $linkContent[] = '<span class="glyphicon glyphicon-info-sign"></span>';
                }
                break;
            case 'button':
                $linkContent[] = $text;
                if ($action === 'render' || $action === 'render-call') {
                    $classes[] = "btn btn-info";
                } else {
                    $classes[] = "btn btn-primary";
                }

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
            strip_tags($text),
            implode($classes),
            implode($linkContent)
        );
    }
}
