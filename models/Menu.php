<?php
class Menu_Model_Menu {
    
	public function getMenu ($boolReload = false)
	{
		$objAclSession = new Zend_Session_Namespace('userData');
		if ($boolReload || empty($objAclSession->menu)) {
			$arrMenu['resources']['navigation']['pages'] = $this->getNavLevel(0);
		}
		return $arrMenu;
	}

	public function getNavLevel ($intParent)
	{
	    $objMenuTable = new Menu_Model_Db_Menu(array(), null, false);
		$objMenuSelect = $objMenuTable->select(TRUE);
		$objMenuSelect->where(Menu_Model_Db_Menu::COL_ID_PARENT . ' = ?', $intParent);
		$objMenuSelect->where(Menu_Model_Db_Menu::COL_IS_DELETED . ' = ?', false);
		$objMenuSelect->order(Menu_Model_Db_Menu::COL_ORDER . ' ' . Zend_Db_Select::SQL_ASC);
		$objMenuRowSet = $objMenuTable->fetchAll($objMenuSelect);
		$arrMenu = array();
		foreach ($objMenuRowSet as $objMenuRow) {
			$arrMenu[$objMenuRow->{Menu_Model_Db_Menu::COL_CODE}] = $this->createMenuArrayFromRow($objMenuRow);
		}
		return $arrMenu;
	}

	public function createMenuArrayFromRow ($objMenuRow)
	{
		$arrMenu = array();
		$arrMenu['label'] = $objMenuRow->{Menu_Model_Db_Menu::COL_LABEL};
		
		if (! empty($objMenuRow->{Menu_Model_Db_Menu::COL_MODULE})) {
			$arrMenu['module'] = $objMenuRow->{Menu_Model_Db_Menu::COL_MODULE};
		}
		if (! empty($objMenuRow->{Menu_Model_Db_Menu::COL_CONTROLLER})) {
			$arrMenu['controller'] = $objMenuRow->{Menu_Model_Db_Menu::COL_CONTROLLER};
		}
		if (! empty($objMenuRow->{Menu_Model_Db_Menu::COL_ACTION})) {
			$arrMenu['action'] = $objMenuRow->{Menu_Model_Db_Menu::COL_ACTION};
		}
		if (! empty($objMenuRow->{Menu_Model_Db_Menu::COL_URI})) {
			$arrMenu['uri'] = $objMenuRow->{Menu_Model_Db_Menu::COL_URI};
		}
		$arrMenu['resource'] = $objMenuRow->{Menu_Model_Db_Menu::COL_RESOURCE};
		$arrMenu['privilege'] = $objMenuRow->{Menu_Model_Db_Menu::COL_PRIVELEGE};
		$arrMenu['css'] = $objMenuRow->{Menu_Model_Db_Menu::COL_CSS};
		$arrMenuSubLevel = $this->getNavLevel($objMenuRow->{Menu_Model_Db_Menu::COL_ID_MENU});
		if (! empty($arrMenuSubLevel)) {
			$arrMenu['pages'] = $arrMenuSubLevel;
		}
		return $arrMenu;
	}
    
}