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
    
    public static function getSecondsBetween(\DateTime $from,  \DateTime $to) {
        $retval = $from->diff($to);
        return ($retval->y * 365 * 24 * 60 * 60) +
               ($retval->m * 30 * 24 * 60 * 60) +
               ($retval->d * 24 * 60 * 60) +
               ($retval->h * 60 * 60) +
               ($retval->i * 60) +
               $retval->s;
    }
    
}
