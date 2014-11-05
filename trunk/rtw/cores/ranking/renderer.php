<?php
use mod_rtw\db\game;
use mod_rtw\core\player;
use mod_rtw\core\date_utils;
use mod_rtw\db\question_categories;
use mod_rtw\db\game_ranking;
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
        
        $num_question = 7;
        if($current_game->is_new_game == true || !isset($_SESSION['ranking'])) {
            // Lay danh sach cau hoi theo category trong kho cau hoi moodle
            unset($_SESSION['ranking']);
            $category = question_categories::getInstance()->findCategoryByLevelAndQuest($this->_player_info->current_level, 'ranking');
            $questions = question_categories::getInstance()->get_questions_category($category,$num_question);
            $data = array(
                'game_player_id' => $current_game->id,
                'start_time' => date_utils::getCurrentDateSQL(),
                'is_ranking' => 0
            );
            foreach ($questions as $obj) {
                $data['question_id'] = $obj->id;
                foreach ($obj->options->answers as $opt) {
                    $data['option_id'] = $opt->id;
                    $game_ranking_id = game_ranking::getInstance()->insert($data,true); 
                    $opt->game_ranking_id = $game_ranking_id;
                }
                $obj->game_player_id = $current_game->id;
                $_SESSION['ranking']['questions'][] = $obj;
            }
        } 
       
        redirect('/mod/rtw/view.php?id='.$this->course_module->id.'&c=ranking&a=question','Đang lấy danh sách câu hỏi, vui lòng đợi trong giây lát...');
    }
    
    public function render_question() {
        $seq = optional_param('seq',0, PARAM_INT);
        $this->set_var('seq', $seq);
        $error_message = '';
        try {
            if(!isset($_SESSION['ranking']['questions'][$seq])) {
                unset($_SESSION['ranking']);
                redirect('/mod/rtw/view.php?id='.  $this->course_module->id.'&c=ranking&a=intro','Câu hỏi cho lượt chơi này đã hết, vui lòng bắt đầu lại',5);
            }
            $question = $_SESSION['ranking']['questions'][$seq];
            if(!isset($question->show_time)) {
                $question->show_time = date_utils::getCurrentDateSQL();
                $_SESSION['ranking']['questions'][$seq] = $question;
                foreach ($question->options->answers as $opt) {
                    game_ranking::getInstance()->update($opt->game_ranking_id, array('show_time' => $question->show_time));
                }
            } 
            $this->_log->log(array(__CLASS__,__FUNCTION__,$_SESSION['ranking']['questions'][$seq]));
            $total_time = 60;
            $remain_seconds = $total_time - date_utils::getSecondsBetween(new DateTime($question->show_time), new DateTime());
            if($remain_seconds <= 0) {
                $error_message = 'Câu hỏi đã hết thời gian trả lời!';
                throw new Exception();
            }
            
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
        $option_ranking = optional_param_array('options', array(), PARAM_TEXT);
        $seq = optional_param('seq',0, PARAM_INT);
        $this->set_var('seq', $seq);
        $error_message = '';
        try {
            $this->_log->log(array(__CLASS__,__FUNCTION__,'$time='.$seq,'$option_ranking='.  json_encode($option_ranking)));
            if(empty($option_ranking)) {
                $error_message = 'Bạn chưa chọn đáp án trả lời!';
                throw new Exception();
            } 
            
            if(!isset($_SESSION['ranking']['questions'][$seq])) {
               redirect('/mod/rtw/view.php?id='.$this->course_module->id.'&c=ranking&a=question&seq='.$seq+1,'Câu hỏi không tồn tại, đang tải câu hỏi tiếp theo...',1);
            }
            $question = $_SESSION['ranking']['questions'][$seq];

            if(!isset($question->show_time)) {
                throw new Exception();
            }
            $now = new DateTime();
            $remain_seconds = date_utils::getSecondsBetween(new DateTime($question->show_time), $now);
            if($remain_seconds <= 0) {
                $error_message = 'Câu hỏi đã hết thời gian trả lời!';
                throw new Exception();
            }
            
            $coin = intval($question->defaultmark);
            $data_update = array('submit_time' => $now->format(date_utils::$formatSQLDateTime));
            $coin_id = player::getInstance()->change_coin($question->game_player_id, $coin);
            $experience_id = player::getInstance()->incrExp($question->game_player_id, $coin);
            $answer_ranking = array();
            foreach ($option_ranking as $option_id => $ele) {
                $opt = $question->options->answers[$option_id];
                $data_update['is_ranking'] = $ele;
                $data_update['coin_id'] = $coin_id;
                $data_update['experience_id'] = $experience_id;
                $this->set_var('change_coin', $coin);
                $style = 'color:green';
                $this->set_var('style', $style);
                game_ranking::getInstance()->update($opt->game_ranking_id, $data_update);
                $answer_ranking[] = $ele;
            }
            
            $this->set_var('answer_ranking', $answer_ranking);
            unset($_SESSION['ranking']['questions'][$seq]);
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