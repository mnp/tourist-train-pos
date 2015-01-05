<?
/*
* Table Definition for tAuthGroup
*/



require_once('DB/DataObject.php');

class DataObjects_TAuthGroup extends MNPDataObject {

    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    var $__table = 'tAuthGroup';                      // table name
    var $id;                              // int(6)  not_null primary_key auto_increment
    var $gname;                           // string(50)  not_null
    var $comment;                         // string(80)  

    /* ZE2 compatibility trick*/
    function __clone() { return $this;}

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_TAuthGroup',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    /**
     * @access public
     * @return array all groups
     */
    function getAll() 
    {
      static $out;
      if (isset($out)) {
	return $out;
      }
      $g = new DataObjects_TAuthGroup;
      $g->find();
      while($g->fetch()) {
	$out[$g->id] = $g->gname;
      }
      return $out;
    }

    /**
     * @access public
     * @return string html selector
     */
    function formCode($name='groupSelect')
    {
      return MNP::selector_string($name,
				  DataObjects_TAuthGroup::getAll(), 
				  0,
				  0,
				  @$this->id);
    }  

    /**
     * @access public
     * @return string group name
     */
    function toString()
    {
      return $this->gname;
    }
}
?>