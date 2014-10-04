<?php
namespace mod_rtw\db;
use mod_rtw\db\base;
class coin extends base {
    protected static $_instance = null;

    /**
     * 
     * @return coin
     */
    public static function getInstance() {
        if (!empty(self::$_instance)) {
            return self::$_instance;
        }
        self::$_instance = new coin('mdl_rtw_coins');
        return self::$_instance;
    }
    
    public function __construct($tableName, $primaryKey = 'id') {
        parent::__construct($tableName, $primaryKey);
    }
}

