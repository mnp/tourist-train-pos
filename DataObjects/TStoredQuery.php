<?
/*
* Table Definition for tStoredQuery
*/



require_once('DB/DataObject.php');

class DataObjects_TStoredQuery extends MNPDataObject {

    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    var $__table = 'tStoredQuery';                    // table name
    var $id;                              // int(6)  not_null primary_key auto_increment
    var $query;                           // blob(65535)  blob
    var $description;                     // blob(65535)  blob
    var $title;                           // string(80)  
    var $template;                        // string(80)  

    /* ZE2 compatibility trick*/
    function __clone() { return $this;}

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_TStoredQuery',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    function DataObjects_TStoredQuery ()
    {
      $this->table_title = 'Reports';
      $this->niceNames   = array('id' => 'Id',
				 'title' => 'Title',
				 'description' => 'Description',
				 'template' => 'Template');
    }

}

?>