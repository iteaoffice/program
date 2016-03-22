<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * PHP Version 5
 *
 * @category    Program
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   2004-2016 ITEA Office
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/program for the canonical source repository
 */

namespace Program\Controller;

use Program\Entity\Call\Country as CallCountry;
use Zend\View\Model\ViewModel;

/**
 * ITEA Office copyright message placeholder.
 *
 * @category  Program
 *
 * @author    Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */
class CallCountryManagerController extends ProgramAbstractController
{
    /**
     * @return ViewModel
     */
    public function viewAction()
    {
        /**
         * @var $callCountry CallCountry
         */
        $callCountry = $this->getCallService()->findEntityById('Call\Country', (int)$this->params('id'));

        if (is_null($callCountry)) {
            return $this->notFoundAction();
        }

        return new ViewModel([
            'callCountry' => $callCountry,
        ]);
    }

    /**
     * @return ViewModel
     */
    public function editAction()
    {
        /**
         * @var $callCountry CallCountry
         */
        $callCountry = $this->getCallService()->findEntityById('Call\Country', (int)$this->params('id'));


        if (is_null($callCountry)) {
            return $this->notFoundAction();
        }

        $data = array_merge($this->getRequest()->getPost()->toArray(), $this->getRequest()->getFiles()->toArray());
        $form = $this->getFormService()->prepare($callCountry->get('entity_name'), $callCountry, $data);
        $country = $callCountry->getCountry();

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/call/country/view', [
                    'id' => $callCountry->getId(),
                ]);
            }

            if (isset($data['delete'])) {
                $this->getCallService()->removeEntity($callCountry);
                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(sprintf(
                        $this->translate("txt-call-country-information-%s-has-successfully-been-removed"),
                        $country
                    ));

                return $this->redirect()->toRoute('zfcadmin/call/view', [
                    'id' => $callCountry->getCall()->getId(),
                ]);
            }


            if ($form->isValid()) {
                /**
                 * @var $entity CallCountry
                 */
                $entity = $form->getData();

                $this->getCallService()->updateEntity($entity);
                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(sprintf(
                        $this->translate("txt-call-country-information-%s-has-successfully-been-updated"),
                        $callCountry->getCountry()
                    ));

                return $this->redirect()->toRoute('zfcadmin/call/country/view', [
                    'id' => $entity->getId(),
                ]);
            }
        }
        $form->setAttribute('role', 'form');
        $form->setAttribute('class', 'form-horizontal');

        return new ViewModel(['form' => $form, 'callCountry' => $callCountry]);
    }

    /**
     * @return ViewModel
     */
    public function newAction()
    {
        /**
         * Find the corresponding call and country and process
         */
        $callService = $this->getCallService()->setCallId($this->params('call'));
        /** @var \General\Entity\Country $country */
        $country = $this->getGeneralService()->findEntityById('country', $this->params('country'));

        if ($callService->isEmpty() || is_null($country)) {
            return $this->notFoundAction();
        }

        /**
         * @var $country CallCountry
         */
        $callCountry = new CallCountry();
        $callCountry->setCall($callService->getCall());
        $callCountry->setCountry($country);

        $data = array_merge($this->getRequest()->getPost()->toArray(), $this->getRequest()->getFiles()->toArray());

        $form = $this->getFormService()->prepare($callCountry->get('entity_name'), $callCountry, $data);

        $form->remove('delete');

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/call/country/view', [
                    'id' => $callCountry->getId(),
                ]);
            }

            if ($form->isValid()) {
                /**
                 * @var $callCountry CallCountry
                 */
                $callCountry = $form->getData();
                $callCountry->setCall($callService->getCall());
                $callCountry->setCountry($country);

                $this->getCallService()->updateEntity($callCountry);
                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(sprintf(
                        $this->translate("txt-call-country-information-%s-has-successfully-been-updated"),
                        $country
                    ));

                return $this->redirect()->toRoute('zfcadmin/call/country/view', [
                    'id' => $callCountry->getId(),
                ]);
            }
        }

        $form->setAttribute('role', 'form');
        $form->setAttribute('class', 'form-horizontal');

        return new ViewModel(['form' => $form, 'callCountry' => $callCountry]);
    }
}
