<?php
use mod_rtw\db\game_lottery;
class mod_rtw_renderer extends mod_rtw_renderer_base {
    public function __construct(\moodle_page $page, $target) {
        parent::__construct($page, $target);
        $this->_PATH = realpath(dirname(__FILE__));
    }
    
    
    public function render_index() {
        //rtw_debug($this->_player_info);
        $rewardArray = array(1,2,3,4,5,6,7,8,9,10);
        shuffle($rewardArray);
        $rad = 30;
        $tmpArray = array_reverse($rewardArray);
        $rewardRadius = array();
        foreach ($tmpArray as $value) {
            $rewardRadius[$value] = $rad;
            $rad+= 30;
        }
        $this->set_var('rewardArray', $rewardArray);
        $this->set_var('rewardRadius', $rewardRadius);
        $this->set_var('widget_player_info', $this->widget('player_info'));
        $this->doRender('index.php');
    }
    
    public function render_result() {
        $current_game = $this->initGame(300);
        $response = array(
            'response_code' => 0,
            'response_message' => '',
            'response_data' => ''
        );
        $result = game_lottery::getInstance()->turn($this->_player_info->id,$current_game);
        if($result[0] != 1) {
            $response['response_message'] = 'Kiểm tra dữ liệu thất bại, vui lòng thử lại sau!';
        } else {
            $response['response_code'] = 1;
            $response['response_message'] = 'Xin chúc mừng, bạn đã nhận được '.  number_format($result[1]).' coin';
            $response['response_data'] = $result[1];
        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }
    
    public function render_test() {
        $data = mod_rtw\db\coin::getInstance()->query(array('player_game_id' => 86),true);
        $this->set_var('data', $data);
        $this->doRender('test.php');
    }
}