<?php
class Menu_IndexController extends Zend_Controller_Action {
    
    public function indexAction(){
        
		$objMenuTable = new Menu_Model_Db_Menu();
		$objMenuSelect = $objMenuTable->select (TRUE);
		
		$arrOptions = array ("hiddengrid" => false, "caption" => "" );
		
		$grid = new Ingot_JQuery_JqGrid ( 'menu', new Ingot_JQuery_JqGrid_Adapter_DbTableSelect ( $objMenuSelect ),$arrOptions );
		$grid->setIdCol ( Menu_Model_Db_Menu::COL_ID_MENU );
		$grid->setLocalEdit();		
				
		$grid->addColumn ( new Ingot_JQuery_JqGrid_Column ( Menu_Model_Db_Menu::COL_CODE, array('editable' => true) ) );
		$grid->addColumn ( new Ingot_JQuery_JqGrid_Column ( Menu_Model_Db_Menu::COL_LABEL, array('editable' => true) ) );
				
		Ingot_JQuery_JqGrid_Column_DoubleColumn::createSelectColumn($grid, 'Parent', array(), FALSE );
				
		$grid->addColumn ( new Ingot_JQuery_JqGrid_Column ( Menu_Model_Db_Menu::COL_URI, array('editable' => true) ) );
		$grid->addColumn ( new Ingot_JQuery_JqGrid_Column ( Menu_Model_Db_Menu::COL_MODULE, array('editable' => true) ) );
		$grid->addColumn ( new Ingot_JQuery_JqGrid_Column ( Menu_Model_Db_Menu::COL_CONTROLLER , array('editable' => true)) );
		$grid->addColumn ( new Ingot_JQuery_JqGrid_Column ( Menu_Model_Db_Menu::COL_ACTION, array('editable' => true) ) );
		$grid->addColumn ( new Ingot_JQuery_JqGrid_Column ( Menu_Model_Db_Menu::COL_CSS, array('editable' => true) ) );
		
		
		Ingot_JQuery_JqGrid_Column_DoubleColumn::createSelectColumn($grid, 'Resources', array(), FALSE );
		
		//$grid->addColumn ( new Ingot_JQuery_JqGrid_Column ( Menu_Model_Db_Menu::COL_RESOURCE ) );
		$grid->addColumn ( new Ingot_JQuery_JqGrid_Column ( Menu_Model_Db_Menu::COL_PRIVELEGE, array('editable' => true) ) );	
		$grid->addColumn ( new Ingot_JQuery_JqGrid_Column ( Menu_Model_Db_Menu::COL_ORDER, array('editable' => true) ) );	
				
		$objPlugin = $grid->getPager ();
		$objPlugin->setDefaultAdd ();
		$objPlugin->setDefaultEdit ();
		$objPlugin->setDefaultDel ();
		$grid->setDblClkEdit();
		
		$grid->registerPlugin ( new Ingot_JQuery_JqGrid_Plugin_ToolbarFilter () );
		$this->view->grid = $grid->render ();
    }
    
    
    public function editmenuAction(){
                
        $strOper = $this->_request->getParam ( 'oper' );
		
		$objMenuTable = new Labadmin_Models_Menu();
		$objRow = null;
		$arrStatus  =array();
		switch ($strOper) {
			case "edit" :
				$intId = ( int ) $this->_request->getParam ( 'id' );
				$objSelect = $objMenuTable->select (TRUE);
				$objSelect->where ( Labadmin_Models_Menu::COL_ID_MENU." = ?", $intId );
				$objRow = $objMenuTable->fetchRow ( $objSelect );
			case "add" :
				if (empty ( $objRow )) {
					$objRow = $objMenuTable->createRow ();
				}
				$arrParams = $this->_request->getParams ();
				$objRow->setFromArray($arrParams);
				if( $objRow->save ()){
				    $arrStatus = array('code' => 'ok', 'msg' => '');
				} else {
				    $arrStatus = array('code' => 'error', 'msg' => $this->view->translate('LBL_UPDATE_FAIL') );
				}
				
				break;
			case "del" :				
				$intId = ( int ) $this->_request->getParam ( 'id' );
				$objSelect = $objMenuTable->select (TRUE);
				$objSelect->where ( Labadmin_Models_Menu::COL_ID_MENU." = ?", $intId );
				$objRow = $objMenuTable->fetchRow ( $objSelect );
				if (! empty ( $objRow )) {
					if ($objRow->delete ()){					    
				        $arrStatus = array('code' => 'ok', 'msg' => '');
					} else {
				    $arrStatus = array('code' => 'error', 'msg' => $this->view->translate('LBL_DEL_FAIL') );
					}
				}
				break;
		
		}
		
		Labadmin_Models_Menu::getMenu(TRUE);
		
		$this->view->arrStatus = $arrStatus;
        
    }
    
}