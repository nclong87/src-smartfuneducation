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
    
    public function getRecentActivity($cm_id) {
        $now = new \DateTime();
        $now->sub(new \DateInterval('P1D'));
        $sql = 'select t2.*,t0.player_id,t1.user_id,t1.course_id,t0.course_module_id,t0.create_time as `recent_act` from mdl_rtw_player_activities t0 inner join mdl_rtw_players t1 on t0.player_id = t1.id inner join mdl_user t2 on t1.user_id = t2.id where t0.id in( select max(t0.id) from mdl_rtw_player_activities t0 inner join mdl_rtw_players t1 on t0.player_id = t1.id where 
t0.course_module_id  = ? and t0.create_time >= ? group by t0.player_id) order by t0.id desc limit 0,10';
        return $this->_db->get_records_sql($sql,array($cm_id,$now->format(\mod_rtw\core\date_utils::$formatSQLDate)));
    }
}

