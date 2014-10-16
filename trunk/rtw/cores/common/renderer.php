<?php
class mod_rtw_renderer extends mod_rtw_renderer_base {
    public function __construct(\moodle_page $page, $target) {
        parent::__construct($page, $target);
        $this->_PATH = realpath(dirname(__FILE__));
    }
    
    public function render_properties() {
        $this->doRender('properties.php');
    }
    public function render_group_activity() {
        $this->doRender('group_activity.php');
    }
    public function render_trend() {
        $this->doRender('trend.php');
    }
    public function render_top_player_activity() {
        global $OUTPUT;
        $top_player_activity = mod_rtw\db\game::getInstance()->getTopPlayerActivity($this->course->id);
        foreach ($top_player_activity as $obj) {
            $obj->picture = $OUTPUT->user_picture($obj, array('size'=>30));
        }
        $this->set_var('top_player_activity', $top_player_activity);
        //rtw_debug($top_player_activity);
        $this->doRender('top_player_activity.php');
    }

    public function render_index() {
        
    }

}