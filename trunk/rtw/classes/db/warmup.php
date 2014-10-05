<?php
namespace mod_rtw\db;
use mod_rtw\db\base;
class warmup extends base {
    protected static $_instance = null;

    /**
     * 
     * @return warmup
     */
    public static function getInstance() {
        if (!empty(self::$_instance)) {
            return self::$_instance;
        }
        self::$_instance = new warmup('mdl_rtw_game_warmup');
        return self::$_instance;
    }
    
    public function __construct($tableName, $primaryKey = 'id') {
        parent::__construct($tableName, $primaryKey);
    }
  
}

