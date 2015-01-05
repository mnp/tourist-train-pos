<?php
/**
 * Table Definition for country
 */
require_once 'DB/DataObject.php';

class DataObjects_Country extends MNPDataObject {

    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    var $__table = 'country';                         // table name
    var $id;                              // string(2)  not_null primary_key multiple_key
    var $name;                            // string(80)  not_null

    /* ZE2 compatibility trick*/
    function __clone() { return $this;}

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Country',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    function getAll() 
    {
      $s = new DataObjects_Country;
      $s->find();
      $out = array();
      while($s->fetch()) {
	$out[$s->id] = $s->name;
      }    
      return $out;
    }

    function new_or_get($id=null)
    {
      if (is_null($id)) {
	if (isset($this) && isset($this->id)) {
	  return $this;
	}
      }
      $s = new DataObjects_Country();
      $s->get($id);
      return $s;
    }

    function toString($id=null)
    {
      $s = DataObjects_Country::new_or_get($id);
      return $s->id . " - " . $s->name;
    }

    // ID can come in null
    function formCode($name="country", $id=null)
    {      
      return MNP::selector_string($name, 
				  DataObjects_Country::getAll(), 
				  0,
				  1,
				  is_null($id) ? 'US' : $id);
    }
}
?>