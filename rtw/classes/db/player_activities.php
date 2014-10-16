<?php
namespace mod_rtw\db;
use mod_rtw\db\base;
class player_activities extends base {
    protected static $_instance = null;

    /**
     * @example player_id,controller,`action`,create_time,course_module_id
     * @return player_activities
     */
    public static function getInstance() {
        if (!empty(self::$_instance)) {
            return self::$_instance;
        }
        self::$_instance = new player_activities('mdl_rtw_player_activities');
        return self::$_instance;
    }
    
    public function __construct($tableName, $primaryKey = 'id') {
        parent::__construct($tableName, $primaryKey);
    }
}

