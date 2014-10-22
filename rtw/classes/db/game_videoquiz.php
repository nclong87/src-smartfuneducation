<?php

namespace mod_rtw\db;

use mod_rtw\db\base;

class game_videoquiz extends base {

    protected static $_instance = null;

    /**
     * @example id,videoquiz_id,game_player_id,question_id,show_time,start_time,submit_time,is_correct,coin_id
     * @return game_videoquiz
     */
    public static function getInstance() {
        if (!empty(self::$_instance)) {
            return self::$_instance;
        }
        self::$_instance = new game_videoquiz('mdl_rtw_game_videoquiz');
        return self::$_instance;
    }

    public function __construct($tableName, $primaryKey = 'id') {
        parent::__construct($tableName, $primaryKey);
    }
    
    /**
     * 
     * @param number $player_level
     * @param number $num
     * @param String $quest Queust code
     * @return mix id,embed_code,url,`level`,status,length
     */
    public function getRandomVideo($player_level,$num = 1,$quest = 'videoquiz') {
        return $this->_db->get_record_sql('select * from mdl_rtw_videoquizs where `level` = ? and status = 1 and quest = ? order by rand() limit 0,'.$num,array($player_level,$quest));
    }
    
    /**
     * 
     * @param number $game_player_id
     * @return mix id,embed_code,url,`level`,status,length
     */
    public function getVideoByPlayerGameId($game_player_id) {
        return $this->_db->get_record_sql('select * from mdl_rtw_videoquizs t0 left join mdl_rtw_game_videoquiz t1 on t0.id = t1.videoquiz_id where t1.game_player_id = ? limit 0,1',array($game_player_id));
    }

}
