<?php
namespace mod_rtw\db;
use mod_rtw\db\base;
class experience extends base {
    protected static $_instance = null;

    /**
     * 
     * @return experience
     */
    public static function getInstance() {
        if (!empty(self::$_instance)) {
            return self::$_instance;
        }
        self::$_instance = new coin('mdl_rtw_experience');
        return self::$_instance;
    }
    
    public function __construct($tableName, $primaryKey = 'id') {
        parent::__construct($tableName, $primaryKey);
    }
}

