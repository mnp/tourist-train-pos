<?
/*
* Table Definition for tStation
*/


require_once('DB/DataObject.php');
require_once 'MNPDataObject.php';

class DataObjects_TStation extends MNPDataObject {

    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    var $__table = 'tStation';                        // table name
    var $id;                              // int(6)  not_null primary_key auto_increment
    var $name;                            // string(40)  
    var $code;                            // string(2)  

    /* ZE2 compatibility trick*/
    function __clone() { return $this;}

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_TStation',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    function getAll() 
    {
      static $out;
      
      if (isset($out)) {
	return $out;
      }

      $out = array();
      $station = new DataObjects_TStation;
      $station->find();
      while($station->fetch()) {
	$out[$station->id] = $station->name;
      }
      return $out;
    }

    function staticToString($id) 
    {
      $s = DataObjects_TStation::staticGet($id);
      return $s->name;      
    }

    function formCode($name='stationSelect', $selected=0)
    {
      return MNP::selector_string($name,
				  DataObjects_TStation::getAll(), 
				  0,
				  0,
				  (!isset($this) || @is_null($this->id))
				      ? $selected
				      : $this->id);
    }  

    function staticFormCode($name='stationSelect', $selected=0)
    {
      return MNP::selector_string($name,
				  DataObjects_TStation::getAll(), 
				  0,
				  0,
				  $selected);
    }  
}
?>