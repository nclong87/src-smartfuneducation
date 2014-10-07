<?php
namespace mod_rtw\db;
use Exception;
use mod_rtw\core\log;

abstract class base {

    /**
     *
     * @var \moodle_database; 
     */
    protected $_db;
    protected $_tableName;
    protected $_primaryKey;
    
    /**
     *
     * @var log 
     */
    protected $_log;

    public function __construct($tableName,$primaryKey = 'id') {
        global $DB;
        $this->_tableName = $tableName;
        $this->_primaryKey = $primaryKey;
        $this->_db = $DB;
        $this->_log = log::getInstance();
    }

    public function getLastInsertId() {
        if (!isset($this->_db)) {
            throw new Exception('Error system');
        }
        $row = $this->_db->get_record_sql('SELECT LAST_INSERT_ID() as id');
        return intval($row->id);
    }
    
    /**
     * 
     * @param Long $id
     * @return Object
     */
    public function findById($id) {
        $sql = "select * from {$this->_tableName} where {$this->_primaryKey} = ?";
        $row = $this->_db->get_record_sql($sql, array($id));
        return $row == false ? null : $row;
    }

    /**
     * 
     * @param Array $data
     * @param Boolean $returnId
     * @return type
     */
    public function insert($data,$returnId = false) {
        $this->_log->log(array(__CLASS__,__FUNCTION__,  $this->_tableName,$data,'$returnId='.$returnId));
        if(empty($data)) {
            return;
        }
        $s1 = array();
        $s2 = array();
        $params = array();
        foreach ($data as $colName => $colVal) {
            $s1[] = "`$colName`";
            $s2[] = ":$colName";
            $params[$colName] = $colVal;
        }
        $sql = 'INSERT INTO `' . $this->_tableName . '`(' . join(',', $s1) . ') VALUES (' . join(',', $s2) . ')';
        $this->_db->execute($sql,$params);
        if($returnId) {
            return $this->getLastInsertId();
        }
    }

    /**
     * 
     * @param number $id
     * @param mix $data
     * @return type
     */
    public function update($id,$data) {
        $this->_log->log(array(__CLASS__,__FUNCTION__,  $this->_tableName,$data,'$id='.$id));
        if(empty($data)) {
            return;
        }
        //UPDATE answers SET word_id = :word_id, question_id = :question_id WHERE id = :id
        $params = array($this->_primaryKey => $id);
        $s = array();
        foreach ($data as $key => $value) {
            $s[] = "`{$key}` = :".$key;
            $params[$key] = $value;
        }
        $sql = "UPDATE `{$this->_tableName}` SET ".  join(',', $s)." WHERE {$this->_primaryKey} = :".$this->_primaryKey;
        $this->_db->execute($sql,$params);
    }
    
    public function query($data,$isOne = false) {
        if(empty($data)) {
            return null;
        }
        $where = ' 1 = 1 ';
        $params = array();
        foreach ($data as $key => $value) {
            if(empty($value)) {
                continue;
            }
            $where.= " AND `$key` = :$key";
            $params[$key] = $value;
        }   
        $sql = 'select * from '.$this->_tableName.' where '.$where;
        if($isOne) {
            $result = $this->_db->get_record_sql($sql, $params);
            if($result == false) {
                $result = null;
            }
        } else{
            $result = $this->_db->get_records_sql($sql, $params);
        }
        return $result;
    }
}
