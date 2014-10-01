<?php
class mod_rtw_renderer extends plugin_renderer_base {
    
    public function __construct(\moodle_page $page, $target) {
        parent::__construct($page, $target);
        define('module_static_url', $this->output->pix_url('', 'rtw'));
    }

    public function header() {
        echo $this->output->header();
    }
    
    public function footer() {
        echo $this->output->footer();
    }
    
    private function renderPage($path,$variables) {
        foreach ($variables as $variableName => $variableValue) {
            $$variableName = $variableValue;
        }
        ob_start();
        include($path);
        $var=ob_get_contents(); 
        ob_end_clean();
        return $var;
    }
    
    public function render_rtw_intro() {
        echo $this->renderPage('pages/intro.php', array());
    }
}

/*class rtw_intro implements renderable {
    public function __construct() {
    
        
    }
}*/