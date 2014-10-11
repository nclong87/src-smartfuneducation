<?php
use mod_rtw\db\game;
use mod_rtw\core\player;
use mod_rtw\core\date_utils;
use mod_rtw\db\warmup;
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
    	
    	$currentgroup=get_current_group($COURSE->id);
    	$isgrouped = true;
    	if(empty($currentgroup))
    		$isgrouped = false;
    	
    	$isbelonggroup = optional_param('isbelonggroup', '', PARAM_BOOL);
    	$this->set_var('isbelonggroup', $isgrouped);
    	
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
        
        //Kiểm tra nếu game đã chơi xong --> chuyển qua trang kết quả
        
        //-----------------------------------------------------------
        
        //Bắt đầu game ----------------------
        //Bắt đầu tính giờ, xác định game đang chơi hay là chơi từ đầu
        //loại trừ trường hợp người chơi nhấn F5
        //Lay thong tin nguoi choi
        $player_info = player::getInstance()->getPlayerInfo();
        
        //Lay thong tin game theo level hien tai cua nguoi choi
        $game = game::getInstance()->findByQuest('warmup', $player_info->current_level);
        if($game == false) {
        	throw new Exception(get_string('no_data', 'mod_rtw'));
        }
        
        //Lay game videoquiz gan day nhat cua nguoi choi
        $last_game = game::getInstance()->findLastGame($game->id, $player_info->id);
        if($last_game == false) {
        	// Player chua choi game nay -> Tao game moi cho player
        	$current_game = game::getInstance()->createNewGame($player_info->id, $game->id, 600);
        } else {
        	// Player da choi game nay luc $last_game->create_time;
        	if(date_utils::isPast($last_game->expired_time)) {
        		// Game het hieu luc -> Tao game moi cho player
        		$current_game = game::getInstance()->createNewGame($player_info->id, $game->id, 600);
        	} else {
        		// Game van con hieu luc -> Su dung game nay
        		$current_game = $last_game;
        	}
        }
        //ta được $current_game
        //Kiểm tra xem thông tin này đã lưu chưa, dựa vào $current_game->id (player_game_id)
        $warmupRecord = $DB->get_record('rtw_game_warmup', array('player_game_id'=>$current_game->id));
        if($warmupRecord==null){
	        //Thực hiện lưu thông tin/hiệu chỉnh vào mdl_rtw_game_warmup       
	        $now = new \DateTime();
	        //$record = new stdClass();
	        //$record->player_game_id = $current_game->id;
	        //$record->group_id = $currentgroup;
	        //$record->start_time = $now->format(date_utils::$formatSQLDateTime);
	        //$lastinsertid = $DB->insert_record('rtw_game_warmup', $record, false);
	        
	        $data = array(
	        		'player_game_id' => $current_game->id,
	        		'group_id' => $currentgroup,
	        		'start_time' => $now->format(date_utils::$formatSQLDateTime)
	        );
	        warmup::getInstance()->insert($data);
        }
        $_SESSION['warmup']['current_game'] = $current_game;
        //-----------------------------------
        
        //Khai báo
        //$test = optional_param('test', '', PARAM_TEXT); 
        $coursename = optional_param('coursename', '', PARAM_TEXT);
        $groupname = optional_param('groupname', '', PARAM_TEXT);
        //$coursename = $course->name;
        
        //Test: 
        $this->set_var('id', $_GET["id"]);
        $this->set_var('coursename', $COURSE->fullname);
        
        
        //Get id lớp hiện tại: $COURSE->id
        //Get id nhóm của mình: get_current_group($COURSE->id);
        //Get danh sách lớp
        $role = $DB->get_record('role', array('shortname' => 'student'));
        $context = get_context_instance(CONTEXT_COURSE, $COURSE->id);
        $students = get_role_users($role->id, $context);
        $this->set_var('students', $students);
        //var_dump($students);
        
        //Gán tên nhóm và số lượng thành viên nhóm
        $groupmembers = groups_get_members($currentgroup, 'u.id');
        if($groupmembers!=null) {
            $this->set_var('groupname', $group->name.' ('.count($groupmembers).' thành viên)');
        } else {
            $this->set_var('groupname',"");
        }
        $remain_seconds = date_utils::getSecondsBetween(new DateTime(),new DateTime($current_game->expired_time));
        if($remain_seconds < 0) {
            $remain_seconds = 0;
        }
        $this->set_var('remain_seconds', $remain_seconds);
        
        $this->doRender();
    }
    
    public function render_send() {
        if(!isset($_SESSION['warmup']['current_game'])) {
            redirect('/mod/rtw/view.php?id=10&c=warmup', 'Bạn đã gửi dự đoán cho game này rồi, đang chuyển đến trang giới thiệu...');
        }
        
    	//Khoá học
    	global $COURSE;
    	global $DB;
    	$coursename = optional_param('coursename', '', PARAM_TEXT);
    	$this->set_var('coursename', $COURSE->fullname);
    	
    	$currentgroup=get_current_group($COURSE->id);
    	
    	//Thời điểm gửi
    	$now = new \DateTime();
    	
    	$coins = 5;
    	//Tính điểm dựa vào dự đoán -----------------------------------
    	//Kiểm tra xem có bao nhiêu câu sai
    	//Get danh sách nhóm, chủ yếu để test cho chức năng sau
    	$groupmembers = groups_get_members($currentgroup, 'u.id');
    	//Get danh sách thành viên được chọn
    	$inputs = $pieces = explode(";", $_POST["hidMembers"]);
    	//var_dump($inputs);
    	
    	$failnum = $this->fail_items_number($groupmembers, $inputs);
    	$coins = ($coins-$failnum<0)?0:$coins-$failnum;
    	//-------------------------------------------------------------
    	
    	//Số điểm sau khi trả lời
    	$coins1 = optional_param('coins1', '', PARAM_TEXT);
    	$this->set_var('coins1',$coins);
    	
    	//Lấy thời gian bắt đầu chơi để trừ điểm trễ giờ ---------------
    	$current_game = $_SESSION['warmup']['current_game'];
    	//Lấy warmup hiện tại
    	$warmupRecord = $DB->get_record('rtw_game_warmup', array('player_game_id'=>$current_game->id));
    	$starttime = $warmupRecord->start_time;
    	//echo strtotime($starttime)."<br/>";
    	//echo strtotime($now->format(date_utils::$formatSQLDateTime))."<br/>";
    	$playtime = round(abs(strtotime($now->format(date_utils::$formatSQLDateTime)) - strtotime($starttime)) / 60,2);
    	//echo $playtime."<br/>";
    	//Số điểm bị trừ trễ giờ
    	$mcoins = floor($playtime/5); //trễ 5 phút bao nhiêu lần
    	//--------------------------------------------------------------
    	
    	
    	//Điểm trừ trễ giờ
    	$coins2 = optional_param('coins2', '', PARAM_TEXT);
    	$this->set_var('coins2',$mcoins);
    	
    	//Bắt đầu chơi
    	$start = optional_param('start', '', PARAM_TEXT);
    	$this->set_var('start',$starttime);
    	
    	//Kết thúc lúc
    	$end = optional_param('end', '', PARAM_TEXT);
    	$this->set_var('end',$now->format(date_utils::$formatSQLDateTime));
    	
    	//Tổng điểm
    	$coins = ($coins-$mcoins<0)?0:$coins-$mcoins;
    	$total = optional_param('total', '', PARAM_TEXT);
    	$this->set_var('total',$coins);
    	
    	\mod_rtw\core\player::getInstance()->change_coin($current_game->id, $coins);
        $this->set_var('current_coin', number_format($this->_player_info->current_coin + $coins));
        $warmupRecord = $DB->get_record('rtw_game_warmup', array('player_game_id'=>$current_game->id));
        //$data = array(
        //	'submit_time' => $now->format(date_utils::$formatSQLDateTime),
         //   'submit_data' => serialize($inputs),
        //    'num_correct' => $total
        //);
        $record = new stdClass();
        $record->id = $warmupRecord->id;
        $record->submit_time = $now->format(date_utils::$formatSQLDateTime);
        $record->submit_data = serialize($inputs);
        $record->num_correct = $total;
        $DB->update_record('rtw_game_warmup', $record, false);
        unset($_SESSION['warmup']['current_game']);
        
        //log::getInstance()->log(array($data));
        //warmup::getInstance()->update($warmupRecord->id,$data);
        //$this->set_var('player_info', \mod_rtw\core\player::getInstance()->getPlayerInfo());
        //$this->_file = 'add.php';
        
    	
        $this->doRender('send.php');
    }
    
    private function fail_items_number($groupmembers, $inputs){
    	$failNumber = 0;
    	
    	if (isset($inputs)) {
    		foreach ($inputs as $userid) {
    			if (!empty($groupmembers)) {
    				$exist = false;
    				foreach ($groupmembers as $user) {
    					if($userid==$user->id)
    						$exist = true;
    				}
    				if($exist==false)
    					$failNumber++;
    			}else{
    				$failNumber++;
    			}
    		}
    	}
    	return $failNumber;
    }

}
