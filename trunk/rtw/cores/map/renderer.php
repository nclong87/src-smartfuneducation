<?php
class mod_rtw_renderer extends mod_rtw_renderer_base {
    public function __construct(\moodle_page $page, $target) {
        parent::__construct($page, $target);
        $this->_PATH = realpath(dirname(__FILE__));
    }
    
    public function render_index() {
        $groups_data = groups_get_course_data($this->course->id);
        $data = array();
        foreach ($groups_data->groups as $obj) {
            //$groups_data->groups->
            $group_level_history = \mod_rtw\db\group_level_history::getInstance()->query(array('status' => 1,'group_id' => $obj->id), true,'id desc',1);
            $level = isset($group_level_history->level)?$group_level_history->level:1;
            if(!isset($data[$level])) {
                $data[$level] = array();
            }
            $data[$level][] = $obj->name;
        }
        $this->set_var('data',$data);
        //rtw_debug($data);
        $this->doRender('index.php');
    }
    
    public function render_level() {
        $this->doRender('level.php');
    }

}