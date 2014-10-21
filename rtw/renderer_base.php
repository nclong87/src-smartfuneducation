<?php
use \mod_rtw\core\log;
use mod_rtw\core\player;
use mod_rtw\db\game;
use mod_rtw\core\date_utils;
abstract class mod_rtw_renderer_base extends plugin_renderer_base {
    
    public function __construct(\moodle_page $page, $target) {
        global $cm;
        global $USER;
        global $COURSE;
        global $CONFIG_RTW;
        global $controller;
        global $action;
        parent::__construct($page, $target);
        if(!isset($_SESSION)){
            session_start();
        }
        $pix_url = $this->output->pix_url('', 'rtw');
        define('static_image_path', $pix_url.'=');
        $this->course = $COURSE;
        $this->course_module = $cm;
        $this->user = $USER;
        $this->_player_info = player::getInstance()->getPlayerInfo();
        $this->_log = log::getInstance();
        $this->_config_rtw = $CONFIG_RTW;
        $player_activities = array(
            'player_id' => $this->_player_info->id,
            'controller' =>  $controller,
            'action' => $action,
            'create_time' => \mod_rtw\core\date_utils::getCurrentDateSQL(),
            'course_module_id' => $this->course_module->id
        );
        \mod_rtw\db\player_activities::getInstance()->insert($player_activities, false, true);
        //rtw_debug((array)$this->_config_rtw->levels->lv1->quests);
    }
    protected $course;
    protected $course_module;
    protected $user;
    protected $_PATH;
    protected $_file;
    protected $_variables = array();
    protected $_player_info;
    protected $_config_rtw;
    /**
     *
     * @var log 
     */
    protected $_log;
    public function header() {
        echo $this->output->header();
        //echo '<div style="margin-top: -10px; text-align: center; margin-bottom: 10px; font-style: italic; font-size: 16px; display: inline-block;">Bạn đang có <b id="current_coin">'. number_format($this->_player_info->current_coin).'</b> xu</div>';
    }
    
    public function footer() {
        echo $this->output->footer();
    }
    
    protected function renderPage() {
        if(!isset($this->_PATH) || !isset($this->_file)) {
            throw new Exception('Error System');
        }
        $this->_variables['course'] = $this->course;
        $this->_variables['course_module'] = $this->course_module;
        $this->_variables['player_info'] = $this->_player_info;
        $this->_variables['config_rtw'] = $this->_config_rtw;
        foreach ($this->_variables as $variableName => $variableValue) {
            $$variableName = $variableValue;
        }
        ob_start();
        include($this->_PATH.'/views/'.$this->_file);
        $var=ob_get_contents(); 
        ob_end_clean();
        return $var;
    }
    
    protected function doRender($view = '') {
        if(!empty($view)) {
            $this->_file = $view;
        }
        //echo $this->header();
        echo $this->renderPage();
        //echo $this->footer();
    }
    
    protected function widget($type) {
        if(!isset($this->_PATH)) {
            throw new Exception('Error System');
        }
        if($type == 'player_info') {
            $player_info = $this->_player_info;
            $course_module = $this->course_module;
        }
        ob_start();
        include(realpath($this->_PATH.'/../common/views').'/'.$type.'.php');
        $var=ob_get_contents(); 
        ob_end_clean();
        return $var;
    }
    
    /**
     * 
     * @param int $expired_time Thoi gian (giay) co hieu luc cua game, neu het thoi gian nay se tao 1 game moi
     * @return Object id,player_id,game_id,create_time,expired_time,status
     * @throws Exception
     */
    protected function initGame($expired_time = 300) {
        
        //Lay thong tin game theo level hien tai cua nguoi choi
        $game = game::getInstance()->findByQuest('quiz', $this->_player_info->current_level);
        if($game == false) {
            throw new Exception(get_string('no_data', 'mod_rtw'));
        }
        
        //Lay game quiz gan day nhat cua nguoi choi
        $last_game = game::getInstance()->findLastGame($game->id, $this->_player_info->id);
        $is_new_game = false;
        if($last_game == false) {
            // Player chua choi game nay -> Tao game moi cho player
            $current_game = game::getInstance()->createNewGame($this->_player_info->id, $game->id, $expired_time);
            $is_new_game = true;
        } else {
            // Player da choi game nay luc $last_game->create_time;
            if(date_utils::isPast($last_game->expired_time)) {
                // Game het hieu luc -> Tao game moi cho player
                $current_game = game::getInstance()->createNewGame($this->_player_info->id, $game->id, $expired_time);
                $is_new_game = true;
            } else {
                // Game van con hieu luc -> Su dung game nay
                $current_game = $last_game;
            }
        }
        $current_game->is_new_game = $is_new_game;
        return $current_game;
    }
    
    /**
     * 
     * @param string $name
     * @param mix $value
     */
    protected function set_var($name,$value) {
        $this->_variables[$name] = $value;
    }
    
    abstract function render_index();
}

/*class rtw_intro implements renderable {
    public function __construct() {
    
        
    }
}*/