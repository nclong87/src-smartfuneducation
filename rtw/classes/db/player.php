<?php
namespace mod_rtw\db;
use mod_rtw\db\base;
class player extends base {
    protected static $_instance = null;

    /**
     * 
     * @return player
     */
    public static function getInstance() {
        if (!empty(self::$_instance)) {
            return self::$_instance;
        }
        self::$_instance = new player('mdl_rtw_players');
        return self::$_instance;
    }
    
    public function __construct($tableName, $primaryKey = 'id') {
        parent::__construct($tableName, $primaryKey);
    }
    
    /**
     * 
     * @param string $user_id
     * @param string $course_id
     * @return mix
     */
    public function findByCourseId($user_id,$course_id) {
        return \mod_rtw\db\player::getInstance()->query(array('user_id' => $user_id,'course_id' => $course_id), true);
    }
}

