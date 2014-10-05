<?php
use mod_rtw\db\game;
use mod_rtw\core\player;
use mod_rtw\core\date_utils;
class mod_rtw_renderer extends mod_rtw_renderer_base {
    public function __construct(\moodle_page $page, $target) {
        parent::__construct($page, $target);
        $this->_PATH = realpath(dirname(__FILE__));
    }
    
    public function render_index() {
        //Lay thong tin nguoi choi
        $player_info = player::getInstance()->getPlayerInfo();
        
        //Lay thong tin game theo level hien tai cua nguoi choi
        $game = game::getInstance()->findByQuest('videoquiz', $player_info->current_level);
        if($game == false) {
            throw new Exception(get_string('no_data', 'mod_rtw'));
        }
        
        //Lay game videoquiz gan day nhat cua nguoi choi
        $last_game = game::getInstance()->findLastGame($game->id, $player_info->id);
        if($last_game == false) {
            // Player chua choi game nay -> Tao game moi cho player
            $current_game = game::getInstance()->createNewGame($player_info->id, $game->id, 300);
        } else {
            // Player da choi game nay luc $last_game->create_time;
            if(date_utils::isPast($last_game->expired_time)) {
                // Game het hieu luc -> Tao game moi cho player
                $current_game = game::getInstance()->createNewGame($player_info->id, $game->id, 300);
            } else {
                // Game van con hieu luc -> Su dung game nay
                $current_game = $last_game;
            }
        }
        
        rtw_debug($current_game);
        $this->_file = 'index.php';
        $this->doRender();
    }
    

}