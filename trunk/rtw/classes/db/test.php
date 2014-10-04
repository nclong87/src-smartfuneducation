<?php
namespace mod_rtw\db;
use mod_rtw\db\base;
class test extends base {
    protected static $_instance = null;

    /**
     * 
     * @return test
     */
    public static function getInstance() {
        if (!empty(self::$_instance)) {
            return self::$_instance;
        }
        self::$_instance = new test('mdl_role');
        return self::$_instance;
    }
    
    public function __construct($tableName, $primaryKey = 'id') {
        parent::__construct($tableName, $primaryKey);
    }
}

