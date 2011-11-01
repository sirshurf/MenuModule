<?php
class Menu_IndexController extends Zend_Controller_Action
{

	public function indexAction ()
	{
		$objMenuTable = new Menu_Model_Db_Menu();
		$objMenuSelect = $objMenuTable->select(TRUE);
		$objMenuSelect->where(Menu_Model_Db_Menu::COL_IS_DELETED." = ?",FALSE);
		$arrOptions = array("hiddengrid" => false, "caption" => "");
		$grid = new Ingot_JQuery_JqGrid('menu', new Ingot_JQuery_JqGrid_Adapter_DbTableSelect($objMenuSelect), $arrOptions);
		$grid->setIdCol(Menu_Model_Db_Menu::COL_ID_MENU);
		$grid->setLocalEdit();
		$grid->addColumn(new Ingot_JQuery_JqGrid_Column(Menu_Model_Db_Menu::COL_CODE, array('editable' => true)));
		$grid->addColumn(new Ingot_JQuery_JqGrid_Column(Menu_Model_Db_Menu::COL_LABEL, array('editable' => true)));
		Ingot_JQuery_JqGrid_Column_DoubleColumn::createSelectColumn($grid, 'Parent', array(), FALSE);
		$grid->addColumn(new Ingot_JQuery_JqGrid_Column(Menu_Model_Db_Menu::COL_URI, array('editable' => true)));
		$grid->addColumn(new Ingot_JQuery_JqGrid_Column(Menu_Model_Db_Menu::COL_MODULE, array('editable' => true)));
		$grid->addColumn(new Ingot_JQuery_JqGrid_Column(Menu_Model_Db_Menu::COL_CONTROLLER, array('editable' => true)));
		$grid->addColumn(new Ingot_JQuery_JqGrid_Column(Menu_Model_Db_Menu::COL_ACTION, array('editable' => true)));
		$grid->addColumn(new Ingot_JQuery_JqGrid_Column(Menu_Model_Db_Menu::COL_CSS, array('editable' => true)));
		Ingot_JQuery_JqGrid_Column_DoubleColumn::createSelectColumn($grid, 'Resources', array(), FALSE);
		$grid->addColumn(new Ingot_JQuery_JqGrid_Column(Menu_Model_Db_Menu::COL_PRIVELEGE, array('editable' => true)));
		$grid->addColumn(new Ingot_JQuery_JqGrid_Column(Menu_Model_Db_Menu::COL_ORDER, array('editable' => true)));
		$objPlugin = $grid->getPager();
		$objPlugin->setDefaultAdd();
		$objPlugin->setDefaultEdit();
		$objPlugin->setDefaultDel();
		$grid->setDblClkEdit();
		$grid->registerPlugin(new Ingot_JQuery_JqGrid_Plugin_ToolbarFilter());
		$this->view->grid = $grid->render();
		
		$objForm = new Menu_Form_UploadMenu();
		$this->view->objForm = $objForm;
		
		$arrActions = array();
		$arrActions[] = array('module' => 'menu', 'controller' => 'index', "action" => "savebackup", "name" => 'LBL_SAVE_BKP_FILE');
		$arrActions[] = array('module' => 'menu', 'controller' => 'index', "action" => "loadbackup", "name" => 'LBL_LOAD_BKP_FILE', 'onClick' => '$("#uploader").dialog("open")');
		$this->view->arrActions = $arrActions;
	}

	public function savebackupAction ()
	{
		Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->setNoRender();
		Zend_Controller_Action_HelperBroker::getStaticHelper('layout')->disableLayout();
		$objMenuTable = new Menu_Model_Db_Menu();
		$objMenuSelect = $objMenuTable->select(TRUE);
		$objConfig = new Zend_Config($objMenuTable->fetchAll($objMenuSelect)->toArray());
		$writer = new Zend_Config_Writer_Array(array('config' => $objConfig, 'filename' => APPLICATION_PATH . '/configs/backup/menu.php'));
		try {
			$writer->write();
			Labels_Model_SystemLabels::setJgrowlMessage('LBL_MENU_SAVE_OK');
		} catch (Exception $objEx) {
			Labels_Model_SystemLabels::setJgrowlMessage('LBL_MENU_SAVE_FAILED');
		}
		$strUrl = $this->view->url(array('module' => 'menu', 'controller' => 'index', 'action' => 'index'), null, true);
		$this->_redirect($strUrl);
	}

	public function loadbackupAction ()
	{
		Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->setNoRender();
		Zend_Controller_Action_HelperBroker::getStaticHelper('layout')->disableLayout();
		
		$form = new Menu_Form_UploadMenu();
		
		if ($this->_request->isPost()) {
			$formData = $this->_request->getPost();
			if ($form->isValid($formData)) {
				
				// success - do something with the uploaded file
				if ($form->menuFile->isUploaded()) {
					// foo file given... do something
					$fullFilePath = $form->getValues();
					$fullFilePath = $form->menuFile->getFileName();
					Zend_Debug::dump($fullFilePath);
					
					$objConfig = new Zend_Config(require $fullFilePath);
					
					Zend_Debug::dump($objConfig->toArray());
					$objMenuTable = new Menu_Model_Db_Menu();
					
					foreach ($objConfig as $objConfigMenuRow) {
						$objMenuRowSet = $objMenuTable->find($objConfigMenuRow->{Menu_Model_Db_Menu::COL_ID_MENU});
						
						if ($objMenuRowSet->count() > 0) {
							$objMenuRow = $objMenuRowSet->current();
						} else {
							// Add New
							$objMenuRow = $objMenuTable->createRow();
						}
						$objMenuRow->setFromArray($objConfigMenuRow->toArray());
						$objMenuRow->save();
					}
				}
			
			} else {
				$form->populate($formData);
			}
		}
	
		$strUrl = $this->view->url(array('module' => 'menu', 'controller' => 'index', 'action' => 'index'), null, true);
		$this->_redirect($strUrl);
	}
}