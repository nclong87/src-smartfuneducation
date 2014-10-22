<?php
namespace mod_rtw\db;
use mod_rtw\db\base;
class lotteryturn extends base {
    protected static $_instance = null;

    /**
     * 
     * @return lotteryturn
     */
    public static function getInstance() {
        if (!empty(self::$_instance)) {
            return self::$_instance;
        }
        self::$_instance = new lotteryturn('mdl_rtw_lotteryturn');
        return self::$_instance;
    }
    
    public function __construct($tableName, $primaryKey = 'id') {
        parent::__construct($tableName, $primaryKey);
    }
}

