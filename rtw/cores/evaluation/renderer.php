<?php
use mod_rtw\db\game_proactive;
use mod_rtw\core\date_utils;
use mod_rtw\db\game;
use mod_rtw\db\player;
use mod_rtw\db\game_proactive_evaluation;
class mod_rtw_renderer extends mod_rtw_renderer_base {
    public function __construct(\moodle_page $page, $target) {
        parent::__construct($page, $target);
        $this->_PATH = realpath(dirname(__FILE__));
    }
    
    public function render_intro() {
        $this->set_var('proactive_game', required_param('proactive_game', PARAM_INT));
        $this->doRender('intro.php');
    }
    
    public function render_index() {
        $proactive_game_id = required_param('proactive_game', PARAM_INT);
        $proactive_game = game::getInstance()->findById($proactive_game_id);
        if($proactive_game == null) {
            throw new Exception(get_string('data not found', 'rtw'));
        }
        if($proactive_game->status == 2) {
            redirect('/mod/rtw/view.php?id='.$this->course_module->id.'&c=map&a=level','Yêu cầu đánh giá đã không còn hiệu lực, đang chuyển về lại bản đồ...');
        }
        $rows = game_proactive_evaluation::getInstance()->findByProactiveGameAndPlayer($proactive_game->player_id, $proactive_game->id);
        if(!empty($rows)) {
            redirect('/mod/rtw/view.php?id='.$this->course_module->id.'&c=map&a=level','Bạn đã gửi kết quả đánh giá rồi, đang chuyển về lại bản đồ...');
        }
        //Khoi tao game
        $current_game = $this->initGame(600);
        
        //unset($_SESSION['evaluation']);
        if($current_game->is_new_game == true || !isset($_SESSION['evaluation'])) {
            // Lay danh sach cau hoi theo category trong kho cau hoi moodle
            unset($_SESSION['evaluation']);
            $_SESSION['evaluation']['current_game'] = $current_game;
            $_SESSION['evaluation']['proactive_game'] = $proactive_game;
        } 
        $array = game_proactive::getInstance()->query(array('game_player_id' => $proactive_game_id));
        $this->set_var('array', $array);
        $this->doRender('index.php');
    }
    
    public function render_submit() {
        global $DB;
        $point_section_1 = optional_param('point_section_1',0, PARAM_INT);
        $point_section_2 = optional_param('point_section_2',0, PARAM_INT);
        $point_section_3 = optional_param('point_section_3',0, PARAM_INT);
        $error_message = '';
        $coin = 1;
        $this->set_var('coin', $coin);
        try {
            if($point_section_1 < 0 || $point_section_1 > 3 || $point_section_2 < 0 || $point_section_2 > 3 || $point_section_3 < 0 || $point_section_3 > 3) {
                $error_message = 'Vui lòng chỉ nhập điểm từ 0 đến 3';
                throw new Exception(get_string('data input invalid', 'rtw'));
            }
            if(!isset($_SESSION['evaluation'])) {
                $error_message = 'Bạn đã gửi đánh giá rồi';
                throw new Exception(get_string('data not found', 'rtw'));
            }
            $total_point = $point_section_1 + $point_section_2 + $point_section_3;
            $current_game = $_SESSION['evaluation']['current_game'];
            $proactive_game = $_SESSION['evaluation']['proactive_game'];
            
            $player_request = player::getInstance()->findById($proactive_game->player_id);
            if($player_request == null) {
                throw new Exception(get_string('player not found', 'rtw'));
            }
            
            $avg = 0;
            $transaction = $DB->start_delegated_transaction();
            try {
                // Luu ket qua danh gia
                game_proactive_evaluation::getInstance()->insert(array(
                    'game_player_id' => $current_game->id,
                    'game_proactive_id' => $proactive_game->id,
                    'point_section_1' => $point_section_1,
                    'point_section_2' => $point_section_2,
                    'point_section_3' => $point_section_3,
                    'total_point' => $total_point,
                    'submit_time' => date_utils::getCurrentDateSQL()
                ));
                
                // Lay 1 coin tu user gui yeu cau danh gia chuyen qua cho user danh gia
                player::getInstance()->change_coin($player_request, $proactive_game->id, 0 - $coin);
               // player::getInstance()->change_coin($this->_player_info, $current_game->id, 0 - $coin);
                \mod_rtw\core\player::getInstance()->change_coin($current_game->id, $coin);
                
                // Lay so response danh gia cho yeu cau nay
                $rows = game_proactive_evaluation::getInstance()->query(array('game_proactive_id' => $proactive_game->id));

                // Neu co tren 3 response danh gia, tinh diem trung binh
                $count = count($rows);
                if($count >= 3 ) {
                    $total = 0;
                    foreach ($rows as $row) {
                        $total += $row->total_point;
                    }
                    $avg = $total / $count;
                }
                $this->_log->log(array(__CLASS__,__FUNCTION__,'$avg='.$avg));
                // Neu diem trung binh >= 6.3 --> Cong 5 coin va 5 xp
                $num_exp = 6;
                $num_coin = 6;
                $num_ticketlottery = 1;
                if($avg >= 6.3) {
                    player::getInstance()->change_coin($player_request, $proactive_game->id, $num_coin);
                    player::getInstance()->incrExp($player_request, $proactive_game->id, $num_exp);
                    player::getInstance()->changeTurn($player_request, $num_ticketlottery,$proactive_game->id);
                    game::getInstance()->update($proactive_game->id, array('status' => 2));

                } 

                $transaction->allow_commit();
            } catch (Exception $exc) {
                $transaction->rollback($exc);
                throw new Exception(get_string('system error', 'rtw'));
            }
            $touser = player::getInstance()->getUserByPlayerId($proactive_game->player_id);
            $message = sprintf('[Proactive] Bài viết của bạn được %s đánh giá %d điểm', $this->user->firstname . ' '. $this->user->lastname, $total_point);
            rtw_send_message($this->user, $touser, $message, '');
            
            if($avg >= 6.3) {
                $message = sprintf('[Proactive] Xin chúc mừng bạn đã chinh phục thành công quest này với số điểm trung bình là %2f',$avg);
                rtw_send_message($this->user, $touser, $message, '');

                $message = sprintf('[System] Xin chúc mừng, bạn được nhận thêm %d xu, %d điểm kinh nghiệm và %d lần quay số may mắn!',$num_coin,$num_exp,$num_ticketlottery);
                rtw_send_message($this->user, $touser, $message, '');
            }
                
            
            unset($_SESSION['evaluation']);
            $this->doRender('submit.php');
        } catch (Exception $exc) {
            $this->_log->log($exc, 'error');
            if($error_message == '') {
                $error_message = 'Hệ thống đang bận, vui lòng thử lại sau';
            }
            $this->set_var('error_message', $error_message);
            $this->doRender('error.php');
        }

    }
    
}