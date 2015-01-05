<?php
/**
 * Table Definition for tCustomer
 */
require_once 'DB/DataObject.php';
require_once 'Validate.php';
require_once 'MiniRate.php';
require_once 'HTML/Select/Common/USState.php';
require_once 'HTML/Select/Common/Country.php';
require_once 'TCustType.php';
require_once 'TSourceType.php';
require_once 'TReservation.php';

class DataObjects_TCustomer extends MNPDataObject {

    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    var $__table = 'tCustomer';                       // table name
    var $custId;                          // int(6)  not_null primary_key auto_increment
    var $last;                            // string(40)  not_null
    var $first;                           // string(40)  
    var $address1;                        // string(80)  
    var $address2;                        // string(80)  
    var $city;                            // string(40)  
    var $state;                           // string(2)  
    var $zip;                             // string(20)  
    var $country;                         // string(2)  
    var $province;                        // string(80)  
    var $phone;                           // string(40)  
    var $cell;                            // string(40)  
    var $fax;                             // string(40)  
    var $email;                           // string(80)  
    var $lodging;                         // string(80)  
    var $tCustType_id;                    // int(6)  
    var $a1Rate;                          // real(12)  
    var $a2Rate;                          // real(12)  
    var $c1Rate;                          // real(12)  
    var $c2Rate;                          // real(12)  
    var $tSourceType_id;                  // int(6)  
    var $sentInfo;                        // int(1)  
    var $custComment;                     // blob(65535)  blob
    var $created;                         // date(10)  

    /* ZE2 compatibility trick*/
    function __clone() { return $this;}

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_TCustomer',$k,$v); }

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
      $names = array('custId'	 => 'Id',
		    'first'	 => 'First',
		    'last'	 => 'Last',
		    'address1' 	 => 'Address 1',
		    'address2' 	 => 'Address 2',
		    'city'	 => 'City',
		    'state'	 => 'State',
		    'zip'	 => 'Zip',
		    'email'	 => 'Email',
		    'phone'	 => 'Phone',
		    'fax'	 => 'Fax',
		    'cell'	 => 'Cell',
		    'lodging'	 => 'Lodging',
		    'country'  	 => 'Country',
		    'province' 	 => 'Province',
		    'tCustType_id' => 'Customer Type',
		    'a1Rate'     => 'Special Rate',
		    'tSourceType_id' => 'Source',
		    'sentInfo' => 'Sent Info',
		    'custComment' => 'Comment');
      return key_exists($fieldname, $names) ? $names[$fieldname] : null;
    }
    
    /**
     * getRates - 
     *
     * @return array of float
     */
    function getRates()
    {
      return array($this->a1Rate, $this->a2Rate, $this->c1Rate, $this->c2Rate);
    }

    /**
     * Remove all reservations belonging to a customer
     *
     * @access public
     * @return boolean true, or array of errs
     */
    function deleteCustomerReservations()
    {
      $errs = true;
      $r = new DataObjects_TReservation;
      $r->whereAdd("tCustomer_id = " . $this->custId);	
      $nf = $r->find();

      while ($errs === true && $r->fetch()) {	
	$rcopy = $r;		// new copy!
	$rcopy->getLinks();	
	$errs = $rcopy->deleteRes();
      }

      return $errs;
    }

    /**
     * fixes up states and countries.
     *
     * @access public
     * @param string $mode insert or update
     */
    function _preWrite()
    {
      $this->sentInfo = $this->sentInfo == 'on';

      if ($this->tCustType_id == CUST_TYPE_RACK) {
	$rs = DataObjects_TSeason::getTypeRates(CUST_TYPE_RACK);
	list($this->a1Rate, $this->a2Rate, $this->c1Rate, $this->c2Rate) 
	  = $rs;
      }
      elseif ($this->tCustType_id == CUST_TYPE_GROUP) {
	$rs = DataObjects_TSeason::getTypeRates(CUST_TYPE_GROUP);
	list($this->a1Rate, $this->a2Rate, $this->c1Rate, $this->c2Rate) 
	  = $rs;
      }
      else {
	// For writing, we must counteract the evil setFrom, which NULLs 0's.
	if (is_null($this->a1Rate)) { $this->a1Rate = 0; }
	if (is_null($this->a2Rate)) { $this->a2Rate = 0; }
	if (is_null($this->c1Rate)) { $this->c1Rate = 0; }
	if (is_null($this->c2Rate)) { $this->c2Rate = 0; }
      }           

      if (!isset($this->state)) {
	return;			// checkRequired will catch it.
      }
      // State is set.  If country is null, then set it to US.
      if (!isset($this->country)) {
	$this->country = 'us';
      }
    }

    /**
     * @access public
     * @return boolean true for success
     */
    function deleteCustAndRes()
    {
      $errs = $this->deleteCustomerReservations();
      return ($errs === true)
	? parent::delete()
	: $errs;
    }   

    function insert() 
    {
      $this->_preWrite();
      return parent::insert();
    }

    function update($original=null) 
    {
      $this->_preWrite();
      return parent::update();  //$original
    }

    /**
     * @access public
     * @param  string fieldname
     * @return boolean 
     */
    function requiredField ($fieldname)
    {
      $reqs = array('last', 'state', 'country', 'tCustType_id',
		    'tSourceType_id');   
      return in_array($fieldname, $reqs);
    }

    /**
     * checkRequired, OVERRIDES PARENT
     *
     * @access public
     * @return TRUE or array of errors
     */
    function checkRequired()
    {
      $errs = array();
      if (!isset($this->last) || empty($this->last)) { 
	$errs[] = 'Field <b>Last</b> required'; 
      }
      if (!isset($this->tSourceType_id)) { 
	$errs[] = 'Field <b>Source</b> required'; 
      }
      if (!isset($this->tCustType_id)) { 
	$errs[] = 'Field <b>Customer Type</b> required'; 
      }
      if (empty($this->state) && empty($this->country)) { 
	$errs[] = 'Either field <b>State</b> or <b>Country</b> must be provided'; 
      }
      else if (!empty($this->state) && !empty($this->country) 
	       && $this->country != 'us') { 
	$errs[] = 'If field <b>State</b> is set, then <b>Country</b> must be United States.';
      }
      else if (empty($this->state) && $this->country == 'us') {
	$errs[] = 'Field <b>State</b> is required if <b>Country</b> is US.';
      }      

      return count($errs) == 0 ? TRUE : $errs;
    }


    /**
     * Just the customer name, actually.
     *
     * @access public
     * @return string HTML lastname[,firstname]
     */
    function toString() 
    {
      $str = $this->last;
      if (!empty($this->first)) {
	$str .= ", " . $this->first;
      }
      return $str;
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
      switch($fieldname) {
      case 'tCustType_id':
      case 'tSourceType_id':
	return "document.{$formname}.{$fieldname}.selectedIndex = 0;\n";
      case 'sentInfo':
	return "document.{$formname}.{$fieldname}.checked = false;\n";
      case 'a1Rate':
	return "document.{$formname}.a1Rate.value = '';\n"
	  . "document.{$formname}.a2Rate.value = '';\n"
	  . "document.{$formname}.c1Rate.value = '';\n"
	  . "document.{$formname}.c2Rate.value = '';\n";
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
      switch ($fieldname) {

      case 'tCustType_id':
	// Set default rates here also, if they're empty
	if ($value == CUST_TYPE_GROUP || $value == CUST_TYPE_RACK) {
	  /* HACK: stock fixed rates. */
	  $rs = DataObjects_TSeason::getTypeRates($value);
	  list($this->a1Rate, $this->a2Rate, $this->c1Rate, $this->c2Rate) = $rs;
	}
	return DataObjects_TCustType::formCode($value);

      case 'tSourceType_id':
	return DataObjects_TSourceType::formCode($fieldname, $value);

      case 'custComment':
	return MNP::input_comment($fieldname, $value, 3, 35);

      case 'state':
	$s = new HTML_Select_Common_USState();
	return $s->toHTML($fieldname, $value);

      case 'country':
	$c = new HTML_Select_Common_Country();
	return $c->toHTML($fieldname, $value);

      case 'a1Rate':		// the others will be hidden
	assert($this);	
	$r = new MiniRate($this);
	return $r->toHTML(true, $this->tCustType_id < CUST_TYPE_SPECIAL);
	
    default:
	return null;
      }
    }

    /**
     *
     * @access public
     * @param  string $fieldname
     * @param  string $value
     * @return string
     */
    function makeDisplayItem($fieldname, $value)
    {
      switch ($fieldname) {
      case 'a1Rate':		// the others will be hidden
	$r = new MiniRate($this);
	return $r->toHTML(false);
	
      case 'state':
	$s = new HTML_Select_Common_USState();	
	return $s->getName($value);

      case 'country':
	$c = new I18N_Country();	
	return $c->getName($value);	
	
      case 'tCustType_id':
	return $this->_tCustType_id->name;

      case 'tSourceType_id':
	return $this->_tSourceType_id->name;

      default:
	return null;
      }
    }
}
?>