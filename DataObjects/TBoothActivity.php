<?
/*
* Table Definition for tBoothActivity
*/

require_once('TReservation.php');
require_once('DB/DataObject.php');

class DataObjects_TBoothActivity extends MNPDataObject {

    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    var $__table = 'tBoothActivity';                  // table name
    var $id;                              // int(6)  not_null primary_key auto_increment
    var $tStation_id;                     // int(6)  not_null
    var $date;                            // date(10)  not_null
    var $weather;                         // string(80)  
    var $currency;                        // real(12)  not_null
    var $coins;                           // real(12)  not_null
    var $ccards;                          // real(12)  not_null
    var $travChecks;                      // real(12)  not_null
    var $persChecks;                      // real(12)  not_null
    var $longOrShort;                     // real(12)  not_null
    var $comment;                         // blob(65535)  blob
    var $lastModUid;                      // int(6)  
    var $lastModDateTime;                 // datetime(19)  

    /* ZE2 compatibility trick*/
    function __clone() { return $this;}

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_TBoothActivity',$k,$v); }

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
      $names = array(
	'id'	 => 'Id',
	'comment' => 'Comments',
      	'tStation_id' => 'Reporting Station',
	'date' => 'Date',
	'weather' => 'Weather',
	'currency' => 'Currency',
	'coins' => 'Coins',
	'ccards' => 'Credit Cards',
	'travChecks' => 'Traveller Checks',
	'persChecks' => 'Personal Checks',
	'longOrShort' => 'Cash Over (+) or Short (-)');
      return key_exists($fieldname, $names) ? $names[$fieldname] : null;
    }

    /**
     * @access public
     * @param  string fieldname
     * @return boolean
     */
/*
    function requiredField ($fieldname)
    {
      static $fields = array('currency', 'coins', 
			     'ccards', 'travChecks', 'persChecks'); 
      return in_array($fieldname, $fields);
    }
*/

    /**
     * return a display string
     *
     * @access public
     * @param string fieldname
     * @param string default value
     * @return string HTML display fragment or null
     */
    function makeDisplayItem ($fieldname, $value) 
    {
      switch ($fieldname) {
      case 'currency':
      case 'coins':
      case 'ccards':
      case 'travChecks':
      case 'persChecks':
      case 'longOrShort':
	return MNP::dollars($value);

      case 'tStation_id':
	return DataObjects_TStation::staticToString($value);

	/*
	//HACK FOR DISPLAY
      case 'weather':
	return $value . '<p>';
	*/

      default:
	return null;	
      }
    }


    /**
     * return an input string
     *
     * @access public
     * @param string fieldname
     * @param string default value
     * @return string HTML input fragment or null
     */
    function makeInputItem ($fieldname, $value)
    { 
      global $session_data;
      global $today;

      switch ($fieldname) {
      case 'date':		// fixed field
	return isset($value) ? $value : $today;
	break;
	
      case 'comment':
	return MNP::input_comment($fieldname, $value, 5, 55);
	break;
	
      case 'tStation_id':	// fixed field
	$sta = isset($value) ? $value : $session_data['userLocationId']; 
	return DataObjects_TStation::staticToString($sta);
	break;
	
      default:
	return null;
      }
    }

    /**
     * return js to clear a given field
     *
     * @access public
     * @param string fieldname
     * @return string javascript, or null if caller should figure it out
     */
    function makeClear ($fieldname, $formname)
    {
      switch ($fieldname) {
      case 'tStation_id':
	return '';
      default:
	return null;
      }
    }

    /**
     *  Override parent in order to snag reservation comments
     *
     * @access public
     * @return int or false
     */
    function insert()
    {
      $r = new DataObjects_TReservation;
      $rsvns = $r->getDateReservations($this->date);
      $found = 0;
      
      foreach ($rsvns as $r) {
	if (!empty($r->resComment) && strlen($r->resComment) > 0) {
	  $this->comment .=
	    "\n[Comments from Reservation {$r->resId}]\n"
	    . $r->resComment
	    . "\n";
	  $found = 1;
	}
      }
      if ($found) {
	$this->comment = "[Daily Sheet Comments]\n"
	  . $this->comment
	  . "\n";
      }
      return parent::insert();
    }


}
?>