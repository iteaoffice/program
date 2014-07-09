<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Program
 * @package     Navigation
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Program\Navigation\Service;

/**
 * Factory for the Community admin navigation
 *
 * @package    Application
 * @subpackage Navigation\Service
 */
class NdaNavigationService extends NavigationServiceAbstract
{
    /**
     * Add the dedicated pages to the navigation
     */
    public function update()
    {
        if (!is_null($this->getRouteMatch()) &&
            strtolower($this->getRouteMatch()->getParam('namespace')) === 'program'
        ) {
            if (strpos($this->getRouteMatch()->getMatchedRouteName(), 'community') !== false) {
                //updateCommunityNavigation
            }
            $this->updatePublicNavigation();
        }
    }

    /**
     * @return bool
     */
    public function updatePublicNavigation()
    {
        $publicNavigation = $this->getNavigation();
        switch ($this->getRouteMatch()->getMatchedRouteName()) {
            case 'program/nda/upload':
                if (!is_null($callId = $this->getRouteMatch()->getParam('id'))) {
                    $call = $this->getCallService()->setCallId($callId)->getCall();
                    $publicNavigation->addPage(
                        array(
                            'label'  => $this->translate("txt-home"),
                            'route'  => 'home',
                            'active' => true,
                            'router' => $this->getRouter(),
                            'pages'  => array(
                                array(
                                    'label'  => $this->translate("txt-account-information"),
                                    'route'  => 'contact/profile',
                                    'router' => $this->getRouter(),
                                    'pages'  => array(
                                        array(
                                            'label'  => sprintf(
                                                $this->translate("txt-upload-nda-for-call-%s"),
                                                $call
                                            ),
                                            'route'  => 'program/nda/upload',
                                            'active' => true,
                                            'router' => $this->getRouter(),
                                            'params' => array(
                                                'id' => $this->routeMatch->getParam('call-id')
                                            )
                                        )
                                    )
                                )
                            )
                        )
                    );
                } else {
                    $publicNavigation->addPage(
                        array(
                            'label'  => $this->translate("txt-home"),
                            'route'  => 'home',
                            'active' => true,
                            'router' => $this->getRouter(),
                            'pages'  => array(
                                array(
                                    'label'  => $this->translate("txt-account-information"),
                                    'route'  => 'contact/profile',
                                    'router' => $this->getRouter(),
                                    'pages'  => array(
                                        array(
                                            'label'  => $this->translate("txt-upload-nda"),
                                            'route'  => $this->getRouteMatch()->getMatchedRouteName(),
                                            'active' => true,
                                            'router' => $this->getRouter(),
                                        )
                                    )
                                )
                            )
                        )
                    );
                }
                break;
            case 'program/nda/replace':
                $nda = $this->getProgramService()->findEntityById('Nda', $this->getRouteMatch()->getParam('id'));
                $publicNavigation->addPage(
                    array(
                        'label'  => $this->translate("txt-home"),
                        'route'  => 'home',
                        'active' => true,
                        'router' => $this->getRouter(),
                        'pages'  => array(
                            array(
                                'label'  => $this->translate("txt-account-information"),
                                'route'  => 'contact/profile',
                                'router' => $this->getRouter(),
                                'pages'  => array(
                                    array(
                                        'label'  => sprintf(
                                            $this->translate("txt-replace-nda-%s"),
                                            $nda
                                        ),
                                        'route'  => $this->getRouteMatch()->getMatchedRouteName(),
                                        'active' => true,
                                        'router' => $this->getRouter(),
                                        'params' => array(
                                            'id' => $this->getRouteMatch()->getParam('id')
                                        )
                                    )
                                )
                            )
                        )
                    )
                );
                break;
            case 'program/nda/view':
                $nda = $this->getProgramService()->findEntityById('Nda', $this->getRouteMatch()->getParam('id'));
                $publicNavigation->addPage(
                    array(
                        'label'  => $this->translate("txt-home"),
                        'route'  => 'home',
                        'active' => true,
                        'router' => $this->getRouter(),
                        'pages'  => array(
                            array(
                                'label'  => $this->translate("txt-account-information"),
                                'route'  => 'contact/profile',
                                'router' => $this->getRouter(),
                                'pages'  => array(
                                    array(
                                        'label'  => sprintf(
                                            $this->translate("txt-view-nda-%s"),
                                            $nda
                                        ),
                                        'route'  => $this->getRouteMatch()->getMatchedRouteName(),
                                        'active' => true,
                                        'router' => $this->getRouter(),
                                        'params' => array(
                                            'id' => $this->getRouteMatch()->getParam('id')
                                        )
                                    )
                                )
                            )
                        )
                    )
                );
                break;
        }

        return true;
    }
}