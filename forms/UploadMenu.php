<?php

class Menu_Form_UploadMenu extends ZendX_JQuery_Form
{

	public function init ()
	{
		/* Form Elements & Other Definitions Here ... */
		
		$this->setName('menuFileForm');
		$this->setAttrib('id', 'menuFileForm');
		$this->setAction($this->getView()
			->url(array('module' => 'menu', 'controller' => 'index', 'action' => 'loadbackup'), null, true));
		 $this->setAttrib('enctype', 'multipart/form-data');
		

		// creating object for Zend_Form_Element_File
		$objFile = new Zend_Form_Element_File('menuFile');
		$objFile->setLabel('LBL_MENU_BKP_FILE')
//            ->setDestination('/tmp')
            ->setRequired(true);
		$this->addElement($objFile);
	
	}

}

