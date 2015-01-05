<?php
/**
 * Table Definition for tSourceType
 */


class DataObjects_TSourceType extends MNPDataObject 
{

    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    var $__table = 'tSourceType';                     // table name
    var $id;                              // int(6)  not_null primary_key auto_increment
    var $name;                            // string(80)  
    var $code;                            // string(20)  

    /* ZE2 compatibility trick*/
    function __clone() { return $this;}

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_TSourceType',$k,$v); }

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
      $s = new DataObjects_TSourceType;
      $s->orderBy('name');
      $s->find();
      while($s->fetch()) {
	$out[$s->id] = $s->name;
      }
      return $out;
    }

    /**
     * @access public
     * @return string html selector
     */
    function formCode($name='sourceSelect', $value=null)
    {
      $options = DataObjects_TSourceType::getAll();

      // Yick
      // array_unshift, used in selector_string, reorders the keys,
      // so we'll paste 'none' manually here.  We need ops[0] to
      // actually be the first element also, so a straight assign
      // won't work
      $ops2 = array(0 => 'Select a referrer...');
      foreach($options as $k=>$v) 
      {
	$ops2[$k] = $v;
      }
      
      return MNP::selector_string($name,
				  $ops2,
				  0,
				  FALSE, // offer none: did it above
				  $value);
    }  

}
?>
