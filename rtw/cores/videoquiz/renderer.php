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
        
        if($is_new_game) {
            $video = game_videoquiz::getInstance()->getRandomVideo($player_info->current_level);
            if($video == false) {
                throw new coding_exception('Video not found');
            }
            $video->game_player_id = $current_game->id;
            //$quba = question_engine::make_questions_usage_by_activity('mod_rtw', \context_module::instance($this->course->id));
            //$quba->set_preferred_behaviour('');
            //question_engine::save_questions_usage_by_activity($quba);
            $category = question_categories::getInstance()->findCategoryByLevelAndQuest($player_info->current_level, 'videoquiz');
            //$quba = question_engine::load_questions_usage_by_activity(1);
            $questions = question_categories::getInstance()->get_questions_category($category,3);
            //id,videoquiz_id,game_player_id,question_id,show_time,start_time,submit_time,is_correct,coin_id
            $data = array(
                'videoquiz_id' => $video->id,
                'game_player_id' => $current_game->id,
                'start_time' => date_utils::getCurrentDateSQL(),
                'is_correct' => '0'
            );
            foreach ($questions as $obj) {
                $data['question_id'] = $obj->id;
                game_videoquiz::getInstance()->insert($data);
            }
        } else {
            $video = game_videoquiz::getInstance()->getVideoByPlayerGameId($current_game->id);
        }
        $this->set_var('video', $video);
        $num = 3;
        $rands = array();
        while(count($rands) < $num) {
            $rand_num = rand(10, $video->length);
            $rands[$rand_num] = $rand_num;
        }
        sort($rands);
        $this->set_var('max_num', $video->length);
        $this->set_var('rands', json_encode($rands));
        $this->_file = 'index.php';
        $this->doRender();
    }
    

}