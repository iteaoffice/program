<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * PHP Version 5
 *
 * @category    Affiliation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   2004-2015 ITEA Office
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        https://itea3.org
 */

namespace Program\Controller;

use Program\Entity\Nda;
use Program\Entity\NdaObject;
use Program\Form\NdaApproval;
use Zend\Validator\File\FilesSize;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Affiliation controller.
 *
 * @category   Affiliation
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2015 ITEA Office
 * @license    https://itea3.org/license.txt proprietary
 *
 * @link       https://itea3.org
 */
class NdaManagerController extends ProgramAbstractController
{
    /**
     * @return ViewModel
     */
    public function approvalAction()
    {
        $nda = $this->getCallService()->findNotApprovedNda();
        $form = new NdaApproval($nda, $this->getContactService());

        return new ViewModel([
            'nda'  => $nda,
            'form' => $form,
        ]);
    }

    /**
     * @return \Zend\View\Model\ViewModel
     */
    public function viewAction()
    {
        $nda = $this->callService->findEntityById(Nda::class, $this->params('id'));
        if (is_null($nda)) {
            return $this->notFoundAction();
        }

        return new ViewModel(['nda' => $nda]);
    }

    /**
     * @return \Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        $nda = $this->getCallService()->findEntityById(Nda::class, $this->params('id'));

        if (is_null($nda)) {
            return $this->notFoundAction();
        }

        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );

        $form = $this->getFormService()->prepare(Nda::class, $nda, $data);

        $form->get($nda->get('underscore_entity_name'))->get('contact')->setValueOptions([
            $nda->getContact()->getId() => $nda->getContact()->getFormName(),
        ]);
        $form->get($nda->get('underscore_entity_name'))->get('programCall')->setValue($nda->getCall());

        //Get contacts in an organisation
        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/nda/view', ['id' => $nda->getId()]);
            }

            if (isset($data['delete'])) {
                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(sprintf(
                        $this->translate("txt-nda-for-contact-%s-has-been-removed"),
                        $nda->getContact()->getDisplayName()
                    ));

                $this->getCallService()->removeEntity($nda);

                return $this->redirect()->toRoute('zfcadmin/nda/approval');
            }

            if ($form->isValid()) {
                /**
                 * @var Nda $nda
                 */
                $nda = $form->getData();


                $fileData = $this->params()->fromFiles();

                if ($fileData['nda']['file']['error'] === 0) {
                    /*
                     * Replace the content of the object
                     */
                    if (!$nda->getObject()->isEmpty()) {
                        $nda->getObject()->first()->setObject(file_get_contents($fileData['nda']['file']['tmp_name']));
                    } else {
                        $ndaObject = new NdaObject();
                        $ndaObject->setObject(file_get_contents($fileData['nda']['file']['tmp_name']));
                        $ndaObject->setNda($nda);
                        $this->getCallService()->newEntity($ndaObject);
                    }

                    //Create a article object element
                    $fileSizeValidator = new FilesSize(PHP_INT_MAX);
                    $fileSizeValidator->isValid($fileData['nda']['file']);
                    $nda->setSize($fileSizeValidator->size);
                    $nda->setContentType($this->getGeneralService()
                        ->findContentTypeByContentTypeName($fileData['nda']['file']['type']));
                }

                /*
                 * The programme call needs to have a dedicated treatment
                 */
                if (!empty($data['nda']['programCall'])) {
                    $nda->setCall([$this->getCallService()->findCallById($data['nda']['programCall'])]);
                } else {
                    $nda->setCall([]);
                }

                $this->getCallService()->updateEntity($nda);

                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(sprintf(
                        _("txt-nda-for-contact-%s-has-been-updated"),
                        $nda->getContact()->getDisplayName()
                    ));

                return $this->redirect()->toRoute('zfcadmin/nda/view', ['id' => $nda->getId()]);
            }
        }

        return new ViewModel([
            'nda'  => $nda,
            'form' => $form,
        ]);
    }

    /**
     * Dedicated action to approve NDAs via an AJAX call.
     *
     * @return JsonModel
     */
    public function approveAction()
    {
        $nda = $this->params()->fromPost('nda');
        $dateSigned = $this->params()->fromPost('dateSigned');

        if (empty($dateSigned)) {
            return new JsonModel([
                'result' => 'error',
                'error'  => $this->translate("txt-date-signed-is-empty"),
            ]);
        }

        if (!\DateTime::createFromFormat('Y-h-d', $dateSigned)) {
            return new JsonModel([
                'result' => 'error',
                'error'  => $this->translate("txt-incorrect-date-format-should-be-yyyy-mm-dd"),
            ]);
        }

        /*
         * @var Nda
         */
        $nda = $this->getCallService()->findEntityById(Nda::class, $nda);
        $nda->setDateSigned(\DateTime::createFromFormat('Y-h-d', $dateSigned));
        $nda->setDateApproved(new \DateTime());
        $this->getCallService()->updateEntity($nda);

        //Flush the rights of the NDA guy
        $this->getAdminService()->flushPermitsByContact($nda->getContact());

        //Update the
        return new JsonModel([
            'result' => 'success',
        ]);
    }
}
