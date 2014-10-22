<?php
use mod_rtw\db\game_lottery;
class mod_rtw_renderer extends mod_rtw_renderer_base {
    public function __construct(\moodle_page $page, $target) {
        parent::__construct($page, $target);
        $this->_PATH = realpath(dirname(__FILE__));
    }
    
    
    public function render_index() {
        //rtw_debug($this->_player_info);
        global $CONFIG_RTW;
        $rewardArray = array_keys((array)$CONFIG_RTW->lottery->rate);
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
        try {
            if($this->_player_info->lottery_turn < 1) {
                $response['response_message'] = 'Bạn không có lượt quay nào, vui lòng tích lũy lượt quay để tham gia quay số!';
            } else {
                $result = game_lottery::getInstance()->turn($this->_player_info->id,$current_game);
                if($result[0] != 1) {
                    $response['response_message'] = 'Kiểm tra dữ liệu thất bại, vui lòng thử lại sau!';
                } else {
                    $response['response_code'] = 1;
                    $response['response_message'] = 'Xin chúc mừng, bạn đã nhận được '.  number_format($result[1]).' xu';
                    $response['response_data'] = $result[1];
                }
            }
        } catch (Exception $exc) {
            $this->_log->log($exc, 'error');
            $response['response_message'] = 'Kiểm tra dữ liệu thất bại, vui lòng thử lại sau!';
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }
    
    public function render_test() {
        mod_rtw\core\player::getInstance()->incrTurn(1);
        rtw_debug(mod_rtw\core\player::getInstance()->getPlayerInfo());
    }
}