<?php

namespace mod_rtw\db;

use mod_rtw\db\base;

class group_level_history extends base {

    protected static $_instance = null;

    /**
     * @example id,group_id,`level`,create_time,status
     * @return group_level_history
     */
    public static function getInstance() {
        if (!empty(self::$_instance)) {
            return self::$_instance;
        }
        self::$_instance = new group_level_history('mdl_rtw_group_level_history');
        return self::$_instance;
    }

    public function __construct($tableName, $primaryKey = 'id') {
        parent::__construct($tableName, $primaryKey);
    }
    
}
