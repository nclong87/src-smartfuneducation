<?php

namespace mod_rtw\db;

use mod_rtw\db\base;

class game_ranking extends base {

    protected static $_instance = null;

    /**
     * @example id,game_player_id,question_id,show_time,start_time,submit_time,is_ranking,coin_id
     * @return game_ranking
     */
    public static function getInstance() {
        if (!empty(self::$_instance)) {
            return self::$_instance;
        }
        self::$_instance = new game_ranking('mdl_rtw_game_ranking');
        return self::$_instance;
    }

    public function __construct($tableName, $primaryKey = 'id') {
        parent::__construct($tableName, $primaryKey);
    }
    
    public function getRankingValueArray() {
        global $CONFIG_RTW;
        $array = array();
        foreach ((array)$CONFIG_RTW->ranking->value as $value) {
            $array[] = $value;
        }
        return $array;
    }
}
