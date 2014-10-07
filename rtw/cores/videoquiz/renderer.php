<?php
use mod_rtw\db\game;
use mod_rtw\core\player;
use mod_rtw\core\date_utils;
use mod_rtw\db\question_categories;
use mod_rtw\db\game_videoquiz;
class mod_rtw_renderer extends mod_rtw_renderer_base {
    public function __construct(\moodle_page $page, $target) {
        parent::__construct($page, $target);
        $this->_PATH = realpath(dirname(__FILE__));
    }
    
    public function render_index() {
        //Lay thong tin nguoi choi
        $player_info = player::getInstance()->getPlayerInfo();
        
        //Lay thong tin game theo level hien tai cua nguoi choi
        $game = game::getInstance()->findByQuest('videoquiz', $player_info->current_level);
        if($game == false) {
            throw new Exception(get_string('no_data', 'mod_rtw'));
        }
        
        //Lay game videoquiz gan day nhat cua nguoi choi
        $last_game = game::getInstance()->findLastGame($game->id, $player_info->id);
        $is_new_game = false;
        if($last_game == false) {
            // Player chua choi game nay -> Tao game moi cho player
            $current_game = game::getInstance()->createNewGame($player_info->id, $game->id, 300);
            $is_new_game = true;
        } else {
            // Player da choi game nay luc $last_game->create_time;
            if(date_utils::isPast($last_game->expired_time)) {
                // Game het hieu luc -> Tao game moi cho player
                $current_game = game::getInstance()->createNewGame($player_info->id, $game->id, 300);
                $is_new_game = true;
            } else {
                // Game van con hieu luc -> Su dung game nay
                $current_game = $last_game;
            }
        }
        
        $num_question = 3;
        //unset($_SESSION['videoquiz']);
        if($is_new_game || !isset($_SESSION['videoquiz'])) {
            unset($_SESSION['videoquiz']);
            $video = game_videoquiz::getInstance()->getRandomVideo($player_info->current_level);
            if($video == false) {
                throw new coding_exception('Video not found');
            }
            $video->game_player_id = $current_game->id;
            //$quba = question_engine::make_questions_usage_by_activity('mod_rtw', \context_module::instance($this->course_module->id));
            //$quba->set_preferred_behaviour('');
            //question_engine::save_questions_usage_by_activity($quba);
            $category = question_categories::getInstance()->findCategoryByLevelAndQuest($player_info->current_level, 'videoquiz');
            //$quba = question_engine::load_questions_usage_by_activity(1);
            $questions = question_categories::getInstance()->get_questions_category($category,$num_question);
            //id,videoquiz_id,game_player_id,question_id,show_time,start_time,submit_time,is_correct,coin_id
            $data = array(
                'videoquiz_id' => $video->id,
                'game_player_id' => $current_game->id,
                'start_time' => date_utils::getCurrentDateSQL(),
                'is_correct' => '0'
            );
            $time_rands = array();
            $start_num = 0;
            foreach ($questions as $obj) {
                $data['question_id'] = $obj->id;
                $game_videoquiz_id = game_videoquiz::getInstance()->insert($data,true);
                $obj->game_videoquiz_id = $game_videoquiz_id;
                $obj->game_player_id = $current_game->id;
                
                //$start_num = rand($start_num + 1, $video->length - 10);
                $start_num+=10;
                $time_rands[] = $start_num;
                
                $_SESSION['videoquiz']['questions'][$start_num] = $obj;
            }
            $_SESSION['videoquiz']['time_rands'] = $time_rands;
            $_SESSION['videoquiz']['video'] = $video;
        } else {
            //$video = game_videoquiz::getInstance()->getVideoByPlayerGameId($current_game->id);
            $video = $_SESSION['videoquiz']['video'];
            $time_rands = $_SESSION['videoquiz']['time_rands'];
        }
        $this->set_var('video', $video);
        sort($time_rands);
        $this->set_var('max_num', $video->length);
        $this->set_var('rands', json_encode($time_rands));
        $this->_file = 'index.php';
        $this->doRender();
    }
    
    public function render_test() {
        global $COURSE;
        rtw_debug($_SESSION['videoquiz']);
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