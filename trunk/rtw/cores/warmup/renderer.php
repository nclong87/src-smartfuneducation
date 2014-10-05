<?php
use mod_rtw\db\warmup;
use mod_rtw\core\log;
class mod_rtw_renderer extends mod_rtw_renderer_base {
    public function __construct(\moodle_page $page, $target) {
        parent::__construct($page, $target);
        $this->_PATH = realpath(dirname(__FILE__));
    }
    
    public function render_index() {
        $this->_file = 'index.php';
        $test = optional_param('test', '', PARAM_TEXT); 
        $this->set_var('test', $test);
        $this->doRender();
    }
    
    public function render_add() {
        //\mod_rtw\core\player::getInstance()->change_coin(1, 1);
        $data = array(
            'player_game_id' => 1,
            'group_id' => 4
        );
        log::getInstance()->log(array($data));
        //warmup::getInstance()->insert($data);
        $this->set_var('player_info', \mod_rtw\core\player::getInstance()->getPlayerInfo());
        $this->_file = 'add.php';
        $this->doRender();
    }

}