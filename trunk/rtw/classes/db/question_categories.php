<?php

namespace mod_rtw\db;

use mod_rtw\db\base;
use question_bank;


class question_categories extends base {

    protected static $_instance = null;

    /**
     * 
     * @return question_categories
     */
    public static function getInstance() {
        if (!empty(self::$_instance)) {
            return self::$_instance;
        }
        self::$_instance = new question_categories('mdl_question_categories');
        return self::$_instance;
    }

    public function __construct($tableName, $primaryKey = 'id') {
        parent::__construct($tableName, $primaryKey);
    }

    public function findCategoryByLevelAndQuest($level, $quest) {
        $level = 'LV' . $level;
        return $this->_db->get_record_sql("select cat_child.* from mdl_question_categories cat_root inner join mdl_question_categories cat_level on cat_root.id = cat_level.parent inner join mdl_question_categories cat_child on cat_level.id = cat_child.parent where cat_root.`name` = 'RTW' and cat_level.`name` = ? and cat_child.`name` = ?", array($level, $quest));
    }
    
    /**
     * Function to read all questions for category into big array
     *
     * @param int $category category number
     * @param int $numquestion
     * @param bool $noparent if true only questions with NO parent will be selected
     * @param bool $recurse include subdirectories
     * @param bool $export set true if this is called by questionbank export
     */
    function get_questions_category($category, $numquestion, $noparent = false, $recurse = true, $export = true) {
        // Build sql bit for $noparent
        $npsql = '';
        if ($noparent) {
            $npsql = " and parent='0' ";
        }

        // Get list of categories
        $categorylist = array($category->id);

        // Get the list of questions for the category
        list($usql, $params) = $this->_db->get_in_or_equal($categorylist);
        $questions = $this->_db->get_records_select('question', "category $usql $npsql", $params, 'rand()','*',0,$numquestion);

        // Iterate through questions, getting stuff we need
        $qresults = array();
        foreach ($questions as $question) {
            $question->export_process = $export;
            $qtype = question_bank::get_qtype($question->qtype, false);
            if ($export && $qtype->name() == 'missingtype') {
                // Unrecognised question type. Skip this question when exporting.
                continue;
            }
            $qtype->get_question_options($question);
            $qresults[] = $question;
        }

        return $qresults;
    }
    
    function get_video_questions($category,$video_name,$export = true) {
        // Build sql bit for $noparent
        $questions = $this->_db->get_records_sql("select * from mdl_question where category = ? and id in ( select t0.itemid from mdl_tag_instance t0 inner join mdl_tag t1 on t0.tagid = t1.id where t1.rawname = ? and t0.itemtype = 'question')", array($category->id,$video_name));
        foreach ($questions as $question) {
            $question->export_process = $export;
            $qtype = question_bank::get_qtype($question->qtype, false);
            if ($export && $qtype->name() == 'missingtype') {
                // Unrecognised question type. Skip this question when exporting.
                continue;
            }
            $qtype->get_question_options($question);
            $qresults[] = $question;
        }

        return $qresults;
    }

    function get_doc_questions($category,$doc_name,$export = true) {
        return $this->get_video_questions($category, $doc_name, $export);
        return $qresults;
    }

    function get_picture_questions($category,$picture_code,$export = true) {
        // Build sql bit for $noparent
        $questions = $this->_db->get_records_sql("select * from mdl_question where category = ? and name = ?", array($category->id,$picture_code));

        foreach ($questions as $question) {
            $question->export_process = $export;
            $qtype = question_bank::get_qtype($question->qtype, false);
            if ($export && $qtype->name() == 'missingtype') {
                // Unrecognised question type. Skip this question when exporting.
                continue;
            }
            $qtype->get_question_options($question);
            $qresults[] = $question;
        }
        // rtw_debug($qresults);
        return $qresults;
    }

}
