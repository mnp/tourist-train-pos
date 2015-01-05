<?
/*
* Table Definition for tMessage
*/



require_once('DB/DataObject.php');

class DataObjects_TMessage extends MNPDataObject {

    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    var $__table = 'tMessage';                        // table name
    var $id;                              // string(80)  not_null primary_key
    var $message;                         // blob(65535)  blob

    /* ZE2 compatibility trick*/
    function __clone() { return $this;}

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_TMessage',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
?>