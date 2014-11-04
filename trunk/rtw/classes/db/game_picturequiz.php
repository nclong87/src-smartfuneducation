<?php

namespace mod_rtw\db;

use mod_rtw\db\base;

class game_picturequiz extends base {

    protected static $_instance = null;

    /**
     * @example id,picturequiz_id,game_player_id,question_id,show_time,start_time,submit_time,is_correct,coin_id
     * @return game_picturequiz
     */
    public static function getInstance() {
        if (!empty(self::$_instance)) {
            return self::$_instance;
        }
        self::$_instance = new game_picturequiz('mdl_rtw_game_picturequiz');
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
    public function getRandomPicture($player_level,$num = 1,$quest = 'picturequest') {
        return $this->_db->get_record_sql('select * from mdl_rtw_picturequizs where `level` = ? and status = 1 and quest = ? order by rand() limit 0,'.$num,array($player_level,$quest));
    }
    
    /**
     * 
     * @param number $game_player_id
     * @return mix id,embed_code,url,`level`,status,length
     */
    public function getPictureByPlayerGameId($game_player_id) {
        return $this->_db->get_record_sql('select * from mdl_rtw_picturequizs t0 left join mdl_rtw_game_picturequiz t1 on t0.id = t1.picturequiz_id where t1.game_player_id = ? limit 0,1',array($game_player_id));
    }

}
