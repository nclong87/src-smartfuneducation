<?php

namespace mod_rtw\core;
class date_utils {

    public static $formatSQLDateTime = 'Y-m-d H:i:s';
    public static $formatSQLDate = 'Y-m-d';

    public static function getCurrentDateSQL() {
        return date('Y-m-d H:i:s');
    }

    public static function displaySQLDate($sDate, $toFormat = 'd/m/Y H:i:s') {
        $date = new \DateTime($sDate);
        return $date->format($toFormat);
    }

    public static function isPast($date_time) {
        $now = new \DateTime();
        $date_time = new \DateTime($date_time);
        if($date_time < $now) {
            return true;
        }
        return false;
    }

}
