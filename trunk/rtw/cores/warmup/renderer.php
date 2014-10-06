<?php
use mod_rtw\db\warmup;
use mod_rtw\core\player;
use mod_rtw\core\log;
use core\progress\null;
use mod_rtw\core\date_utils;
class mod_rtw_renderer extends mod_rtw_renderer_base {
    public function __construct(\moodle_page $page, $target) {
        parent::__construct($page, $target);
        $this->_PATH = realpath(dirname(__FILE__));
    }
    
    //Step1 - vào chức năng
    public function render_index() {
    	$this->_file = 'index.php';
    	
    	$id = optional_param('id', '', PARAM_TEXT);
    	$this->set_var('id', $_GET["id"]);
    	
    	//Khoá học
    	global $COURSE;
    	$coursename = optional_param('coursename', '', PARAM_TEXT);
    	$this->set_var('coursename', $COURSE->fullname);
    	
    	$this->doRender();
    }
    
    //Step2 - đoán thành viên nhóm mình
    public function render_guess() {
        $this->_file = 'guess.php';
        global $COURSE;
        global $DB;
        global $USER;
        
        $currentgroup=get_current_group($COURSE->id);
        $group = $DB->get_record('groups', array('id' => $currentgroup));
        
        //Bắt đầu game ----------------------
        //Bắt đầu tính giờ, xác định game đang chơi hay là chơi từ đầu
        //loại trừ trường hợp người chơi nhấn F5
        
        //-----------------------------------
        
        //Khai báo
        //$test = optional_param('test', '', PARAM_TEXT); 
        $coursename = optional_param('coursename', '', PARAM_TEXT);
        $groupname = optional_param('groupname', '', PARAM_TEXT);
        //$coursename = $course->name;
        
        //Test: 
        $this->set_var('id', $COURSE->id);
        $this->set_var('coursename', $COURSE->fullname);
        
        
        //Get id lớp hiện tại: $COURSE->id
        //Get id nhóm của mình: get_current_group($COURSE->id);
        //Get danh sách lớp
        $role = $DB->get_record('role', array('shortname' => 'student'));
        $context = get_context_instance(CONTEXT_COURSE, $COURSE->id);
        $students = get_role_users($role->id, $context);
        $this->set_var('students', $students);
        //var_dump($students);
        
        //Get danh sách nhóm, chủ yếu để test cho chức năng sau
        $groupmembers = groups_get_members($currentgroup, 'u.id, u.lastname, u.firstname, u.email', 'lastname ASC, firstname ASC');
        $this->set_var('groupmembers', $groupmembers);
        //var_dump($groupmembers);
        
        //Gán tên nhóm và số lượng thành viên nhóm
        if($groupmembers!=null)
        	$this->set_var('groupname', $group->name.' ('.count($groupmembers).' thành viên)');
        else 
        	$this->set_var('groupname',"");
        
        $this->doRender();
    }
    
    public function render_send() {
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