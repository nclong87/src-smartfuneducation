<?php

namespace mod_rtw\db;

use mod_rtw\db\base;

class game_proactive_evaluation extends base {

    protected static $_instance = null;

    /**
     * @example id,game_player_id,game_proactive_id,point_section_1,point_section_2,point_section_3,point_avg,submit_time
     * @return game_proactive_evaluation
     */
    public static function getInstance() {
        if (!empty(self::$_instance)) {
            return self::$_instance;
        }
        self::$_instance = new game_proactive_evaluation('mdl_rtw_game_proactive_evaluation');
        return self::$_instance;
    }

    public function __construct($tableName, $primaryKey = 'id') {
        parent::__construct($tableName, $primaryKey);
    }
    
    public function findByProactiveGameAndPlayer($player_id,$game_proactive_id) {
        return $this->_db->get_records_sql('select * from mdl_rtw_game_proactive_evaluation t0 inner join mdl_rtw_player_game t1 on t0.game_player_id = t1.id where t1.player_id = ? and t0.game_proactive_id = ?', array($player_id,$game_proactive_id));
    }
    
}
