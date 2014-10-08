<?php

namespace mod_rtw\core;
use mod_rtw\core\utils;
use Exception;

class log {

    protected static $_instance = null;

    /**
     * 
     * @return \mod_rtw\core\log
     */
    public static function getInstance() {
        if (!empty(self::$_instance)) {
            return self::$_instance;
        }
        self::$_instance = new log();
        return self::$_instance;
    }

    private $process_id = null;
    private $ip;

    public function __construct() {
        $this->process_id = date("Ymd") . utils::genSecureKey();
        $this->ip = utils::getIp();
    }

    public function __destruct() {
        //$this->_db->closeConnection();
    }

    public function log($data, $type = 'info') {
        try {
            $date = date("Y-m-d	H:i:s");
            if ($type == 'error') {
                $str = '';
                if (is_array($data)) {
                    foreach ($data as $index => $item) {
                        if ($index == 0) {
                            $e = $item;
                        } else {
                            if (is_array($item)) {
                                $str.="\t" . json_encode($item);
                            } else {
                                $str.="\t{$item}";
                            }
                        }
                    }
                } else {
                    $e = $data;
                }
                $message = $e->getMessage();
                $code = $e->getCode();
                $file = $e->getFile();
                $line = $e->getLine();
                $trace = '';
                $message = "\t{code:{$code}}\t{message:{$message}}{data:{$str}}\t{file:{$file}:{$line}}\n{$trace}";
            } else {
                if (is_array($data)) {
                    $message = '';
                    foreach ($data as $item) {
                        if (is_object($item)) {
                            $item = (array) $item;
                        }
                        if (is_array($item)) {
                            $message.="\t" . json_encode($item);
                        } else {
                            $message.="\t{$item}";
                        }
                    }
                } else {
                    $message = $data;
                }
            }
            $message = "{$date}\t{$this->ip}\t{$this->process_id}\t{$type}{$message}";
            //file_put_contents('/home/localadm/logs/rtw/log.txt', $message . PHP_EOL, FILE_APPEND);
            //Core_Log::plusLog($message);
        } catch (Exception $e) {
            //Core_Utils_Notification::addAlertInfo('log', $e, 'error');
        }
    }

    public function getProcessId() {
        if (isset($this->process_id)) {
            return $this->process_id;
        }
        return '';
    }

}
