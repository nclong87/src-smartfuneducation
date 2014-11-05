<?php
use mod_rtw\db\game_proactive;
use mod_rtw\db\question_categories;
use mod_rtw\core\date_utils;
use mod_rtw\db\player_activities;
use mod_rtw\db\player;
class mod_rtw_renderer extends mod_rtw_renderer_base {
    public function __construct(\moodle_page $page, $target) {
        parent::__construct($page, $target);
        $this->_PATH = realpath(dirname(__FILE__));
    }
    
    public function render_intro() {
        $this->doRender('intro.php');
    }
    
    public function render_index() {
        //Khoi tao game
        $current_game = $this->initGame(2000);
        
        $num_question = 5;
        unset($_SESSION['proactive']);
        if($current_game->is_new_game == true || !isset($_SESSION['proactive'])) {
            // Lay danh sach cau hoi theo category trong kho cau hoi moodle
            unset($_SESSION['proactive']);
            $_SESSION['proactive']['current_game'] = $current_game;
            $category = question_categories::getInstance()->findCategoryByLevelAndQuest($this->_player_info->current_level, 'proactive');
            $questions = question_categories::getInstance()->get_questions_category($category,$num_question);
            $data = array(
                'game_player_id' => $current_game->id,
                'text_question' => ''
            );
            foreach ($questions as $obj) {
                $data['question_id'] = $obj->id;
                $game_proactive_id = game_proactive::getInstance()->insert($data,true);
                $obj->game_proactive_id = $game_proactive_id;
                $obj->game_player_id = $current_game->id;
                
                $_SESSION['proactive']['questions'][] = $obj;
            }
        } 
        //rtw_debug($_SESSION['proactive']['questions']);
        redirect('/mod/rtw/view.php?id='.$this->course_module->id.'&c=proactive&a=question','Đang lấy danh sách câu hỏi, vui lòng đợi trong giây lát...');
    }
    
    public function render_question() {
        $seq = optional_param('seq',0, PARAM_INT);
        $this->set_var('seq', $seq);
        $error_message = '';
        try {
            if(!isset($_SESSION['proactive']['questions'][$seq])) {
                //unset($_SESSION['proactive']);
                redirect('/mod/rtw/view.php?id='.  $this->course_module->id.'&c=proactive&a=request','Đang chuyển trang mời người chơi đánh giá trả lời của bạn...');
            }
            $question = $_SESSION['proactive']['questions'][$seq];
            if(!isset($question->show_time)) {
                $question->show_time = date_utils::getCurrentDateSQL();
                $_SESSION['proactive']['questions'][$seq] = $question;
                game_proactive::getInstance()->update($question->game_proactive_id, array('show_time' => $question->show_time));
            } 
            $this->_log->log(array(__CLASS__,__FUNCTION__,$_SESSION['proactive']['questions'][$seq]));
            $total_time = 600;
            $remain_seconds = $total_time - date_utils::getSecondsBetween(new DateTime($question->show_time), new DateTime());
            if($remain_seconds <= 0) {
                $error_message = 'Câu hỏi đã hết thời gian trả lời!';
                throw new Exception();
            }
            //unset($_SESSION['proactive']['questions'][$time]);
            $this->set_var('question', $question);            
            $this->set_var('remain_seconds', $remain_seconds);
            $this->doRender('show_question.php');
        } catch (Exception $exc) {
            $this->_log->log($exc, 'error');
            if($error_message == '') {
                $error_message = 'Hệ thống đang bận, vui lòng thử lại sau';
            }
            $this->set_var('error_message', $error_message);
            $this->doRender('error.php');
        }

    }
    
    public function render_answer() {
        $seq = optional_param('seq',0, PARAM_INT);
        $this->set_var('seq', $seq);
        $txtAnswer = required_param('txtAnswer', PARAM_TEXT);
        $error_message = '';
        try {
            $this->_log->log(array(__CLASS__,__FUNCTION__,'$time='.$seq,'$txtAnswer='.  $txtAnswer));
            if(empty($txtAnswer)) {
                $error_message = 'Bạn chưa nhập đáp án trả lời!';
                throw new Exception();
            }
            if(!isset($_SESSION['proactive']['questions'][$seq])) {
               redirect('/mod/rtw/view.php?id='.$this->course_module->id.'&c=proactive&a=question&seq='.$seq+1,'Câu hỏi không tồn tại, đang tải câu hỏi tiếp theo...',1);
            }
            $question = $_SESSION['proactive']['questions'][$seq];
            if(!isset($question->show_time)) {
                throw new Exception();
            }
            $now = new DateTime();
            $remain_seconds = date_utils::getSecondsBetween(new DateTime($question->show_time), $now);
            if($remain_seconds <= 0) {
                $error_message = 'Câu hỏi đã hết thời gian trả lời!';
                throw new Exception();
            }
            $data_update = array(
                'text_question' => $question->questiontext,
                'answer' => $txtAnswer,
                'submit_time' => $now->format(date_utils::$formatSQLDateTime)
            );
            game_proactive::getInstance()->update($question->game_proactive_id, $data_update);
            $question->answer = $txtAnswer;
            $_SESSION['proactive']['questions'][$seq] = $question;
            exit;
        } catch (Exception $exc) {
            $this->_log->log($exc, 'error');
            if($error_message == '') {
                $error_message = 'Hệ thống đang bận, vui lòng thử lại sau';
            }
            $this->set_var('error_message', $error_message);
            $this->doRender('error.php');
        }
    }
    
    public function render_request() {
        global $OUTPUT;
        $users = player_activities::getInstance()->getRecentActivity($this->course_module->id);
        $table = new html_table();
        $table->head = array('','Username','Họ tên', 'Họat động gần nhất','');
        foreach ($users as $user) {
            if($user->player_id == $this->_player_info->id) {
                continue;
            }
            if(isset($_SESSION['proactive']['request'][$user->id])) {
                $button = '<button disabled=true>Mời đánh giá</button>';
            } else {
                $button = '<button onclick="invite('.$user->id.',this)">Mời đánh giá</button>';
            }
            $table->data[] = array(
                $OUTPUT->user_picture($user, array('size'=>30)),
                $user->username,
                $user->firstname .' '. $user->lastname,
                $user->recent_act,
                $button
            );
        }
        $this->set_var('table', $table);
        $this->doRender('request.php');
    }
    
    public function render_request_user() {
        if(!isset($_SESSION['proactive']['current_game'])) {
            die('error1');
        }
        $current_game = $_SESSION['proactive']['current_game'];
        $user_id = required_param('uid', PARAM_INT);
        $touser = player::getInstance()->getUserById($user_id);
        if($touser == null) {
            die('error2');
        }
        $context_url = "/mod/rtw/view.php?id={$this->course_module->id}&c=evaluation&a=intro&proactive_game=".$current_game->id;
        rtw_send_message($this->user, $touser, get_string('proactive_message_subject', 'rtw'), $context_url);
        $_SESSION['proactive']['request'][$user_id] = true;
    }
}