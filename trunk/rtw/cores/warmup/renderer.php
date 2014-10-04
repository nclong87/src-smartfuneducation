<?php
class mod_rtw_renderer extends mod_rtw_renderer_base {
    public function __construct(\moodle_page $page, $target) {
        parent::__construct($page, $target);
        $this->_PATH = realpath(dirname(__FILE__));
    }
    
    public function render_index() {
        $this->_file = 'index.php';
        $this->doRender();
    }
    
    public function render_add() {
        \mod_rtw\core\player::getInstance()->change_coin(1, 1);
        $this->set_var('player_info', \mod_rtw\core\player::getInstance()->getPlayerInfo());
        $this->_file = 'add.php';
        $this->doRender();
    }

}