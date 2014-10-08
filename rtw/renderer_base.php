<?php
use \mod_rtw\core\log;
use mod_rtw\core\player;
abstract class mod_rtw_renderer_base extends plugin_renderer_base {
    
    public function __construct(\moodle_page $page, $target) {
        global $cm;
        global $USER;
        parent::__construct($page, $target);
        if(!isset($_SESSION)){
            session_start();
        }
        $pix_url = $this->output->pix_url('', 'rtw');
        define('module_static_url', $pix_url.'=');
        $this->course_module = $cm;
        $this->user = $USER;
        $this->_player_info = player::getInstance()->getPlayerInfo();
        $this->_log = log::getInstance();
    }
    protected $course_module;
    protected $user;
    protected $_PATH;
    protected $_file;
    protected $_variables = array();
    protected $_player_info;
    /**
     *
     * @var log 
     */
    protected $_log;
    public function header() {
        echo $this->output->header();
        echo '<div style="margin-top: -10px; text-align: center; margin-bottom: 10px; font-style: italic; font-size: 16px; display: inline-block;">Bạn đang có <b id="current_coin">'. number_format($this->_player_info->current_coin).'</b> xu</div>';
    }
    
    public function footer() {
        echo $this->output->footer();
    }
    
    protected function renderPage() {
        if(!isset($this->_PATH) || !isset($this->_file)) {
            throw new Exception('Error System');
        }
        $this->_variables['course_module'] = $this->course_module;
        $this->_variables['player_info'] = $this->_player_info;
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