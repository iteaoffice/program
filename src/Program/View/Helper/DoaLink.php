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

use Organisation\Entity\Organisation;
use Program\Entity\Program;

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
     * @param Entity\Doa   $doa
     * @param string       $action
     * @param string       $show
     * @param Organisation $organisation
     * @param Program      $program
     *
     * @return string
     * @throws \Exception
     */
    public function __invoke(
        Entity\Doa $doa = null,
        $action = 'view',
        $show = 'name',
        Organisation $organisation = null,
        Program $program = null
    )
    {
        $translate = $this->view->plugin('translate');
        $url       = $this->view->plugin('url');
        $serverUrl = $this->view->plugin('serverUrl');
        $isAllowed = $this->view->plugin('isAllowed');

        $auth      = $this->view->getHelperPluginManager()->getServiceLocator()->get('BjyAuthorize\Service\Authorize');
        $assertion = $this->view->getHelperPluginManager()->getServiceLocator()->get('program_acl_assertion_doa');

        if (!is_null($doa) && !$auth->getAcl()->hasResource($doa)) {
            $auth->getAcl()->addResource($doa);
            $auth->getAcl()->allow(array(), $doa, array(), $assertion);
        }

        if (!is_null($doa) && !$isAllowed($doa, $action)) {
            return $action . ' is not possible for ' . $doa;
        }

        $params = array(
            'id'     => (!is_null($doa) ? $doa->getId() : null),
            'entity' => 'doa'
        );


        switch ($action) {
            case 'upload':
                $router = 'program/doa/upload';
                $text   = sprintf($translate("txt-upload-doa-for-organisation-%s-in-program-%s-link-title"),
                    $organisation->getOrganisation(),
                    $program->getProgram()
                );
                break;
            case 'render':
                $router = 'program/doa/render';
                /**
                 * The $doa can be null, we then use the $organisation and $program to produce the link
                 */
                $renderText = "txt-render-doa-for-organisation-%s-in-program-%s-link-title";
                if (is_null($doa)) {
                    $text = sprintf($translate($renderText),
                        $organisation->getOrganisation(),
                        $program->getProgram()
                    );

                    $params['organisation-id'] = $organisation->getId();
                    $params['program-id']      = $program->getId();
                } else {
                    $text = sprintf($translate($renderText),
                        $doa->getOrganisation(),
                        $doa->getProgram()
                    );

                    $params['organisation-id'] = $doa->getOrganisation()->getId();
                    $params['program-id']      = $doa->getProgram()->getId();
                }


                break;
            case 'replace':
                $router = 'program/doa/replace';
                $text   = sprintf($translate("txt-replace-doa-for-organisation-%s-in-program-%s-link-title"),
                    $doa->getOrganisation(),
                    $doa->getProgram()
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
                $linkContent[] = '<span class="glyphicon glyphicon-info"></span> ' . $text;
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
