<?php
abstract class mod_rtw_renderer_base extends plugin_renderer_base {
    
    public function __construct(\moodle_page $page, $target) {
        global $cm;
        parent::__construct($page, $target);
        define('module_static_url', $this->output->pix_url('', 'rtw'));
        $this->course = $cm;
    }
    protected $course;
    protected $_PATH;
    protected $_file;
    protected $_variables = array();
    public function header() {
        echo $this->output->header();
    }
    
    public function footer() {
        echo $this->output->footer();
    }
    
    protected function renderPage() {
        if(!isset($this->_PATH) || !isset($this->_file)) {
            throw new Exception('Error System');
        }
        $this->_variables['course'] = $this->course;
        foreach ($this->_variables as $variableName => $variableValue) {
            $$variableName = $variableValue;
        }
        ob_start();
        include($this->_PATH.'/views/'.$this->_file);
        $var=ob_get_contents(); 
        ob_end_clean();
        return $var;
    }
    
    protected function doRender() {
        echo $this->renderPage();
    }
    
    abstract function render_index();
}

/*class rtw_intro implements renderable {
    public function __construct() {
    
        
    }
}*/