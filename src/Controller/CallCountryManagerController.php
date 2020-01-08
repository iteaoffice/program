<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Program
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
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
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\View\Model\ViewModel;

/**
 * @package Program\Controller
 * @method FlashMessenger flashMessenger()
 */
final class CallCountryManagerController extends AbstractActionController
{
    private CallService $callService;
    private GeneralService $generalService;
    private FormService $formService;
    private TranslatorInterface $translator;

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
                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate(
                            'txt-call-country-information-%s-has-successfully-been-removed'
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
                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate(
                            'txt-call-country-information-%s-has-successfully-been-updated'
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

        return new ViewModel(['form' => $form, 'callCountry' => $callCountry]);
    }

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
                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate(
                            'txt-call-country-information-%s-has-successfully-been-updated'
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
