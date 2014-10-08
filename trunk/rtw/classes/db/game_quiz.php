<?php

namespace mod_rtw\db;

use mod_rtw\db\base;

class game_quiz extends base {

    protected static $_instance = null;

    /**
     * @example id,game_player_id,question_id,show_time,start_time,submit_time,is_correct,coin_id
     * @return game_quiz
     */
    public static function getInstance() {
        if (!empty(self::$_instance)) {
            return self::$_instance;
        }
        self::$_instance = new game_quiz('mdl_rtw_game_quiz');
        return self::$_instance;
    }

    public function __construct($tableName, $primaryKey = 'id') {
        parent::__construct($tableName, $primaryKey);
    }
    
}
