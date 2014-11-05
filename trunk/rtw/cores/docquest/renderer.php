<?php
use mod_rtw\core\player;
use mod_rtw\core\date_utils;
use mod_rtw\db\question_categories;
use mod_rtw\db\game_docquiz;
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
        unset($_SESSION['docquest']);
        if($current_game->is_new_game == true || !isset($_SESSION['docquest'])) {
            unset($_SESSION['docquest']);
            $doc = game_docquiz::getInstance()->getRandomDoc($this->_player_info->current_level,1,'docquest');
            // rtw_debug($doc);
            if($doc == false) {
                throw new coding_exception('Doc not found');
            }
            $doc->game_player_id = $current_game->id;
            $category = question_categories::getInstance()->findCategoryByLevelAndQuest($this->_player_info->current_level, 'docquest');
            $questions = (question_categories::getInstance()->get_doc_questions($category,$doc->url));
            // rtw_debug($questions);
            $data = array(
                'docquiz_id' => $doc->id,
                'game_player_id' => $current_game->id,
                'start_time' => date_utils::getCurrentDateSQL(),
                'is_correct' => '0'
            );
            $time_rands = array();
            $start_num = 10;
            $doc->total_page = 1000;
            $i = 0;
            $div = intval($doc->total_page / $num_question);
            foreach ($questions as $obj) {
                $data['question_id'] = $obj->id;
                $game_docquiz_id = game_docquiz::getInstance()->insert($data,true);
                $obj->game_docquiz_id = $game_docquiz_id;
                $obj->game_player_id = $current_game->id;
                $start_num = $i * $div;
                if(is_number($obj->name)) {
                    $rand_num = intval($obj->name);
                } else {
                    $rand_num = rand($start_num + 10, $start_num + $div);
                }
                //$start_num+=10;
                $time_rands[] = $rand_num;
                $_SESSION['docquest']['questions'][$rand_num][] = $obj;
                $i++;
            }
            $_SESSION['docquest']['time_rands'] = $time_rands;
            $_SESSION['docquest']['doc'] = $doc;
        } else {
            //$doc = game_docquest::getInstance()->getDocByPlayerGameId($current_game->id);
            $doc = $_SESSION['docquest']['doc'];
            $time_rands = $_SESSION['docquest']['time_rands'];
        }
        $this->set_var('doc', $doc);
        //sort($time_rands);
        $this->set_var('max_num', $doc->total_page);
        $this->set_var('rands', json_encode($time_rands));
        $this->_file = 'index.php';
        $this->doRender();
    }
    
    
    public function render_question() {
        $page = required_param('page', PARAM_INT);
        if(!isset($_SESSION['docquest']['questions'][$page])) {
            return;
        }
        $questions = (array)$_SESSION['docquest']['questions'][$page];
        $question_index = rand(0, sizeof($questions) -1 );
        $_SESSION['docquest']['question_index'] = $question_index;
        // rtw_debug($questions);
        $question = $questions[$question_index];
        if(!isset($question->show_time)) {
            $question->show_time = date_utils::getCurrentDateSQL();
            $_SESSION['docquest']['questions'][$page][$question_index] = $question;
            game_docquiz::getInstance()->update($question->game_docquiz_id, array('show_time' => $question->show_time));
        } 
        $this->_log->log(array(__CLASS__,__FUNCTION__,$_SESSION['docquest']['questions'][$page]));
        $total_time = 20;
        $remain_seconds = $total_time - date_utils::getSecondsBetween(new DateTime($question->show_time), new DateTime());
        if($remain_seconds <= 0) {
            //$this->set_var('error_message', 'Câu hỏi đã hết thời gian trả lời!');
            //$this->doRender('error.php');
            return;
        }
        //unset($_SESSION['docquest']['questions'][$page]);
        $this->set_var('question', $question);
        $is_multichoice = false;
        foreach ($question->options->answers as $obj) {
            if($obj->fraction == 1) {
                $is_multichoice = false;
                break;
            }
        }
        $this->set_var('is_multichoice', $is_multichoice);
        $this->set_var('time', $page);
        $this->set_var('remain_seconds', $remain_seconds);
        $this->_file = 'show_question.php';
        $this->doRender();
    }
    
    public function render_answer() {
        $page = required_param('time', PARAM_INT);
        $answers = optional_param_array('options',array(), PARAM_INT);
        $error_message = '';
        try {
            $this->_log->log(array(__CLASS__,__FUNCTION__,'$page='.$page,'$answers='.  json_encode($answers)));
            if(empty($answers)) {
                $error_message = 'Bạn chưa chọn đáp án!';
                throw new Exception();
            }
            if(!isset($_SESSION['docquest']['questions'][$page])) {
                throw new Exception();
            }
            if(!isset($_SESSION['docquest']['question_index'])) {
                $question_index = 0;
            }else{
                $question_index = $_SESSION['docquest']['question_index'];
            }
            $questions = (array) $_SESSION['docquest']['questions'][$page];
            // rtw_debug($questions);
            $question = $questions[$question_index];
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
            game_docquiz::getInstance()->update($question->game_docquiz_id, $data_update);
            unset($_SESSION['docquest']['questions'][$page]);
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