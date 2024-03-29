<?php

namespace mod_rtw\core;
use Exception;

class player {
    private $_player_info;
    private $_user_id;
    private $_course_id;

    protected static $_instance = null;

    /**
     * 
     * @return player
     */
    public static function getInstance() {
        if (!empty(self::$_instance)) {
            return self::$_instance;
        }
        self::$_instance = new player();
        return self::$_instance;
    }
    
    public function __construct() {
    }
    
    public function init($user_id,$course_id) {
        $this->_user_id = $user_id;
        $this->_course_id = $course_id;
        unset($this->_player_info);
        $this->getPlayerInfo();
    }
    
    public function getPlayerInfo() {
        if(!isset($this->_player_info)) {
            $this->_player_info = \mod_rtw\db\player::getInstance()->findByCourseId($this->_user_id, $this->_course_id);
            if($this->_player_info == false) {
                throw new Exception('Player not found');
            }
        }
        return $this->_player_info;
    }
    
   /**
    * 
    * @param long $game_id Id in table mdl_rtw_player_game
    * @param int $coin_change Number coin change
    * @return long Id in table mdl_rtw_coins
    */
    public function change_coin($game_id,$coin_change) {
        $coin_after = $this->_player_info->current_coin + $coin_change;
        $data = array(
            'player_game_id' => $game_id,
            'coin_change' => $coin_change,
            'create_time' => date_utils::getCurrentDateSQL(),
            'coin_before' => $this->_player_info->current_coin,
            'coin_after' => $coin_after
        );
        $coin_id = \mod_rtw\db\coin::getInstance()->insert($data,true);
        $data = array(
            'current_coin' => $coin_after,
            'last_update' => date_utils::getCurrentDateSQL()
        );
        \mod_rtw\db\player::getInstance()->update($this->_player_info->id, $data);
        $this->_player_info->current_coin = $coin_after;
        return $coin_id;
    }
    
    /**
     * 
     * @param number $game_id
     * @param number $num_xp
     */
    public function incrExp($game_id,$num_xp) {
        $xp_after = $this->_player_info->current_xp + $num_xp;
        $data = array(
            'player_game_id' => $game_id,
            'num_xp' => $num_xp,
            'create_time' => date_utils::getCurrentDateSQL(),
            'xp_before' => $this->_player_info->current_xp,
            'xp_after' => $xp_after
        );
        $exp_id = \mod_rtw\db\experience::getInstance()->insert($data,true);
        $data = array(
            'current_xp' => $xp_after,
            'last_update' => date_utils::getCurrentDateSQL()
        );
        \mod_rtw\db\player::getInstance()->update($this->_player_info->id, $data);
        $this->_player_info->current_xp = $xp_after;
        return $exp_id;
    }
    
    /**
     * 
     * @param Integer $num_turn So lan quay so cong (tru)
     * @param Long $game_id Id game
     */
    public function changeTurn($num_turn,$game_id = null) {
        $turn_before = $this->_player_info->lottery_turn;
        $this->_player_info->lottery_turn += $num_turn;
        $lottery_turn_after = $this->_player_info->lottery_turn;
        $data = array(
            'player_game_id' => $game_id,
            'turn_change' => $num_turn,
            'create_time' => date_utils::getCurrentDateSQL(),
            'turn_before' => $turn_before,
            'turn_after' => $lottery_turn_after
        );
        \mod_rtw\db\lotteryturn::getInstance()->insert($data);
        $data = array(
            'lottery_turn' => $lottery_turn_after,
            'last_update' => date_utils::getCurrentDateSQL()
        );
        \mod_rtw\db\player::getInstance()->update($this->_player_info->id, $data);
    }
    
}
