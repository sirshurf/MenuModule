<?php

class Menu_Model_Db_Menu extends Bf_Db_Table
{

    /**
     * The default table name 
     */
    const TBL_NAME = 'menu';

    CONST COL_ID_MENU = 'id_menu';

    CONST COL_ID_MENU_CODE = 'id_menu_code';

    CONST COL_CODE = "code";

    CONST COL_LABEL = "label";

    CONST COL_ID_PARENT = "id_parent";

    CONST COL_URI = "uri";

    CONST COL_MODULE = "col_module";

    CONST COL_CONTROLLER = "col_controller";

    CONST COL_ACTION = "col_action";

    CONST COL_CSS = "css_class";

    CONST COL_RESOURCE = "id_resources";

    CONST COL_PRIVELEGE = "privelege";

    CONST COL_ORDER = "sort_order";

    CONST COL_UPDATED_BY = 'updated_by';

    CONST COL_UPDATED_ON = 'updated_on';

    CONST COL_CREATED_BY = 'created_by';

    CONST COL_CREATED_ON = 'created_on';

    CONST COL_IS_DELETED = 'is_deleted';
    
    protected $_referenceMap = array();

    public function __construct ($config = array(), $definition = null, $boolLoadReference = true)
    {
        parent::__construct($config, $definition);
        
        if ($boolLoadReference) {
            $this->_referenceMap = array(
            'Parent' => array('columns' => array(self::COL_ID_PARENT), 'refTableClass' => 'Menu_Model_Db_Menu', 'refColumns' => array(self::COL_ID_MENU), 'displayColumn' => self::COL_LABEL), 
            'Resources' => array('columns' => array(self::COL_RESOURCE), 'refTableClass' => 'User_Model_Db_Resources', 'refColumns' => array(User_Model_Db_Resources::COL_ID_RESOURCES), 
            'displayColumn' => new Zend_Db_Expr("CONCAT_WS('/'," . User_Model_Db_Resources::COL_MODULE . "," . User_Model_Db_Resources::COL_CONTROLLER . ")")));
        }
    
    }

}
