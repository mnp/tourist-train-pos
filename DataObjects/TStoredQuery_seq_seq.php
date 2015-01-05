<?
/*
* Table Definition for tStoredQuery_seq_seq
*/



require_once('DB/DataObject.php');

class DataObjects_TStoredQuery_seq_seq extends DB_DataObject {

    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    var $__table='tStoredQuery_seq_seq';            // table name
    var $id;                              // int(10)  not_null primary_key unsigned auto_increment


    /* ZE2 compatibility trick*/
    function __clone() { return $this;}



    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_TStoredQuery_seq_seq',$k,$v); }


    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
?>