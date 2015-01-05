<?php
/**
 * Table Definition for states
 */
require_once 'DB/DataObject.php';

class DataObjects_States extends MNPDataObject {

    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    var $__table = 'states';                          // table name
    var $id;                              // string(2)  not_null primary_key
    var $name;                            // string(40)  not_null

    /* ZE2 compatibility trick*/
    function __clone() { return $this;}

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_States',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    function getAll() 
    {
      static $out;
      if (!isset($out)) {
	$s = new DataObjects_States;
	$s->find();
	$out = array();
	while($s->fetch()) {
	  $out[$s->id] = $s->id . " - " . $s->name;
	}
      }
      return $out;
      
    }

    function new_or_get($id=null)
    {
      if (is_null($id) && @$this->id) {
	  return $this;
      }
      
      $s = new DataObjects_States();
      $s->get($id);
      return $s;
    }

    function toString($id=null)
    {
      $s = DataObjects_States::new_or_get($id);
      return $s->id . " - " . $s->name;
    }

    // - because of how this is called, is_null is needed for id; the
    // default function arg doesn't catch this for some reason
    function formCode($name="States", $id)
    {
      return 
	MNP::selector_string($name, 
				  DataObjects_States::getAll(), 
				  0,
			     	  1,
				  is_null($id) ? "SD" : $id);
    }
}
?>