<?php
use mod_rtw\core\player;
use mod_rtw\core\date_utils;
use mod_rtw\db\question_categories;
use mod_rtw\db\game_picturequiz;
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
        
        // $num_question = 1;
        //unset($_SESSION['picturequest']);
        if($current_game->is_new_game == true || !isset($_SESSION['picturequest'])) {
            unset($_SESSION['picturequest']);
            $picture = game_picturequiz::getInstance()->getRandomPicture($this->_player_info->current_level,1,'picturequest');
            if($picture == false) {
                throw new coding_exception('Picture not found');
            }
            $picture->game_player_id = $current_game->id;
            $category = question_categories::getInstance()->findCategoryByLevelAndQuest($this->_player_info->current_level, 'picturequest');
            $questions = question_categories::getInstance()->get_picture_questions($category,$picture->code);
            $data = array(
                'picturequiz_id' => $picture->id,
                'game_player_id' => $current_game->id,
                'start_time' => date_utils::getCurrentDateSQL(),
                'is_correct' => '0'
            );
            $time_rands = array();
            // $start_num = 10;
            $picture->length = 1000;
            $i = 0;
            // $div = intval($picture->length / $num_question);
            foreach ($questions as $obj) {
                $data['question_id'] = $obj->id;
                $game_picturequiz_id = game_picturequiz::getInstance()->insert($data,true);
                $obj->game_picturequiz_id = $game_picturequiz_id;
                $obj->game_player_id = $current_game->id;
                // $start_num = $i * $div;
                // if(is_number($obj->name)) {
                //     $rand_num = intval($obj->name);
                // } else {
                //     $rand_num = rand($start_num + 10, $start_num + $div);
                // }
                // //$start_num+=10;
                // $time_rands[] = $rand_num;
                $_SESSION['picturequest']['questions'][] = $obj;
                $i++;
            }
            $_SESSION['picturequest']['time_rands'] = $time_rands;
            $_SESSION['picturequest']['picture'] = $picture;
        } else {
            //$picture = game_picturequest::getInstance()->getPictureByPlayerGameId($current_game->id);
            $picture = $_SESSION['picturequest']['picture'];
            $time_rands = $_SESSION['picturequest']['time_rands'];
        }
        $this->set_var('picture', $picture);
        // rtw_debug($picture);
        //sort($time_rands);
        $this->set_var('max_num', $picture->length);
        $this->set_var('rands', json_encode($time_rands));
        $this->_file = 'index.php';
        $this->doRender();
    }
    
    
    public function render_question() {
        // rtw_debug($_SESSION['picturequest']['questions']);
        $question = $_SESSION['picturequest']['questions'][0];
        if(!isset($question->show_time)) {
            $question->show_time = date_utils::getCurrentDateSQL();
            $_SESSION['picturequest']['questions'][0] = $question;
            game_picturequiz::getInstance()->update($question->game_picturequiz_id, array('show_time' => $question->show_time));
        } 
        $this->_log->log(array(__CLASS__,__FUNCTION__,$_SESSION['picturequest']['questions'][0]));
        $total_time = 20;
        $remain_seconds = $total_time - date_utils::getSecondsBetween(new DateTime($question->show_time), new DateTime());
        if($remain_seconds <= 0) {
            //$this->set_var('error_message', 'Câu hỏi đã hết thời gian trả lời!');
            //$this->doRender('error.php');
            return;
        }
        //unset($_SESSION['picturequest']['questions'][$time]);
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
        $this->_file = 'show_question.php';
        $this->doRender();
    }
    
    public function render_answer() {
        $answers = optional_param_array('options',array(), PARAM_INT);
        $error_message = '';
        try {
            $this->_log->log(array(__CLASS__,__FUNCTION__,'$answers='.  json_encode($answers)));
            if(empty($answers)) {
                $error_message = 'Bạn chưa chọn đáp án!';
                throw new Exception();
            }
            if(!isset($_SESSION['picturequest']['questions'][0])) {
                throw new Exception();
            }
            $question = $_SESSION['picturequest']['questions'][0];
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
            game_picturequiz::getInstance()->update($question->game_picturequiz_id, $data_update);
            // unset($_SESSION['picturequest']['questions'][0]);
            unset($_SESSION['picturequest']);
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