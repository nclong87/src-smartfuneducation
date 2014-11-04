<?php

namespace mod_rtw\db;

use mod_rtw\db\base;

class game_proactive extends base {

    protected static $_instance = null;

    /**
     * @example id,game_player_id,question_id, answer,answer_time
     * @return game_proactive
     */
    public static function getInstance() {
        if (!empty(self::$_instance)) {
            return self::$_instance;
        }
        self::$_instance = new game_proactive('mdl_rtw_game_proactive');
        return self::$_instance;
    }

    public function __construct($tableName, $primaryKey = 'id') {
        parent::__construct($tableName, $primaryKey);
    }
    
}
