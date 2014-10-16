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
        $this->set_var('widget_player_info', $this->widget('player_info'));
        //rtw_debug($data);
        $this->doRender('index.php');
    }
    
    public function render_level() {
        global $OUTPUT;
        $level = required_param('l', PARAM_INT);
        if($level > $this->_player_info->current_level) {
            redirect('/mod/rtw/view.php?id='.$this->course_module->id.'&c=map', 'Level của bạn chưa đủ để chơi map này, hay cố gắng lên nhé :)');
        }
        $current_members = array();
        $currentgroup = groups_get_user_groups($this->course->id, $this->user->id);
        $group_id = isset($currentgroup[0][0])?$currentgroup[0][0]:'';
        if($group_id != '') {
            $groupmembers = groups_get_members($group_id, 'u.*');
            $uids = array();
            foreach ($groupmembers as $member) {
                $uids[] = $member->id;
            }
            if(!empty($uids)) {
                $rows = \mod_rtw\db\game::getInstance()->findLastGameByUids($this->course->id, $uids);
                $pos = array(0,1,2,3);
                foreach ($rows as $row) {
                    if(isset($groupmembers[$row->user_id])) {
                        $member = $groupmembers[$row->user_id];
                        $member->picture = $OUTPUT->user_picture($member, array('size'=>30));
                        $member->pos = rtw_pick_one($pos);
                        $current_members[$row->module_name][] = $member;
                    }

                }
            }
        }
        //rtw_debug($current_members);
        
        $this->set_var('widget_player_info', $this->widget('player_info'));
        $play_history = mod_rtw\db\game::getInstance()->getPlayHistory($this->_player_info->id,  $this->_player_info->current_level);
        $array_games = array();
        foreach ($play_history as $ele) {
            $array_games[$ele->module_name] = true;
        }
        $this->set_var('quests', (array)$this->_config_rtw->levels->lv1->quests);
        $this->set_var('current_members',$current_members);
        $this->set_var('array_games',$array_games);
        $this->doRender('level.php');
    }

}