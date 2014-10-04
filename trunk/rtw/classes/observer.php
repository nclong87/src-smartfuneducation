<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Event observers used in forum.
 *
 * @package    mod_rtw
 * @copyright  2013 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Event observer for mod_rtw.
 */
class mod_rtw_observer {

    /**
     * Triggered via user_enrolment_deleted event.
     *
     * @param \core\event\user_enrolment_deleted $event
     */
    public static function user_enrolment_created(\core\event\user_enrolment_created $event) {
        $player = mod_rtw\db\player::getInstance()->findByCourseId($event->relateduserid, $event->courseid);
        if(!isset($player->id)) { //insert new
            $data = array(
                'user_id' => $event->relateduserid,
                'course_id' => $event->courseid,
                'current_coin' => 0,
                'current_level' => 1,
                'last_update' => mod_rtw\core\date_utils::getCurrentDateSQL()
            );
            mod_rtw\db\player::getInstance()->insert($data);
        } else { //update
            $data = array(
                'last_update' => mod_rtw\core\date_utils::getCurrentDateSQL(),
                'status' => '1'
            );
            mod_rtw\db\player::getInstance()->update($player->id,$data);
        }
    }
    
    public static function user_enrolment_deleted(\core\event\user_enrolment_deleted $event) {
        $player = mod_rtw\db\player::getInstance()->findByCourseId($event->relateduserid, $event->courseid);
        if(isset($player->id)) {
            $data = array(
                'last_update' => mod_rtw\core\date_utils::getCurrentDateSQL(),
                'status' => '0'
            );
            mod_rtw\db\player::getInstance()->update($player->id,$data);
        }
    }

}
