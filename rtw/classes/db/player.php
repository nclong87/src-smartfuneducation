<?php
namespace mod_rtw\db;
use mod_rtw\db\base;
use mod_rtw\core\date_utils;
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
    
    public function getUserById($uid) {
        $user = $this->_db->get_record_sql('select * from mdl_user where id = ?', array($uid));
        return $user == false ? null : $user;
    }
    
    public function getUserByPlayerId($player_id) {
        $user = $this->_db->get_record_sql("select t0.*,t1.id as 'player_id', t1.course_id,t1.current_coin,t1.current_level,t1.current_xp,t1.lottery_turn from mdl_user t0 inner join mdl_rtw_players t1 on t0.id = t1.user_id where t1.status = 1 and t1.id = ?", array($player_id));
        return $user == false ? null : $user;
    }
    
    /**
    * 
    * @param object $player_info Table mdl_rtw_player 
    * @param long $game_id Id in table mdl_rtw_player_game
    * @param int $coin_change Number coin change
    * @return long Id in table mdl_rtw_coins
    */
    public function change_coin($player_info, $game_id,$coin_change) {
        $coin_after = $player_info->current_coin + $coin_change;
        $data = array(
            'player_game_id' => $game_id,
            'coin_change' => $coin_change,
            'create_time' => date_utils::getCurrentDateSQL(),
            'coin_before' => $player_info->current_coin,
            'coin_after' => $coin_after
        );
        $coin_id = \mod_rtw\db\coin::getInstance()->insert($data,true);
        $data = array(
            'current_coin' => $coin_after,
            'last_update' => date_utils::getCurrentDateSQL()
        );
        \mod_rtw\db\player::getInstance()->update($player_info->id, $data);
        return $coin_id;
    }
    
     /**
     * 
     * @param object $player_info Table mdl_rtw_player 
     * @param number $game_id
     * @param number $num_xp
     */
    public function incrExp($player_info, $game_id,$num_xp) {
        $xp_after = $player_info->current_xp + $num_xp;
        $data = array(
            'player_game_id' => $game_id,
            'num_xp' => $num_xp,
            'create_time' => date_utils::getCurrentDateSQL(),
            'xp_before' => $player_info->current_xp,
            'xp_after' => $xp_after
        );
        $exp_id = \mod_rtw\db\experience::getInstance()->insert($data,true);
        $data = array(
            'current_xp' => $xp_after,
            'last_update' => date_utils::getCurrentDateSQL()
        );
        \mod_rtw\db\player::getInstance()->update($player_info->id, $data);
        return $exp_id;
    }
}

