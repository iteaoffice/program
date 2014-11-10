<?php
/**
 * ITEA Office copyright message placeholder
 *
 * PHP Version 5
 *
 * @category    Affiliation
 * @package     Controller
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   2004-2014 ITEA Office
 * @license     http://debranova.org/license.txt proprietary
 * @link        http://debranova.org
 */
namespace Program\Controller;

use Contact\Service\ContactServiceAwareInterface;
use General\Service\GeneralServiceAwareInterface;
use Program\Entity\Nda;
use Program\Entity\NdaObject;
use Program\Form\NdaApproval;
use Program\Service\CallServiceAwareInterface;
use Zend\Validator\File\FilesSize;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Affiliation controller
 *
 * @category   Affiliation
 * @package    Controller
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2014 ITEA Office
 * @license    http://debranova.org/license.txt proprietary
 * @link       http://debranova.org
 */
class NdaManagerController extends ProgramAbstractController implements
    CallServiceAwareInterface,
    GeneralServiceAwareInterface,
    ContactServiceAwareInterface
{
    /**
     * @return ViewModel
     */
    public function approvalAction()
    {
        $nda = $this->getCallService()->findNotApprovedNda();
        $form = new NdaApproval($nda, $this->getContactService());

        return new ViewModel(
            [
                'nda'  => $nda,
                'form' => $form
            ]
        );
    }


    /**
     * @return \Zend\View\Model\ViewModel
     */
    public function viewAction()
    {
        $nda = $this->getNdaService()->setNdaId(
            $this->getEvent()->getRouteMatch()->getParam('id')
        );
        if (is_null($nda)) {
            return $this->notFoundAction();
        }

        return new ViewModel(['nda' => $nda->getNda()]);
    }

    /**
     * @return \Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        $nda = $this->getCallService()->findEntityById(
            'nda',
            $this->getEvent()->getRouteMatch()->getParam('id')
        );


        if (is_null($nda)) {
            return $this->notFoundAction();
        }

        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );

        $form = $this->getFormService()->prepare('nda', $nda, $data);

        //Get contacts in an organisation
        if ($this->getRequest()->isPost() && $form->isValid()) {
            /**
             * @var $nda Nda
             */
            $nda = $form->getData();
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/affiliation-manager/nda/view',
                    ['id' => $nda->getId()]
                );
            }

            if (isset($data['delete'])) {
                $this->flashMessenger()->setNamespace('success')->addMessage(
                    sprintf(
                        _("txt-project-nda-for-organisation-%s-in-project-%s-has-been-removed"),
                        $nda->getAffiliation()->getOrganisation(),
                        $nda->getAffiliation()->getProject()
                    )
                );

                $this->getNdaService()->removeEntity($nda);

                return $this->redirect()->toRoute('zfcadmin/affiliation-manager/nda/list');
            }

            $fileData = $this->params()->fromFiles();

            if ($fileData['nda']['file']['error'] === 0) {
                /**
                 * Replace the content of the object
                 */
                if (!$nda->getObject()->isEmpty()) {
                    $nda->getObject()->first()->setObject(file_get_contents($fileData['nda']['file']['tmp_name']));
                } else {
                    $ndaObject = new NdaObject();
                    $ndaObject->setObject(file_get_contents($fileData['nda']['file']['tmp_name']));
                    $ndaObject->setNda($nda);
                    $this->getNdaService()->newEntity($ndaObject);
                }

                //Create a article object element
                $fileSizeValidator = new FilesSize(PHP_INT_MAX);
                $fileSizeValidator->isValid($fileData['nda']['file']);
                $nda->setSize($fileSizeValidator->size);
                $nda->setContentType(
                    $this->getGeneralService()->findContentTypeByContentTypeName($fileData['nda']['file']['type'])
                );

            }

            $this->getNdaService()->updateEntity($nda);

            $this->flashMessenger()->setNamespace('success')->addMessage(
                sprintf(
                    _("txt-project-nda-for-organisation-%s-in-project-%s-has-been-updated"),
                    $nda->getAffiliation()->getOrganisation(),
                    $nda->getAffiliation()->getProject()
                )
            );

            return $this->redirect()->toRoute(
                'zfcadmin/affiliation-manager/nda/view',
                ['id' => $nda->getId()]
            );

        }

        return new ViewModel(
            [
                'nda'  => $nda,
                'form' => $form
            ]
        );
    }


    /**
     * Dedicated action to approve NDAs via an AJAX call
     *
     * @return JsonModel
     */
    public function approveAction()
    {
        $nda = $this->getEvent()->getRequest()->getPost()->get('nda');
        $dateSigned = $this->getEvent()->getRequest()->getPost()->get('dateSigned');

        if (empty($dateSigned)) {
            return new JsonModel(
                [
                    'result' => 'error',
                    'error'  => _("txt-date-signed-is-empty")
                ]
            );
        }

        if (!\DateTime::createFromFormat('Y-h-d', $dateSigned)) {
            return new JsonModel(
                [
                    'result' => 'error',
                    'error'  => _("txt-incorrect-date-format-should-be-yyyy-mm-dd")
                ]
            );
        }

        /**
         * @var $nda Nda
         */
        $nda = $this->getCallService()->findEntityById('Nda', $nda);
        $nda->setDateSigned(\DateTime::createFromFormat('Y-h-d', $dateSigned));
        $this->getCallService()->updateEntity($nda);

        return new JsonModel(
            [
                'result' => 'success',
            ]
        );

    }
}
