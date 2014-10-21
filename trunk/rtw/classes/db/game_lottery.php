<?php

namespace mod_rtw\db;

use mod_rtw\db\base;
use Exception;

class game_lottery extends base {

    protected static $_instance = null;

    /**
     * @example id,game_player_id,result_coin,coin_id,create_time
     * @return game_lottery
     */
    public static function getInstance() {
        if (!empty(self::$_instance)) {
            return self::$_instance;
        }
        self::$_instance = new game_lottery('mdl_rtw_game_lottery');
        return self::$_instance;
    }

    public function __construct($tableName, $primaryKey = 'id') {
        parent::__construct($tableName, $primaryKey);
    }
 
    /**
     * 
     * @param long $player_id
     * @param object $current_game mdl_rtw_player_game
     * @return array response_code, pluscoin
     */
    public function turn($player_id,$current_game) {
        $transaction = $this->_db->start_delegated_transaction();
        try {
            $array = $this->getRandomArray();
            $plus_coin = $array[rand(0, count($array) - 1)];
            //$plus_coin = 7;
            $sql = 'update mdl_rtw_players set lottery_turn = lottery_turn - 1 where id = ?';
            $this->_db->execute($sql, array($player_id));
            
            $coin_id = \mod_rtw\core\player::getInstance()->change_coin($current_game->id, $plus_coin);
            $data= array(
                'game_player_id' => $current_game->id,
                'result_coin' => $plus_coin,
                'coin_id' => $coin_id,
                'create_time' => \mod_rtw\core\date_utils::getCurrentDateSQL()
            );
            game_lottery::getInstance()->insert($data);
            
            $transaction->allow_commit();
            return array(1,$plus_coin);
        } catch (Exception $exc) {
            $transaction->rollback($exc);
        }
        return array(0,0);
    }
    
    private function getRandomArray() {
        global $CONFIG_RTW;
        $array = array();
        foreach ((array)$CONFIG_RTW->lottery->rate as $key => $value) {
            for ($i = 1; $i <= $value; $i++) {
                $array[] = $key;
            }
        }
        return $array;
    }
    
    /**
     * 
     * @param long $player_id
     * @param int $num_turn So lan quay so cong vao
     */
    public function addTurn($player_id,$num_turn) {
        $sql = 'update mdl_rtw_players set lottery_turn = lottery_turn + ? where id = ?';
        $this->_db->execute($sql, array($num_turn,$player_id));
        
    }

}
