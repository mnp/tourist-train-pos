<?
/*
* Table Definition for tTime
*/

require_once('DB/DataObject.php');

class DataObjects_TTime extends MNPDataObject {

    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    var $__table = 'tTime';                           // table name
    var $id;                              // int(6)  not_null primary_key auto_increment
    var $runTime;                         // time(8)  not_null

    /* ZE2 compatibility trick*/
    function __clone() { return $this;}

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_TTime',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
  
 
    function getAll() 
    {
      static $out;
      
      if (isset($out)) {
	return $out;
      }

      $time = new DataObjects_TTime;
      $time->find();
      while($time->fetch()) {
	$out[$time->id] = $time->toString();
      }
      return $out;
    }

    function toString()
    {
      // 00:00:00 -> 0:00 AM
      return date('h:i a', strtotime($this->runTime));

      /*
      if (preg_match('/^([0-9][0-9]):([0-9][0-9])/', $this->runTime, 
		     $matches)) {
	$hrs = intval($matches[1]);
	return $hrs > 12
	  ? sprintf("%02d:%02d pm", $matches[1] - 12, $matches[2])
	  : sprintf("%02d:%02d am", $matches[1], $matches[2]);
      }
      else {
	return $this->runTime;      
      }
      */
    }
    
    function formCode($name='timeSelect')
    {
      return MNP::selector_string($name, 
				  DataObjects_TTime::getAll(), 
				  0,
				  0,
				  (!isset($this) || @is_null($this->id))
				      ? null 
				      : $this->id);
    }    

}
?>