<?php
use mod_rtw\core\player;
use mod_rtw\core\date_utils;
use mod_rtw\db\question_categories;
use mod_rtw\db\game_videoquiz;
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
        
        $num_question = 3;
        //unset($_SESSION['videoquiz']);
        if($current_game->is_new_game == true || !isset($_SESSION['videoquiz'])) {
            unset($_SESSION['videoquiz']);
            $video = game_videoquiz::getInstance()->getRandomVideo($this->_player_info->current_level);
            if($video == false) {
                throw new coding_exception('Video not found');
            }
            $video->game_player_id = $current_game->id;
            $category = question_categories::getInstance()->findCategoryByLevelAndQuest($this->_player_info->current_level, 'videoquiz');
            $questions = question_categories::getInstance()->get_video_questions($category,$video->url);
            $data = array(
                'videoquiz_id' => $video->id,
                'game_player_id' => $current_game->id,
                'start_time' => date_utils::getCurrentDateSQL(),
                'is_correct' => '0'
            );
            $time_rands = array();
            $start_num = 10;
            $video->length = 150;
            $i = 0;
            $div = intval($video->length / $num_question);
            foreach ($questions as $obj) {
                $data['question_id'] = $obj->id;
                $game_videoquiz_id = game_videoquiz::getInstance()->insert($data,true);
                $obj->game_videoquiz_id = $game_videoquiz_id;
                $obj->game_player_id = $current_game->id;
                $start_num = $i * $div;
                if(is_number($obj->name)) {
                    $rand_num = intval($obj->name);
                } else {
                    $rand_num = rand($start_num + 10, $start_num + $div);
                }
                //$start_num+=10;
                $time_rands[] = $rand_num;
                $_SESSION['videoquiz']['questions'][$rand_num] = $obj;
                $i++;
            }
            $_SESSION['videoquiz']['time_rands'] = $time_rands;
            $_SESSION['videoquiz']['video'] = $video;
        } else {
            //$video = game_videoquiz::getInstance()->getVideoByPlayerGameId($current_game->id);
            $video = $_SESSION['videoquiz']['video'];
            $time_rands = $_SESSION['videoquiz']['time_rands'];
        }
        $this->set_var('video', $video);
        //sort($time_rands);
        $this->set_var('max_num', $video->length);
        $this->set_var('rands', json_encode($time_rands));
        $this->_file = 'index.php';
        $this->doRender();
    }
    
    public function render_test() {
        $video = game_videoquiz::getInstance()->getRandomVideo($this->_player_info->current_level);
        $category = question_categories::getInstance()->findCategoryByLevelAndQuest($this->_player_info->current_level, 'videoquiz');
        $questions = question_categories::getInstance()->get_video_questions($category,$video->url);
        rtw_debug(array($video,$category,$questions));
        
        $this->_file = 'test.php';
        $this->doRender();
    }
    
    public function render_question() {
        $time = required_param('time', PARAM_INT);
        if(!isset($_SESSION['videoquiz']['questions'][$time])) {
            return;
        }
        $question = $_SESSION['videoquiz']['questions'][$time];
        if(!isset($question->show_time)) {
            $question->show_time = date_utils::getCurrentDateSQL();
            $_SESSION['videoquiz']['questions'][$time] = $question;
            game_videoquiz::getInstance()->update($question->game_videoquiz_id, array('show_time' => $question->show_time));
        } 
        $this->_log->log(array(__CLASS__,__FUNCTION__,$_SESSION['videoquiz']['questions'][$time]));
        $total_time = 20;
        $remain_seconds = $total_time - date_utils::getSecondsBetween(new DateTime($question->show_time), new DateTime());
        if($remain_seconds <= 0) {
            //$this->set_var('error_message', 'Câu hỏi đã hết thời gian trả lời!');
            //$this->doRender('error.php');
            return;
        }
        //unset($_SESSION['videoquiz']['questions'][$time]);
        $this->set_var('question', $question);
        $is_multichoice = false;
        foreach ($question->options->answers as $obj) {
            if($obj->fraction == 1) {
                $is_multichoice = false;
                break;
            }
        }
        $this->set_var('is_multichoice', $is_multichoice);
        $this->set_var('time', $time);
        $this->set_var('remain_seconds', $remain_seconds);
        $this->_file = 'show_question.php';
        $this->doRender();
    }
    
    public function render_answer() {
        $time = required_param('time', PARAM_INT);
        $answers = optional_param_array('options',array(), PARAM_INT);
        $error_message = '';
        try {
            $this->_log->log(array(__CLASS__,__FUNCTION__,'$time='.$time,'$answers='.  json_encode($answers)));
            if(empty($answers)) {
                $error_message = 'Bạn chưa chọn đáp án!';
                throw new Exception();
            }
            if(!isset($_SESSION['videoquiz']['questions'][$time])) {
                throw new Exception();
            }
            $question = $_SESSION['videoquiz']['questions'][$time];
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
            $point = 0;
            foreach ($answers as $ele) {
                if(isset($question->options->answers[$ele])) {
                    $point+= $question->options->answers[$ele]->fraction;
                }
            }
            $data_update = array('submit_time' => $now->format(date_utils::$formatSQLDateTime));
            if($point == 1) { //correct
                $data_update['is_correct'] = 1;
                $coin_id = player::getInstance()->change_coin($question->game_player_id, $coin);
                $data_update['experience_id'] = player::getInstance()->incrExp($question->game_player_id, $coin);
                $data_update['coin_id'] = $coin_id;
                $this->set_var('change_coin', $coin);
                $style = 'color:green';
            } else {
                $style = 'color:red';
            }
            $this->set_var('point', $point);
            $this->set_var('style', $style);
            game_videoquiz::getInstance()->update($question->game_videoquiz_id, $data_update);
            unset($_SESSION['videoquiz']['questions'][$time]);
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