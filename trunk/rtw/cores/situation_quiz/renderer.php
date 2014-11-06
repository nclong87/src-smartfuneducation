<?php
use mod_rtw\db\game;
use mod_rtw\core\player;
use mod_rtw\core\date_utils;
use mod_rtw\db\question_categories;
use mod_rtw\db\game_situation_quiz;

//TODO: This class is too long, any idea to break it smaller?
//TODO: Any solution to put these private methods in the same place then we can use it in controller without duplicating code?
class mod_rtw_renderer extends mod_rtw_renderer_base {    
    const QUIZ_CATEGORY = "situation_quiz";
    const NUM_QUESTIONS = 1;
    const TIME_PER_GAME = 500;
    
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
        $game = array(
            'question_id' => $question->id,
            'game_player_id' => $game_player_id,
            'start_time' => date_utils::getCurrentDateSQL(),
        );
        
        $game_id = game_situation_quiz::getInstance()->insert($game, true);

        // push the questions to $_SESSION
        $question->game_id = $game_id;
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
    private function prepare_new_game($level, $cat, $num_questions=3, $time=self::TIME_PER_GAME) {
        $current_game = $this->initGame($time);

        if ($current_game->is_new_game || !isset($_SESSION[$cat])) {
            list($category, $questions) = $this->get_questions($level, $cat, self::NUM_QUESTIONS);

            foreach ($questions as $question) {
                $this->create_game_entry($cat, $question, $current_game->id);
            }
        }
    }
   
    /**
     * Render index page, which generating questions and redirect to question page     
     **/
    public function render_index() {
        $this->prepare_new_game($this->_player_info->current_level, self::QUIZ_CATEGORY);
        redirect('/mod/rtw/view.php?id='.$this->course_module->id.'&c=situation_quiz&a=question','Đang lấy danh sách câu hỏi, vui lòng đợi trong giây lát...');
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
     * @param  str   $cat   Category
     * @param  int   $i  Question number
     *
     * @return $question  Question obj with showtime is setted
     **/
    private function set_show_time($cat, $i) {
        $question = $_SESSION[$cat]['questions'][$i];
        
        if (!isset($question->show_time)) {
            $question->show_time = date_utils::getCurrentDateSQL();
            $_SESSION[$cat]['questions'][$i] = $question;
            game_situation_quiz::getInstance()->update($question->game_id, array('show_time' => $question->show_time));
        }

        return $question;
    }

    /**
     * Get current question which is sticked with $seq
     *
     * @param  int  $seq  index of question 
     * @return mixed  $question  The question with index $seq in $_SESSION
     **/
    private function get_current_question($seq) {
        if (!isset($_SESSION[self::QUIZ_CATEGORY]['questions'][$seq])) {
            redirect('/mod/rtw/view.php?id='.$this->course_module->id.'&c=situation_quiz&a=question&seq='.$seq+1,'Câu hỏi không tồn tại, đang tải câu hỏi tiếp theo...',1);                
        }
        return $_SESSION[self::QUIZ_CATEGORY]['questions'][$seq];
     
    }
    /**
     * Get hint from question
     *
     * @param  mixed  $question  Question object
     * @return  str  $hint  Question hint, return empty string if there's no hint
     **/
    private function get_hint($question) {
        if (!empty($question->options->hints)) {
            $hint = $question->options->hints[0];
        } else {
            $hint = "";
        }
        return $hint;        
    }
    
    /**
     * Check if question's show_time is set
     *
     * @param  mixed  $question  Question object
     * @throw  Exception  If show_time is not set
     * @return bool  True if show_time is set
     **/
    private function check_question_show_time($question) {
        if (!isset($question->show_time)) {
            throw new Exception();           
        }
        return true;
    }
    
    /**
     * Render evaluation page
     **/
    public function render_evaluation() {
        $seq = optional_param('seq',0, PARAM_INT);
        
        try {
            if (isset($_POST['essay']) && !empty($_POST['essay'])) {
                $essay = $_POST['essay'];
            } else {
                throw new Exception('Bạn chưa nhập câu trả lời');
            }

            $this->_log->log(array(__CLASS__,__FUNCTION__,$_SESSION[self::QUIZ_CATEGORY]['questions'][$seq]));
            
            $question = $this->get_current_question($seq);
            $show_time_is_set = $this->check_question_show_time($question);

            // FIXME: Refactor this function 
            $now = new DateTime();
            $remain_seconds = date_utils::getSecondsBetween(new DateTime($question->show_time), $now);
            if($remain_seconds <= 0) {                
                $error_message = 'Câu hỏi đã hết thời gian trả lời!';
                throw new Exception();
            }
    
            $this->set_var('seq', $seq);
            $this->set_var('essay', $essay);
            $this->set_var('question', $question);
            $this->set_var('hint', $this->get_hint($question));
            $this->doRender('show_evaluation.php');
        } catch (Exception $e) {
            $this->_log->log($e, 'error');
            $error_message = $e->getMessage() ? $e->getMessage() : 'Hệ thống đang bận, xin vui lòng thử lại sau';
            $this->set_var('error_message', $error_message);
            $this->doRender('error.php');            
        }
    }
    /**
     * Render question page
     **/
    public function render_question() {
        $seq = optional_param('seq',0, PARAM_INT);
        $this->set_var('seq', $seq);        
        
        try {
            if(!isset($_SESSION[self::QUIZ_CATEGORY]['questions'][$seq])) {
                unset($_SESSION[self::QUIZ_CATEGORY]);
                redirect('/mod/rtw/view.php?id='.  $this->course_module->id.'&c=situation_quiz&a=intro','Câu hỏi cho lượt chơi này đã hết, vui lòng bắt đầu lại',5);
            }
        
            $question = $this->set_show_time(self::QUIZ_CATEGORY, $seq);            
            $this->_log->log(array(__CLASS__,__FUNCTION__,$_SESSION[self::QUIZ_CATEGORY]['questions'][$seq]));
            
            $remain_seconds = $this->calculate_remaining_time($question->show_time);
            $this->set_Var('remain_seconds', $remain_seconds);
            $this->set_Var('question', $question);
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
     * Check user self-evaluation answer, it should be in range 1..3
     *
     * @param  int  $answer  User's self-evaluation answer     
     * @throw  Exception  $e  When it't bad answer (user is trying to trick, etc.), throw Exception
     **/
    private function check_answer($answer) {
        if (is_numeric($answer) && ($answer <= 3) && ($answer >=1))
            return true;
        return false;
    }
    
    /**
     * Check and render answer page
     **/
    public function render_answer() {
        // TODO: This should be refactored
        $seq = optional_param('seq',0, PARAM_INT);
        $this->set_var('seq', $seq);

        try { 
            if(empty($_POST['answer']) || empty($_POST['essay'])) {
                throw new Exception('Bạn chưa chọn đáp án trả lời!');
            } else {                
                $answer = $_POST['answer'];
                $essay = $_POST['essay'];
            }

            $question = $this->get_current_question($seq);

            if(!isset($question->show_time)) {
                throw new Exception();
            }
            
            $this->_log->log(array(__CLASS__,__FUNCTION__,'$time='.$seq,'$answer='.  json_encode($answer)));
            
            if(!isset($_SESSION[self::QUIZ_CATEGORY]['questions'][$seq])) {
               redirect('/mod/rtw/view.php?id='.$this->course_module->id.'&c=situation_quiz&a=question&seq='.$seq+1,'Câu hỏi không tồn tại, đang tải câu hỏi tiếp theo...',1);
            }
            
            $now = new DateTime();
            $game = array('submit_time' => $now->format(date_utils::$formatSQLDateTime), 'user_answer' => $essay);
                
            if ($this->check_answer($answer)) {
                // $answer == $coin, now update user's coin and experience
                $coin_id = player::getInstance()->change_coin($question->game_player_id, $answer);
                $experience_id = player::getInstance()->incrExp($question->game_player_id, $answer);
                
                $game['coin_id'] = $coin_id;
                $game['experience_id'] = $experience_id;
                $game['self_evaluation'] = $answer;
                game_situation_quiz::getInstance()->update($question->game_id, $game);
            } else {
                game_situation_quiz::getInstance()->update($question->game_id, $game);
                throw new Exception();
            }
            
            unset($_SESSION[self::QUIZ_CATEGORY]['questions'][$seq]);
            $this->set_var('change_coin', $answer);
            $this->doRender('result.php');
            
        } catch (Exception $e) {
            $this->_log->log($e, 'error');
            $error_message = $e->getMessage() ? $e->getMessage() : 'Hệ thống đang bận, vui lòng thử lại sau';
            $this->set_var('error_message', $error_message);
            $this->doRender('error.php');
        }
    }
}