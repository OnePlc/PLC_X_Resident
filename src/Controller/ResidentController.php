<?php
/**
 * ResidentController.php - Main Controller
 *
 * Main Controller Resident Module
 *
 * @category Controller
 * @package Resident
 * @author Verein onePlace
 * @copyright (C) 2020  Verein onePlace <admin@1plc.ch>
 * @license https://opensource.org/licenses/BSD-3-Clause
 * @version 1.0.0
 * @since 1.0.0
 */

declare(strict_types=1);

namespace OnePlace\Resident\Controller;

use Application\Controller\CoreController;
use Application\Model\CoreEntityModel;
use OnePlace\Resident\Model\Resident;
use OnePlace\Resident\Model\ResidentTable;
use Laminas\View\Model\ViewModel;
use Laminas\Db\Adapter\AdapterInterface;

class ResidentController extends CoreController {
    /**
     * Resident Table Object
     *
     * @since 1.0.0
     */
    private $oTableGateway;

    /**
     * ResidentController constructor.
     *
     * @param AdapterInterface $oDbAdapter
     * @param ResidentTable $oTableGateway
     * @since 1.0.0
     */
    public function __construct(AdapterInterface $oDbAdapter,ResidentTable $oTableGateway,$oServiceManager) {
        $this->oTableGateway = $oTableGateway;
        $this->sSingleForm = 'resident-single';
        parent::__construct($oDbAdapter,$oTableGateway,$oServiceManager);

        if($oTableGateway) {
            # Attach TableGateway to Entity Models
            if(!isset(CoreEntityModel::$aEntityTables[$this->sSingleForm])) {
                CoreEntityModel::$aEntityTables[$this->sSingleForm] = $oTableGateway;
            }
        }
    }

    /**
     * Resident Index
     *
     * @since 1.0.0
     * @return ViewModel - View Object with Data from Controller
     */
    public function indexAction() {
        # Set Layout based on users theme
        $this->setThemeBasedLayout('resident');

        # Add Buttons for breadcrumb
        $this->setViewButtons('resident-index');

        # Set Table Rows for Index
        $this->setIndexColumns('resident-index');

        # Get Paginator
        $oPaginator = $this->oTableGateway->fetchAll(true);
        $iPage = (int) $this->params()->fromQuery('page', 1);
        $iPage = ($iPage < 1) ? 1 : $iPage;
        $oPaginator->setCurrentPageNumber($iPage);
        $oPaginator->setItemCountPerPage(3);

        # Log Performance in DB
        $aMeasureEnd = getrusage();
        $this->logPerfomance('resident-index',$this->rutime($aMeasureEnd,CoreController::$aPerfomanceLogStart,"utime"),$this->rutime($aMeasureEnd,CoreController::$aPerfomanceLogStart,"stime"));

        return new ViewModel([
            'sTableName'=>'resident-index',
            'aItems'=>$oPaginator,
        ]);
    }

    /**
     * Resident Add Form
     *
     * @since 1.0.0
     * @return ViewModel - View Object with Data from Controller
     */
    public function addAction() {
        # Set Layout based on users theme
        $this->setThemeBasedLayout('resident');

        # Get Request to decide wether to save or display form
        $oRequest = $this->getRequest();

        # Display Add Form
        if(!$oRequest->isPost()) {
            # Add Buttons for breadcrumb
            $this->setViewButtons('resident-single');

            # Load Tabs for View Form
            $this->setViewTabs($this->sSingleForm);

            # Load Fields for View Form
            $this->setFormFields($this->sSingleForm);

            # Log Performance in DB
            $aMeasureEnd = getrusage();
            $this->logPerfomance('resident-add',$this->rutime($aMeasureEnd,CoreController::$aPerfomanceLogStart,"utime"),$this->rutime($aMeasureEnd,CoreController::$aPerfomanceLogStart,"stime"));

            return new ViewModel([
                'sFormName' => $this->sSingleForm,
            ]);
        }

        # Get and validate Form Data
        $aFormData = $this->parseFormData($_REQUEST);

        # Save Add Form
        $oResident = new Resident($this->oDbAdapter);
        $oResident->exchangeArray($aFormData);
        $iResidentID = $this->oTableGateway->saveSingle($oResident);
        $oResident = $this->oTableGateway->getSingle($iResidentID);

        # Save Multiselect
        $this->updateMultiSelectFields($_REQUEST,$oResident,'resident-single');

        # Log Performance in DB
        $aMeasureEnd = getrusage();
        $this->logPerfomance('resident-save',$this->rutime($aMeasureEnd,CoreController::$aPerfomanceLogStart,"utime"),$this->rutime($aMeasureEnd,CoreController::$aPerfomanceLogStart,"stime"));

        # Display Success Message and View New Resident
        $this->flashMessenger()->addSuccessMessage('Resident successfully created');
        return $this->redirect()->toRoute('resident',['action'=>'view','id'=>$iResidentID]);
    }

    /**
     * Resident Edit Form
     *
     * @since 1.0.0
     * @return ViewModel - View Object with Data from Controller
     */
    public function editAction() {
        # Set Layout based on users theme
        $this->setThemeBasedLayout('resident');

        # Get Request to decide wether to save or display form
        $oRequest = $this->getRequest();

        # Display Edit Form
        if(!$oRequest->isPost()) {

            # Get Resident ID from URL
            $iResidentID = $this->params()->fromRoute('id', 0);

            # Try to get Resident
            try {
                $oResident = $this->oTableGateway->getSingle($iResidentID);
            } catch (\RuntimeException $e) {
                echo 'Resident Not found';
                return false;
            }

            # Attach Resident Entity to Layout
            $this->setViewEntity($oResident);

            # Add Buttons for breadcrumb
            $this->setViewButtons('resident-single');

            # Load Tabs for View Form
            $this->setViewTabs($this->sSingleForm);

            # Load Fields for View Form
            $this->setFormFields($this->sSingleForm);

            # Log Performance in DB
            $aMeasureEnd = getrusage();
            $this->logPerfomance('resident-edit',$this->rutime($aMeasureEnd,CoreController::$aPerfomanceLogStart,"utime"),$this->rutime($aMeasureEnd,CoreController::$aPerfomanceLogStart,"stime"));

            return new ViewModel([
                'sFormName' => $this->sSingleForm,
                'oResident' => $oResident,
            ]);
        }

        $iResidentID = $oRequest->getPost('Item_ID');
        $oResident = $this->oTableGateway->getSingle($iResidentID);

        # Update Resident with Form Data
        $oResident = $this->attachFormData($_REQUEST,$oResident);

        # Save Resident
        $iResidentID = $this->oTableGateway->saveSingle($oResident);

        $this->layout('layout/json');

        # Parse Form Data
        $aFormData = $this->parseFormData($_REQUEST);

        # Save Multiselect
        $this->updateMultiSelectFields($aFormData,$oResident,'resident-single');

        # Log Performance in DB
        $aMeasureEnd = getrusage();
        $this->logPerfomance('resident-save',$this->rutime($aMeasureEnd,CoreController::$aPerfomanceLogStart,"utime"),$this->rutime($aMeasureEnd,CoreController::$aPerfomanceLogStart,"stime"));

        # Display Success Message and View New User
        $this->flashMessenger()->addSuccessMessage('Resident successfully saved');
        return $this->redirect()->toRoute('resident',['action'=>'view','id'=>$iResidentID]);
    }

    /**
     * Resident View Form
     *
     * @since 1.0.0
     * @return ViewModel - View Object with Data from Controller
     */
    public function viewAction() {
        # Set Layout based on users theme
        $this->setThemeBasedLayout('resident');

        # Get Resident ID from URL
        $iResidentID = $this->params()->fromRoute('id', 0);

        # Try to get Resident
        try {
            $oResident = $this->oTableGateway->getSingle($iResidentID);
        } catch (\RuntimeException $e) {
            echo 'Resident Not found';
            return false;
        }

        # Attach Resident Entity to Layout
        $this->setViewEntity($oResident);

        # Add Buttons for breadcrumb
        $this->setViewButtons('resident-view');

        # Load Tabs for View Form
        $this->setViewTabs($this->sSingleForm);

        # Load Fields for View Form
        $this->setFormFields($this->sSingleForm);

        # Log Performance in DB
        $aMeasureEnd = getrusage();
        $this->logPerfomance('resident-view',$this->rutime($aMeasureEnd,CoreController::$aPerfomanceLogStart,"utime"),$this->rutime($aMeasureEnd,CoreController::$aPerfomanceLogStart,"stime"));

        return new ViewModel([
            'sFormName'=>$this->sSingleForm,
            'oResident'=>$oResident,
        ]);
    }
}
