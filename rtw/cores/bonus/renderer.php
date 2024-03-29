<?php
use mod_rtw\core\player;
use mod_rtw\core\date_utils;
use mod_rtw\db\question_categories;
use mod_rtw\db\game_quiz;
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
        $current_game = $this->initGame(300);
        
        $num_question = 1;
        //unset($_SESSION['bonus']);
        if($current_game->is_new_game == true || !isset($_SESSION['bonus'])) {
            // Lay danh sach cau hoi theo category trong kho cau hoi moodle
            unset($_SESSION['bonus']);
            $category = question_categories::getInstance()->findCategoryByLevelAndQuest($this->_player_info->current_level, 'quiz');
            $questions = question_categories::getInstance()->get_questions_category($category,$num_question);
            $data = array(
                'game_player_id' => $current_game->id,
                'start_time' => date_utils::getCurrentDateSQL(),
                'is_correct' => '0'
            );
            foreach ($questions as $obj) {
                $data['question_id'] = $obj->id;
                $game_quiz_id = game_quiz::getInstance()->insert($data,true);
                $obj->game_quiz_id = $game_quiz_id;
                $obj->game_player_id = $current_game->id;
                
                $_SESSION['bonus']['questions'][] = $obj;
            }
        } 
        redirect('/mod/rtw/view.php?id='.$this->course_module->id.'&c=bonus&a=question','Đang lấy danh sách câu hỏi, vui lòng đợi trong giây lát...');
    }
    
    public function render_question() {
        $seq = optional_param('seq',0, PARAM_INT);
        $this->set_var('seq', $seq);
        $error_message = '';
        try {
            if(!isset($_SESSION['bonus']['questions'][$seq])) {
                unset($_SESSION['bonus']);
                redirect('/mod/rtw/view.php?id='.  $this->course_module->id.'&c=bonus&a=intro','Câu hỏi cho lượt chơi này đã hết, vui lòng bắt đầu lại',5);
            }
            $question = $_SESSION['bonus']['questions'][$seq];
            if(!isset($question->show_time)) {
                $question->show_time = date_utils::getCurrentDateSQL();
                $_SESSION['bonus']['questions'][$seq] = $question;
                game_quiz::getInstance()->update($question->game_quiz_id, array('show_time' => $question->show_time));
            } 
            $this->_log->log(array(__CLASS__,__FUNCTION__,$_SESSION['bonus']['questions'][$seq]));
            $total_time = 30;
            $remain_seconds = $total_time - date_utils::getSecondsBetween(new DateTime($question->show_time), new DateTime());
            if($remain_seconds <= 0) {
                $error_message = 'Câu hỏi đã hết thời gian trả lời!';
                throw new Exception();
            }
            //unset($_SESSION['bonus']['questions'][$time]);
            $this->set_var('question', $question);
            $is_multichoice = false;
            foreach ($question->options->answers as $obj) {
                if($obj->fraction == 1) {
                    $is_multichoice = false;
                    break;
                }
            }
            $this->set_var('is_multichoice', $is_multichoice);
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
        $answers = optional_param_array('options',array(), PARAM_INT);
        $error_message = '';
        try {
            $this->_log->log(array(__CLASS__,__FUNCTION__,'$time='.$seq,'$answers='.  json_encode($answers)));
            if(empty($answers)) {
                $error_message = 'Bạn chưa chọn đáp án trả lời!';
                throw new Exception();
            }
            if(!isset($_SESSION['bonus']['questions'][$seq])) {
               redirect('/mod/rtw/view.php?id='.$this->course_module->id.'&c=bonus&a=question&seq='.$seq+1,'Câu hỏi không tồn tại, đang tải câu hỏi tiếp theo...',1);
            }
            $question = $_SESSION['bonus']['questions'][$seq];
            if(!isset($question->show_time)) {
                throw new Exception();
            }
            $now = new DateTime();
            $remain_seconds = date_utils::getSecondsBetween(new DateTime($question->show_time), $now);
            if($remain_seconds <= 0) {
                $error_message = 'Câu hỏi đã hết thời gian trả lời!';
                throw new Exception();
            }
            $defaultmark = intval($question->defaultmark);
            $point = 0;
            foreach ($answers as $ele) {
                if(isset($question->options->answers[$ele])) {
                    $point+= $question->options->answers[$ele]->fraction;
                }
            }
            $data_update = array('submit_time' => $now->format(date_utils::$formatSQLDateTime));
            if($point == 1) { //correct
                $data_update['is_correct'] = 1;
                player::getInstance()->changeTurn(1, $question->game_player_id);
                $experience_id = player::getInstance()->incrExp($question->game_player_id, $defaultmark);
                //$data_update['coin_id'] = $coin_id;
                $data_update['experience_id'] = $experience_id;
                $this->set_var('plus_lottery_turn', 1);
                $style = 'color:green';
            } else {
                $style = 'color:red';
            }
            $this->set_var('point', $point);
            $this->set_var('style', $style);
            game_quiz::getInstance()->update($question->game_quiz_id, $data_update);
            unset($_SESSION['bonus']['questions'][$seq]);
            $this->doRender('result.php');
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