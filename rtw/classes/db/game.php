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
     * @desc 
     * @param string $quest_name
     * @param number $user_level
     * @return mix
     * @throws Exception
     */
    public function findByQuest($quest_name,$user_level) {
        $sql = 'select t1.*,t0.quest_name,t0.module_name from mdl_rtw_quests t0 inner join mdl_rtw_games t1 on t0.id = t1.quest_id where `level` = ? and t0.status = 1 and t1.status = 1 and t0.module_name = ?';
        return $this->_db->get_record_sql($sql, array($user_level,$quest_name));
    }
    
    /**
     * @param type $game_id
     * @param type $player_id
     * @return id,player_id,game_id,create_time,expired_time,status
     */
    public function findLastGame($game_id,$player_id) {
        $sql = 'select * from mdl_rtw_player_game where status = 1 and player_id = ? and game_id = ? order by id desc limit 0,1';
        return $this->_db->get_record_sql($sql, array($player_id,$game_id));
    }
    
    /**
     * 
     * @param number $player_id
     * @param number $game_id
     * @param number $limit_time (second , default is 300 seconds)
     * @return number id,player_id,game_id,create_time,expired_time,status
     */
    public function createNewGame($player_id,$game_id,$limit_time = 300) {
        $now = new \DateTime();
        $data = array(
            'player_id' => $player_id,
            'game_id' => $game_id,
            'create_time' => $now->format(date_utils::$formatSQLDateTime),
            'status' => 1
        );
        $now->add(new \DateInterval('PT'.$limit_time.'S'));
        $data['expired_time'] = $now->format(date_utils::$formatSQLDateTime);
        $player_game_id = $this->insert($data,true);
        $data['id'] = $player_game_id;
        return \mod_rtw\core\utils::array2object($data);
    }
}

