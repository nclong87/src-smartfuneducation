<?php

namespace mod_rtw\core;

class utils {

    public static function getIp() {
        if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            return $_SERVER["HTTP_CLIENT_IP"];
        }
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            return $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        if (isset($_SERVER["HTTP_X_FORWARDED"])) {
            return $_SERVER["HTTP_X_FORWARDED"];
        }
        if (isset($_SERVER["HTTP_FORWARDED_FOR"])) {
            return $_SERVER["HTTP_FORWARDED_FOR"];
        }
        if (isset($_SERVER["HTTP_FORWARDED"])) {
            return $_SERVER["HTTP_FORWARDED"];
        }
        if (isset($_SERVER["REMOTE_ADDR"])) {
            return $_SERVER["REMOTE_ADDR"];
        }
        return 'Undefined';
    }

    public static function genSecureKey($len = 10) {
        $key = '';
        list($usec, $sec) = explode(' ', microtime());
        mt_srand((float) $sec + ((float) $usec * 100000));
        $inputs = array_merge(range('z', 'a'), range(0, 9), range('A', 'Z'));
        for ($i = 0; $i < $len; $i++) {
            $key .= $inputs{mt_rand(0, 61)};
        }
        return $key;
    }
    
    public static function writeFile($file_name,$content) {
        file_put_contents(ROOT_DIR . '/data/'.$file_name, $content);
    }
    
    public static function array2object($array) {
        $object = new \stdClass();
        foreach ($array as $key => $value) {
            $object->{$key} = $value;
        }
        return $object;
    }
    
    /**
     * 
     * @param USER $userfrom User send message
     * @param USER $userto User receive message
     * @param String $message
     * @param String $context_url
     */
    public static function sendMessage($userfrom,$userto,$message,$context_url) {
        $eventdata = new stdClass();
        $eventdata->component         = 'moodle'; //your component name
        $eventdata->name              = 'instantmessage'; //this is the message name from messages.php
        $eventdata->userfrom          = $userfrom;
        $eventdata->userto            = $userto;
        $eventdata->subject           = $message;
        $eventdata->fullmessage       = $message;
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessagehtml   = '';
        $eventdata->smallmessage      = $message;
        $eventdata->notification      = 1;
        $eventdata->contexturl        = $context_url;
        message_send($eventdata);
    }

}
