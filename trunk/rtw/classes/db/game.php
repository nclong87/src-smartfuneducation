<?php
namespace mod_rtw\db;
use mod_rtw\db\base;
use mod_rtw\core\date_utils;
class game extends base {
    protected static $_instance = null;

    /**
     * 
     * @return game
     */
    public static function getInstance() {
        if (!empty(self::$_instance)) {
            return self::$_instance;
        }
        self::$_instance = new game('mdl_rtw_player_game');
        return self::$_instance;
    }
    
    public function __construct($tableName, $primaryKey = 'id') {
        parent::__construct($tableName, $primaryKey);
    }
    
    /**
     * @param String $quest Quest module
     * @param Long $player_id
     * @return Object id,player_id,level,quest,create_time,expired_time,status
     */
    public function findLastGame($quest,$player_id) {
        $sql = 'select * from mdl_rtw_player_game t0 where t0.status = 1 and player_id = ? and quest = ? order by t0.id desc limit 0,1';
        return $this->_db->get_record_sql($sql, array($player_id,$quest));
    }
    
    /**
     * 
     * @param number $player_id
     * @param number $quest
     * @param number $level Current level of player
     * @param number $limit_time (second , default is 300 seconds)
     * @return Object id,player_id,level,quest,create_time,expired_time,status
     */
    public function createNewGame($player_id,$quest,$level,$limit_time = 300) {
        $now = new \DateTime();
        $data = array(
            'player_id' => $player_id,
            'quest' => $quest,
            'level' => $level,
            'create_time' => $now->format(date_utils::$formatSQLDateTime),
            'status' => 1
        );
        $now->add(new \DateInterval('PT'.$limit_time.'S'));
        $data['expired_time'] = $now->format(date_utils::$formatSQLDateTime);
        $player_game_id = $this->insert($data,true);
        $data['id'] = $player_game_id;
        return \mod_rtw\core\utils::array2object($data);
    }
    
    public function findLastGameByUids($course_id, $uids) {
        $sql = 'select t0.*,t1.user_id from mdl_rtw_player_game t0 inner join (select user_id,max(t0.id) as id from mdl_rtw_player_game t0 inner join mdl_rtw_players t1 on t0.player_id = t1.id inner join mdl_user t2 on t1.user_id = t2.id and t1.status = 1 where t0.status = 1 and t1.course_id = ? and t2.id in ('.  implode(',', $uids).') group by user_id) as t1 on t0.id = t1.id';
       return $this->_db->get_records_sql($sql, array($course_id));
    }
    
    public function getPlayHistory($player_id,$level) {
        $sql = 'select  t0.quest,t0.`level`,count(*) from mdl_rtw_player_game t0 where t0.status = 1 and player_id = ? and t0.`level` = ? group by t0.quest,t0.`level`';
        return $this->_db->get_records_sql($sql, array($player_id,$level));
    }
    
    public function getTopPlayerActivity($course_id) {
        //$sql = 'select t2.*,t1.id as player_id,t1.current_coin,t1.current_level,t1.current_xp,count(*) as play_time from mdl_rtw_player_game t0 inner join mdl_rtw_players t1 on t0.player_id = t1.id inner join mdl_user t2 on t1.user_id = t2.id where t1.course_id = ? and t0.status =1 and t1.status = 1 group by t1.user_id order by count(*) desc limit 0,10';
        $sql = 'select t1.*,t0.id as `player_id`,t0.course_id,t0.current_coin,t0.current_level,t0.current_xp from mdl_rtw_players t0 inner join mdl_user t1 on t0.user_id = t1.id
where t0.status = 1 and t0.course_id = ? order by t0.current_coin desc limit 0,5';
        return $this->_db->get_records_sql($sql, array($course_id));
    }
}

