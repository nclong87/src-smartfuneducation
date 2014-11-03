<?php
use mod_rtw\db\game;
use mod_rtw\core\player;
use mod_rtw\core\date_utils;
use mod_rtw\db\question_categories;
use mod_rtw\db\game_quiz;

//TODO: This class is too long, any idea to break it smaller?
class mod_rtw_renderer extends mod_rtw_renderer_base {    
    const QUIZ_CATEGORY = "ma_quiz";
    const NUM_QUESTIONS = 3;
    const TIME_PER_GAME = 300;
    const EXP = 1;
    const COIN = 2;
    
    public function __construct(\moodle_page $page, $target) {
        parent::__construct($page, $target);
        $this->_PATH = realpath(dirname(__FILE__));
    }
    
    public function render_intro() {
        $this->doRender('intro.php');
    }

    /**
     * Get question fof specific category, level
     * 
     * @param  int  $level           Game level
     * @param  str  $cat             Quiz category
     * @param  int  $num_questions   Number of questions, default we get 3 
     *
     * @return
     */
    private function get_questions($level, $cat, $num_questions=3) {
        // Reset $_SESSION[$cat]
        unset($_SESSION[$cat]);
        $_SESSION[$cat]['questions'] = array();
        
        $category = question_categories::getInstance()->findCategoryByLevelAndQuest($level, $cat);
        $questions = question_categories::getInstance()->get_questions_category($category, $num_questions);
        
        return array($category, $questions);
    }

    /**
     * Create a new game entry (question), then push it to $_SESSION
     *
     * @param  str  $cat Category
     * @param  Question $questionn Question
     * @param  int  $game_player_id  Game-Player table id
     *
     * @return 
     */
    private function create_game_entry($cat, $question, $game_player_id) {
        // Create a game quiz
        $game_quiz = array(
            'question_id' => $question->id,
            'game_player_id' => $game_player_id,
            'start_time' => date_utils::getCurrentDateSQL(),
            'is_correct' => '0'
        );
        
        $game_quiz_id = game_quiz::getInstance()->insert($game_quiz, true);

        // push the questions to $_SESSION
        $question->game_quiz_id = $game_quiz_id;
        $question->game_player_id = $game_player_id;        
        array_push($_SESSION[$cat]['questions'], $question);
    }
    
    /**
     * Prepare a new game: create a new game in database, get the questions, push question information to game instance in db, 
     * and push questions to $_SESSION for rendering and check the answer
     *
     * @param  int  $level  user's current level
     * @param  str  $cat  category
     * @param  int  $num_questions Number of questiosn, default 3
     * @param  int  $time  Time per game, default 300s
     *
     * @return
     **/
    private function prepare_new_game($level, $cat, $num_questions=3, $time=300) {
        $current_game = $this->initGame($time);

        if ($current_game->is_new_game || !isset($_SESSION[$cat])) {
            list($category, $questions) = $this->get_questions($level, $cat, $num_questions);

            foreach ($questions as $question) {
                $this->create_game_entry($cat, $question, $current_game->id);
            }
        }
    }
   
    /**
     * Render index page, which generating questions and redirect to question page     
     **/
    public function render_index() {
        $this->prepare_new_game($this->_player_info->current_level, 'ma_quiz');
        redirect('/mod/rtw/view.php?id='.$this->course_module->id.'&c=ma_quiz&a=question','Đang lấy danh sách câu hỏi, vui lòng đợi trong giây lát...');
    }

    /**
     * Check if question is multiple choice or not, we won't use this guy in ma_quiz
     *
     * @param  Question  $question   Question object from database
     *
     * @return  bool  is_multiple_choice  Return true if it's a multiple choice and vice versa
     */
    private function is_multiple_choice($questions) {
        $is_multiple_choice = true;
        
        foreach ($questions->options->anwsers as $answer) {
            if ($answer->fraction == 1) {
                $is_multiple_choice = false;
                break;
            }
        }

        return $is_multiple_choice;
    }

    /**
     * Calculating the remaining time
     */
    private function calculate_remaining_time($show_time, $total_time=30) {
        $remaining_time = $total_time - date_utils::getSecondsBetween(new DateTime($show_time), new DateTime());

        if ($remaining_time <= 0) {
            throw new Exception('Câu hỏi đã hết thời gian trả lời');
        }
        return $remaining_time;        
    }

    /**
     * Set show time
     *
     * @param  str   $cat   Category, ma_quiz in our case
     * @param  int   $i  Question number
     *
     * @return $question  Question obj with showtime is setted
     **/
    private function set_show_time($cat, $i) {
        $question = $_SESSION[$cat]['questions'][$i];
        
        if (!isset($question->show_time)) {
            $question->show_time = date_utils::getCurrentDateSQL();
            $_SESSION[$cat]['questions'][$i] = $question;
            game_quiz::getInstance()->update($question->game_quiz_id, array('show_time' => $question->show_time));
        }

        return $question;
    }

    /**
     * Get all possible answers
     *
     * @param  mixed $question  Question object
     *
     * @return array  $answers   All the possible answers (tasg is stripped)
     */
    private function get_answers($question) {
        $answers = array();
        
        foreach ($question->options->subquestions as $subquestion) {
            array_push($answers, strip_tags($subquestion->answertext));
        }
        return $answers;
    }
    

    /**
     * In case of matching questions, we have many subquestions, so this method will get all the subquestions and shuffle it
     *
     * @param  mixed  $question  Question object
     *
     * @return array  $result  All the subquestion (tags is stripped)
     **/
    private function get_and_shuffle_subquestions($question) {
        $result = array();
        $subquestions = $question->options->subquestions;
        $is_shuffle = shuffle($subquestions);

        foreach ($subquestions as $subquestion) {
            array_push($result, strip_tags($subquestion->questiontext));
        }
        return $result;
    }

    /**
     * Render question page
     **/
    public function render_question() {
        $seq = optional_param('seq',0, PARAM_INT);
        $this->set_var('seq', $seq);        
        
        try {
            if(!isset($_SESSION['ma_quiz']['questions'][$seq])) {
                unset($_SESSION['ma_quiz']);
                redirect('/mod/rtw/view.php?id='.  $this->course_module->id.'&c=ma_quiz&a=intro','Câu hỏi cho lượt chơi này đã hết, vui lòng bắt đầu lại',5);
            }
            
            $question = $this->set_show_time('ma_quiz', $seq);            
            $this->_log->log(array(__CLASS__,__FUNCTION__,$_SESSION['ma_quiz']['questions'][$seq]));
            
            $remain_seconds = $this->calculate_remaining_time($question->show_time);
            $answers = $this->get_answers($question);
            $subquestions = $this->get_and_shuffle_subquestions($question);

            $this->set_var('subquestions', $subquestions);
            $this->set_var('answers', $answers);
            $this->set_var('remain_seconds', $remain_seconds);
            $this->set_var('question', $question);
            $this->doRender('show_question.php');
            
        } catch (Exception $e) {           
            $this->_log->log($e, 'error');
            
            // render error page
            $error_message = $e->getMessage() ? $e->getMessage() : 'Hệ thống đang bận, xin vui lòng thử lại sau';
            $this->set_var('error_message', $error_message);
            $this->doRender('error.php');
        }

    }

    /**
     * Check the answers
     *
     * @param  mixed  $question  Question object 
     * @param  array  $answers   User's provided answer
     *
     * @return bool  $result  True if it's correted, otherwise 
     */
    private function check_answers($question, $answers) {
        foreach ($question->options->subquestions as $subquestion) {
            if (!isset($answers[$subquestion->answertext]) ||$answers[$subquestion->answertext] != strip_tags($subquestion->questiontext)) {
                return false;
            }
        }
        return true;
    }

    private function get_correct_answers($question) {
        $correct_answers = array();
        
        foreach ($question->options->subquestions as $subquestion) {
            array_push($correct_answers, array('question' => strip_tags($subquestion->questiontext), 'answer' => strip_tags($subquestion->answertext)));
        }
        
        return $correct_answers;
    }
    /**
     * Check and render answer page
     **/
    public function render_answer() {
        // TODO: This should be refactored
        $seq = optional_param('seq',0, PARAM_INT);
        $this->set_var('seq', $seq);

        try { 
            $answers = array();
            if(empty($_POST['answers'])) {
                throw new Exception('Bạn chưa chọn đáp án trả lời!');
            } else {
                $answers = $_POST['answers'];
            }
            
            $this->_log->log(array(__CLASS__,__FUNCTION__,'$time='.$seq,'$answers='.  json_encode($answers)));
            
            if(!isset($_SESSION['ma_quiz']['questions'][$seq])) {
               redirect('/mod/rtw/view.php?id='.$this->course_module->id.'&c=ma_quiz&a=question&seq='.$seq+1,'Câu hỏi không tồn tại, đang tải câu hỏi tiếp theo...',1);
            }
            
            $question = $_SESSION['ma_quiz']['questions'][$seq];            
            if(!isset($question->show_time)) {
                throw new Exception();
            }
            
            $now = new DateTime();
            $remain_seconds = date_utils::getSecondsBetween(new DateTime($question->show_time), $now);
            if($remain_seconds <= 0) {                
                $error_message = 'Câu hỏi đã hết thời gian trả lời!';
                throw new Exception();
            }

            $is_corrected = $this->check_answers($question, $answers);
            
            if ($is_corrected) {
                $point = 1;
                $coin = 1;
            }
            else {
                $point = 0;
                $coin = 0;
            }
            
            $data_update = array('submit_time' => $now->format(date_utils::$formatSQLDateTime));
            
            if($point == 1) { //correct
                $data_update['is_correct'] = 1;
                $coin_id = player::getInstance()->change_coin($question->game_player_id, $coin);
                $experience_id = player::getInstance()->incrExp($question->game_player_id, $coin);
                $data_update['coin_id'] = $coin_id;
                $data_update['experience_id'] = $experience_id;
                $this->set_var('change_coin', $coin);
                $style = 'color:green';
            } else {
                $style = 'color:red';
            }
            
            $this->set_var('point', $point);
            $this->set_var('style', $style);
            $this->set_var('correct_answers', $this->get_correct_answers($question));
            game_quiz::getInstance()->update($question->game_quiz_id, $data_update);
            
            unset($_SESSION['ma_quiz']['questions'][$seq]);
            $this->doRender('result.php');
            
        } catch (Exception $e) {
            $this->_log->log($e, 'error');
            $error_message = $e->getMessage() ? $e->getMessage() : 'Hệ thống đang bận, vui lòng thử lại sau';
            $this->set_var('error_message', $error_message);
            $this->doRender('error.php');
        }
    }
}