<?php
use mod_rtw\db\game;
use mod_rtw\core\player;
use mod_rtw\core\date_utils;
use mod_rtw\db\question_categories;
use mod_rtw\db\game_videoquiz;
class mod_rtw_renderer extends mod_rtw_renderer_base {
    public function __construct(\moodle_page $page, $target) {
        parent::__construct($page, $target);
        $this->_PATH = realpath(dirname(__FILE__));
    }
    
    
    public function render_index() {
       
    }
    
    public function render_test() {
        $data = mod_rtw\db\coin::getInstance()->query(array('player_game_id' => 86),true);
        $this->set_var('data', $data);
        $this->doRender('test.php');
    }
}