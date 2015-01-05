<?php
/**
 * Table Definition for tCustType
 */

require_once 'DB/DataObject.php';

define("CUST_TYPE_RACK",    1);  // HACK - this duplicates the db, so
define("CUST_TYPE_GROUP",   2);  //  probably we don't need the db.
define("CUST_TYPE_SPECIAL", 3);  //  FIXME.

class DataObjects_TCustType extends MNPDataObject 
{

    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    var $__table = 'tCustType';                       // table name
    var $id;                              // int(6)  not_null primary_key auto_increment
    var $name;                            // string(40)  
    var $code;                            // string(20)  

    /* ZE2 compatibility trick*/
    function __clone() { return $this;}

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_TCustType',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    function getAllNames() 
    {
      static $out;

      if (isset($out)) {
	return $out;
      }
      $out = array();
      $ct = new DataObjects_TCustType;
      $ct->find();
      while($ct->fetch()) {
	$out[$ct->id] = $ct->name;
      }    
      return $out;
    }
    
    function getAllCodes()
    {
      static $out;
      if (isset($out)) {
	return $out;
      }
      $out = array();

      $ct = new DataObjects_TCustType;
      $ct->find();
      while($ct->fetch()) {
	$out[$ct->id] = $ct->code;
      }    
      return $out;
    }

    function formCode($value)
    {           
      // This is a special selector because it runs some JS to
      // paste rates when changed.

      $extras = 'onChange="return pasteRates();"';
      return MNP::selector_string('tCustType_id', 
				  DataObjects_TCustType::getAllNames(), 
				  false,	// refresh on chg
				  true, 	// offer none
				  $value,
				  $extras);
    } 

    function toHtml($id=null)
    {
      if ($id) {
	$x = new DataObjects_TCustType;
	$x->get($id);
	return $x->name;
      }
      else {
	return $this->name;
      }
    }
}
?>