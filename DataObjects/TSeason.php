<?
/*
* Table Definition for tSeason
*/


require_once('DB/DataObject.php');
require_once 'TCustType.php';

class DataObjects_TSeason extends MNPDataObject {

    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    var $__table = 'tSeason';                         // table name
    var $id;                              // int(6)  not_null primary_key auto_increment
    var $comment;                         // string(80)  
    var $gA1Rate;                         // real(12)  
    var $gC1Rate;                         // real(12)  
    var $gA2Rate;                         // real(12)  
    var $gC2Rate;                         // real(12)  
    var $kA1Rate;                         // real(12)  
    var $kC1Rate;                         // real(12)  
    var $kA2Rate;                         // real(12)  
    var $kC2Rate;                         // real(12)  
    var $sA1Rate;                         // real(12)  
    var $sC1Rate;                         // real(12)  
    var $sA2Rate;                         // real(12)  
    var $sC2Rate;                         // real(12)  
    var $boxLunchRate;                    // real(12)  

    /* ZE2 compatibility trick*/
    function __clone() { return $this;}

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_TSeason',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    /**
     * niceName - give human name for a field
     *
     * @access public
     * @param  string fieldname
     * @return string nicename
     */
    function niceName ($fieldname)
    {
      $names = array('id' => 'Year',
		     'comment' => 'Comment',
		     'groupA1Rate' => '',
		     'groupC1Rate' => '',
		     'groupA2Rate' => '',
		     'groupC2Rate' => '',
		     'rackA1Rate' => '',
		     'rackC1Rate' => '',
		     'rackA2Rate' => '',
		     'rackC2Rate' => '',
		     'specialA1Rate' => '',
		     'specialC1Rate' => '',
		     'specialA2Rate' => '',
		     'specialC2Rate' => ''
	);
      return key_exists($fieldname, $names) ? $names[$fieldname] : null;
    }

    /**
     * @access public
     * @param  string fieldname
     * @return boolean
     */
    function requiredField ($fieldname)
    {
      $reqs = array('id', 
		    'groupA1Rate', 'groupC1Rate', 
		    'groupA2Rate', 'groupC2Rate', 
		    'rackA1Rate', 'rackC1Rate', 
		    'rackA2Rate', 'rackC2Rate', 
		    'specialA1Rate', 'specialC1Rate', 
		    'specialA2Rate', 'specialC2Rate');
      return in_array($fieldname, $reqs);
    }

    function staticCreate($comment, $id)
    {
      if (!isset($comment) || empty($comment)) {
	return array(0, "comment must be defined");
      }
      
      $s = new DataObjects_TSeason;
      $s->comment = $comment;
      $s->id = $id;
      return array($s->insert(), "created new season $id");      
    }

    function staticDelete($id)
    {
      $s = new DataObjects_TSeason;
      $s->get($id);
      return array($s->delete(), "deleted season $id");
    }

    /* Override delete method.  This deletes child trains and runs, so this
       could do a lot of damage... */
    function delete ($use_where=false) 
    {
      // foreach $trains ...
      // foreach $schedruns ...
      // $ret &= $train->delete($use_where ???)

      $ret = parent::delete($use_where);
      return $ret;
    }

    function getAll() 
    {
      static $out;

      if (isset($out)) {
	return $out;
      }
      $out = array();
      $season = new DataObjects_TSeason;
      $season->find();
      while($season->fetch()) {
	$out[$season->id] = $season->id; //. " - " . $season->comment;
      }    
      return $out;
    }

    /**
     * get the four rates for one type of customer
     *
     * @access public
     * @param  int $custType
     * @param  int $season
     * @return array of a1, a2, c1, c2
     */
    function getTypeRates($custType=1, $season=null)
    {
      if (0 == $custType) {
	return array(0, 0, 0, 0);
      }

      if (is_null($season)) {
	global $current_season;
	$season = $current_season;
      }
      
      $type = DataObjects_TCustType::staticGet($custType);
      assert($type);
      $s = DataObjects_TSeason::staticGet($season);
      assert($s);

      $a1 = $type->code . 'A1Rate';
      $a2 = $type->code . 'A2Rate';
      $c1 = $type->code . 'C1Rate';
      $c2 = $type->code . 'C2Rate';

      return array($s->$a1, $s->$a2, $s->$c1, $s->$c2);
    }

    /**
     * get all four rates for one season
     *
     * @access public
     * @param  int $season Null season means give empty rates (zeros)
     * @return array
     */
    function getAllRates($season)
    {
      if ($season) {
	$sea = DataObjects_TSeason::staticGet($season);
	assert($sea);
      }
      
      $rates = array('A1Rate', 'A2Rate', 'C1Rate', 'C2Rate');
      $codes = DataObjects_TCustType::getAllCodes();
      assert($codes);
      foreach ($codes as $id=>$code) {
	foreach ($rates as $rate) {
	  $slot = $code . $rate;
	  $out[$slot] = $season ? $sea->$slot : 0;
	}
      }
      return $out;
    }

    /**
     * getBoxLunchRate - 
     *
     * @static
     * @access (public|private)
     * @return {  type|objectdefinition } [ $varname ] [ description ]
     */
    function getBoxLunchRate()
    {
      global $current_season;      
      $s = DataObjects_TSeason::staticGet($current_season);
      assert($s);
      return $s->boxLunchRate;
    }


    /** @static **/
    function &formCode($name='seasonSelect', $default=null)
    {     
      global $current_season;
      return MNP::selector_string($name,
				  DataObjects_TSeason::getAll(), 
				  false,
				  true, 
				  $default);
    } 
}
?>