<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Program
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/program for the canonical source repository
 */

declare(strict_types=1);

namespace Program\Controller;

use General\Entity\Country;
use General\Service\GeneralService;
use Program\Entity\Call\Country as CallCountry;
use Program\Service\CallService;
use Program\Service\FormService;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Zend\View\Model\ViewModel;

/**
 * Class CallCountryManagerController
 *
 * @package Program\Controller
 * @method FlashMessenger flashMessenger()
 */
final class CallCountryManagerController extends AbstractActionController
{
    /**
     * @var CallService
     */
    protected $callService;
    /**
     * @var GeneralService
     */
    protected $generalService;
    /**
     * @var FormService
     */
    protected $formService;
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * CallCountryManagerController constructor.
     *
     * @param CallService         $callService
     * @param GeneralService      $generalService
     * @param FormService         $formService
     * @param TranslatorInterface $translator
     */
    public function __construct(
        CallService $callService,
        GeneralService $generalService,
        FormService $formService,
        TranslatorInterface $translator
    ) {
        $this->callService = $callService;
        $this->generalService = $generalService;
        $this->formService = $formService;
        $this->translator = $translator;
    }


    /**
     * @return ViewModel
     */
    public function viewAction(): ViewModel
    {
        /**
         * @var $callCountry CallCountry
         */
        $callCountry = $this->callService->find(CallCountry::class, (int)$this->params('id'));

        if (null === $callCountry) {
            return $this->notFoundAction();
        }

        return new ViewModel(
            [
                'callCountry' => $callCountry,
            ]
        );
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function editAction()
    {
        /**
         * @var $callCountry CallCountry
         */
        $callCountry = $this->callService->find(CallCountry::class, (int)$this->params('id'));

        if (null === $callCountry) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();
        $form = $this->formService->prepare($callCountry, $data);
        $country = $callCountry->getCountry();

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/call/country/view',
                    [
                        'id' => $callCountry->getId(),
                    ]
                );
            }

            if (isset($data['delete'])) {
                $this->callService->delete($callCountry);
                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(
                        sprintf(
                            $this->translator->translate(
                                "txt-call-country-information-%s-has-successfully-been-removed"
                            ),
                            $country
                        )
                    );

                return $this->redirect()->toRoute(
                    'zfcadmin/call/view',
                    [
                        'id' => $callCountry->getCall()->getId(),
                    ]
                );
            }


            if ($form->isValid()) {
                /**
                 * @var $entity CallCountry
                 */
                $entity = $form->getData();

                $this->callService->save($entity);
                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(
                        sprintf(
                            $this->translator->translate(
                                "txt-call-country-information-%s-has-successfully-been-updated"
                            ),
                            $callCountry->getCountry()
                        )
                    );

                return $this->redirect()->toRoute(
                    'zfcadmin/call/country/view',
                    [
                        'id' => $entity->getId(),
                    ]
                );
            }
        }
        $form->setAttribute('role', 'form');
        $form->setAttribute('class', 'form-horizontal');

        return new ViewModel(['form' => $form, 'callCountry' => $callCountry]);
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function newAction()
    {
        /**
         * Find the corresponding call and country and process
         */
        $call = $this->callService->findCallById((int)$this->params('call'));
        /** @var Country $country */
        $country = $this->generalService->find(Country::class, (int)$this->params('country'));

        if (null === $call || null === $country) {
            return $this->notFoundAction();
        }

        /**
         * @var $country CallCountry
         */
        $callCountry = new CallCountry();
        $callCountry->setCall($call);
        $callCountry->setCountry($country);

        $data = $this->getRequest()->getPost()->toArray();

        $form = $this->formService->prepare($callCountry, $data);
        $form->remove('delete');

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/call/country/view',
                    [
                        'id' => $callCountry->getId(),
                    ]
                );
            }

            if ($form->isValid()) {
                /**
                 * @var $callCountry CallCountry
                 */
                $callCountry = $form->getData();
                $callCountry->setCall($call);
                $callCountry->setCountry($country);

                $this->callService->save($callCountry);
                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(
                        sprintf(
                            $this->translator->translate(
                                "txt-call-country-information-%s-has-successfully-been-updated"
                            ),
                            $country
                        )
                    );

                return $this->redirect()->toRoute(
                    'zfcadmin/call/country/view',
                    [
                        'id' => $callCountry->getId(),
                    ]
                );
            }
        }

        return new ViewModel(['form' => $form, 'callCountry' => $callCountry]);
    }
}
