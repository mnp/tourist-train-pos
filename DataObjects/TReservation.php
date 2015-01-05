<?php
/**
 * Table Definition for tReservation
 */
require_once 'DB/DataObject.php';
require_once 'TCustomer.php';
require_once 'TTrain.php';

define('RES_RESERVED',  0);
define('RES_CHECKEDIN', 1);
define('RES_RELEASED',  2);

class DataObjects_TReservation extends MNPDataObject {

    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    var $__table = 'tReservation';                    // table name
    var $resId;                           // int(6)  not_null primary_key auto_increment
    var $tCustomer_id;                    // int(6)  not_null
    var $tTrain_id_1;                     // int(6)  not_null
    var $tTrain_id_2;                     // int(6)  not_null
    var $deposit;                         // real(12)  
    var $dateReceived;                    // date(10)  
    var $amountDue;                       // real(12)  
    var $adults;                          // int(6)  
    var $children;                        // int(6)  
    var $laps;                            // int(6)  
    var $specials;                        // int(6)  
    var $escorts;                         // int(6)  
    var $resComment;                      // blob(65535)  blob
    var $lastModUid;                      // int(6)  
    var $lastModDateTime;                 // datetime(19)  
    var $a1Rate;                          // real(12)  
    var $a2Rate;                          // real(12)  
    var $c1Rate;                          // real(12)  
    var $c2Rate;                          // real(12)  
    var $walkupName;                      // string(80)  
    var $walkupState;                     // string(2)  
    var $status;                          // int(6)  
    var $walkupType;                      // int(6)  
    var $walkupCountry;                   // string(2)  
    var $boxLunches;                      // int(6)  

    /* ZE2 compatibility trick*/
    function __clone() { return $this;}

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_TReservation',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    var $train1_run;
    var $train2_run;
    var $train1_date;
    var $train2_date;

    /**
     * Walkup reservations need not have a customer id.  We might need
     * an explicit bit for this in the DB.
     *
     * @return boolean
     */
    function isWalkup()
    {
      return !isset($this->tCustomer_id) || $this->tCustomer_id==0;
    }

    /**
     * Return reservation name from walkup or by looking in cust.
     * OPTIMIZE:  Select only name column, skip db object??
     * @return string or null
     */
    function getName ()
    {
      if ($this->walkupName) {
	return $this->walkupName;
      }
      if (isset($this->tCustomer_id) && !isset($this->_tCustomer_id)) {
	$c = DataObjects_TCustomer::staticGet($this->tCustomer_id);
	return ($c) ? $c->toString() : null;
      }
      if (!empty($this->_tCustomer_id)) {
	return $this->_tCustomer_id->toString();
      }
      return null;
    }

    /**
     * Return reservation type
     *
     * @return int
     */
    function getType ()
    {
      if ($this->walkupType) {
	return $this->walkupType;
      }
      if (isset($this->_tCustomer_id)) {
	return $this->_tCustomer_id->tCustType_id;
      }
      $this->getLinks(false,true);
      if ($this->_tCustomer_id) {
	return $this->_tCustomer_id->tCustType_id;
      }
      return '(no type)';
    }

    /**
     * getTypeString - 
     *
     * @access public
     * @param  int $type
     * @return string
     */
    function typeToString($type)
    {
      switch ($type) {
      case CUST_TYPE_RACK: return 'Rack';
      case CUST_TYPE_GROUP: return 'Group';
      case CUST_TYPE_SPECIAL: return 'Special';
      default: return '-OOPS-';
      }
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
     * get all resvns on a train
     *
     * @access public
     * @param  object $train
     * @return array of res objects
     */
    function & getTrainReservations(&$train, $bothways=false, $checkedin=false)
    {
       $out = array(); 
       $r = new DataObjects_TReservation();
       $trains = "(trainId={$train->trainId} AND tTrain_id_1=trainId)";
       $ci = $checkedin ? ('status=' . RES_CHECKEDIN . ' AND ') : '';

       if ($bothways) {
	 $trains = "($trains OR (trainId={$train->trainId} AND tTrain_id_2=trainId))";
       }

       $cols = join(',', array_keys($r->_get_table()));
       $sql = "SELECT DISTINCT $cols "
       . "FROM tReservation,tTrain "
       . "WHERE $ci $trains";
       
       $r->query($sql);
 
       while ($r->fetch()) {
	 $out[] = $r;
       }
       return $out;
    }

    /**
     * get all resvns on a date
     *
     * TODO: do only one query
     *
     * @access public
     * @param  string $date
     * @return array of res objects
     */
    function & getDateReservations($date, $station=null)
    {
      $cols = join(',', array_keys($this->_get_table()));
            
      if (is_null($station)) {
	$sta  = '';
	$from = 'tReservation,tTrain';
      }
      else {      
	$sta  = "AND tSchedRun_id=runId AND tStation_id=$station";
	$from = 'tReservation,tTrain,tSchedRun';
      }
      $sql = "SELECT $cols FROM $from"
	. " WHERE (tTrain_id_1=trainId OR tTrain_id_2=trainId)"
	. " AND date='$date' $sta";   
      return $this->queryAllDataObjects($sql);
    }

    /**
     * niceName - give human name for a field
     *
     * @static
     * @access public
     * @param  string fieldname
     * @return string nicename
     */
    function niceName ($fieldname)
    {
      static $names = array('resId'	=>	'Id',
			    'tTrain_id_1' =>	'Out Train',
			    'tTrain_id_2' =>	'Ret Train',
			    'status'	=>      'Status',
			    'deposit'	=>	'Deposit $',
			    'dateReceived' =>   'Date Received',
			    'amountDue' =>      'Amount Due $',
			    'adults'	=>	'Adults',
			    'children'	=>	'Children',
			    'laps'	=>	'Laps',
			    'specials'	=>	'Specials',
			    'escorts'   =>	'Escorts',
			    'boxLunches' =>     'Box Lunches',
			    'resComment' =>	'Comment');
      return key_exists($fieldname, $names) ? $names[$fieldname] : null;
    }

    /**
     * @access public
     * @param  string fieldname
     * @return boolean
     */
    function requiredField ($fieldname)
    { return false;
    	//FIXME *********************************
      $reqs = array('tTrain_id_1');
      return in_array($fieldname, $reqs);
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
      case 'tTrain_id_1':
	return "document.{$formname}.train1_date.value = '';\n"
	  . "document.{$formname}.train1_run.selectedIndex = 0;\n";
      case 'tTrain_id_2':
	return "document.{$formname}.train2_date.value = '';\n"
	  . "document.{$formname}.train2_run.selectedIndex = 0;\n";
      case 'dateReceived':
	return "document.{$formname}.dateReceived.value = '';\n";
      case 'amountDue':
      case 'status':
	return '';		// do not try to clear these
      default:
	return null;
      }
    }

    function statusString ()
    {
      $a = array (RES_CHECKEDIN => 'Checked In',
		  RES_RESERVED  => 'Reserved',
		  RES_RELEASED  => 'Released');
      return $a[is_null($this->status) ? RES_RESERVED : $this->status];
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
      global $formname;		//HACK FIXME

      switch ($fieldname) {

      case 'deposit':
	return MNP::input_number('deposit', $value, false, 8);

      case 'status':
	// display only
	return $this->statusString();

      case 'resComment':
	return MNP::input_comment($fieldname, $value);

      case 'tTrain_id_1':
	// HACK: pass array of (run,date) if we have inputs, but bad ones.
	// this lets the user edit them.

	$t = !empty($this->_tTrain_id_1)
	  ? $this->_tTrain_id_1
	  : array($this->train1_run, $this->train1_date);	
	return DataObjects_TTrain::staticFormCode($formname,
						  "train1",
						  $t,
						  1);

      case 'tTrain_id_2':
	$extra = "<a class=\"button\" href=\"javascript:pasteTrain();\">"
	  . '&nbsp;Immediate Return&nbsp;</a>';
	$t = !empty($this->_tTrain_id_2)
	  ? $this->_tTrain_id_2
	  : array($this->train2_run, $this->train2_date);
	return DataObjects_TTrain::staticFormCode($formname,
						   "train2",
						   $t,
						   1,
						   $extra);

      case 'amountDue':
	// This is not an input, but is displayed.
	return $value
	  ? MNP::background('money', MNP::dollars($value))
          : MNP::dollars(0.0);

      case 'dateReceived':
	return MNP::date_button_string($formname, 'dateReceived',
				       isset($value) ? $value : '');

      default:
	return null;
      }
    }

    /**
     * setTrain - Force a res to have a particular train
     *
     * @access public
     * @param  int 1 or 2
     */
    function setTrain($tn, $tid)
    {
      if ($tn == 1) {
	$this->tTrain_id_1 = $tid;
	$this->_tTrain_id_1 = DataObjects_TTrain::staticGet($tid);
	$this->train1_run =  $this->_tTrain_id_1->getRunTime();
	$this->train1_date = $this->_tTrain_id_1->date;
      }
      else {
	$this->tTrain_id_2 = $tid;
	$this->_tTrain_id_2 = DataObjects_TTrain::staticGet($tid);
	$this->train2_run =  $this->_tTrain_id_2->getRunTime();
	$this->train2_date = $this->_tTrain_id_2->date;
      }
    }

    /**
     * setFrom - Fill difficult fields in object.
     *
     * @access public
     * @param array where we get our values
     * @return null
     */
    function setFrom(&$from, $finding=false)
    {
      $errs = array();
      parent::setFrom($from, $finding);
      $this->train1_date = trim(@$from['train1_date']);
      $this->train1_time = trim(@$from['train1_time']);
      $this->train2_date = trim(@$from['train2_date']);
      $this->train2_time = trim(@$from['train2_time']);

      $this->resId = (isset($from['resId']) && !empty($from['resId']))
	? $from['resId']
	: null;

      // If date and run are set, use those
      // Else if train objects are loaded, use those
      // Else leave them blank
      if (!empty($from['train1_run']) && !empty($from['train1_run'])) {
	$this->train1_run = $from['train1_run'];
	$this->train1_date = $from['train1_date'];
	$this->_tTrain_id_1 = DataObjects_TTrain::findDateRunTrain(
 	  	$from['train1_run'],
		$from['train1_date']);
	if (is_null($this->_tTrain_id_1)){
	  DataObjects_TTrain::makeTrainErrors($this->train1_run,
					      $this->train1_date,
					      'Outbound',
					      $errs);
	}
	else {	  
	  $this->tTrain_id_1 = $this->_tTrain_id_1->trainId;
	}
      }

      if (!empty($from['train2_run']) && !empty($from['train2_run'])) {
	$this->train2_run = $from['train2_run'];
	$this->train2_date = $from['train2_date'];
	$this->_tTrain_id_2 = DataObjects_TTrain::findDateRunTrain(
		$from['train2_run'],
 		$from['train2_date']);
	if (is_null($this->_tTrain_id_2)){
	  DataObjects_TTrain::makeTrainErrors($this->train2_run,
					      $this->train2_date,
					      'Return',
					      $errs);
	}
	else {	  
	  $this->tTrain_id_2 = $this->_tTrain_id_2->trainId;
	}
      }

      if (count($errs) > 0) {
	$this->errs = $errs;
      }
    }

    /**
     * @static
     * @access private
     */
    function trainCond($n, $date, $run)
    {
      $cond = '';
      if (!empty($date) || !empty($run)) {
	$cond = "tTrain_id_{$n}=trainId";
	if (!empty($run)) {
	  $cond .= " AND tSchedRun_id='$run'";
	}
	if (!empty($date)) {
	  $cond .= " AND date='$date'";
	}
      }
      
      return $cond;
    }

    /**
     * _getACLES -
     * @access private
     * @return array of fieldName=>value of only the present px types
     */
    function _getACLES ($id=null)
    {
      if (!is_null($id)) {
	$r = new DataObjects_TReservation;
	$got = $r->get($id);
	assert($got);
      }
      else {
	$r =& $this;
      }
      return array('aRes' => @$r->adults,
		   'cRes' => @$r->children,
		   'lRes' => @$r->laps,
		   'eRes' => @$r->escorts,
		   'sRes' => @$r->specials);
    }

    function aclesEqual(&$a, &$b) 
    {
      return $a['aRes'] == $b['aRes'] 
	&& $a['cRes'] == $b['cRes']
	&& $a['lRes'] == $b['lRes']	
	&& $a['eRes'] == $b['eRes']
	&& $a['sRes'] == $b['sRes'];      
    }

    /**
     * totalRiders
     *
     * @access public
     * @return int
     */
    function totalRiders()
    {
      return $this->adults + $this->children + $this->laps
	+ $this->specials + $this->escorts;
    }

    function &staticGetFull($resId) 
    {
      $r = new DataObjects_TReservation;
      if ($r->get($resId) != 1) {
	MNP::error("Could not find reservation $resId", 1);
      }
      $r->getLinks();
      if (!empty($r->_tTrain_id_1)) {
	$r->_tTrain_id_1->getLinks();
      }
      if (!empty($r->_tTrain_id_2)) {
	$r->_tTrain_id_2->getLinks();
      }
      return $r;
    }

    /**
     * Adjusts passengers on both trains in this reservation.
     *
     * The trains are locked, adjusted in memory, then saved only
     * after the numbers work.
     *
     * @access private
     * @param  int adjust mode (see TTrain)
     * @param  arrayref $errs
     * @return boolean TRUE for okay
     */
    function _AdjustBothTrains($adjust_mode, &$errs)
    {      
      // NOTE: $this contains the in-memory DESIRED RES, not the one ON DISK
      $this->getLinks(true,false);
      $newt1 = $this->_tTrain_id_1;
      $newt2 = @$this->_tTrain_id_2;

      $adding_t2 = false;
      $dropping_t2 = false;
      $switching_t1 = false;
      $switching_t2 = false;
      $new_acles = $this->_getACLES();
      $changed_trains = array();      

      // Get the OLD reservation, which is still in the DB,
      // with $THIS id and should not be changed yet.  The train id
      // in $this -MIGHT- be changed already by setFrom.  If so, we
      // need to dump the old train and add the new one.
      if ($adjust_mode == ADJUST_MODE_CHG) {
	$oldres =& DataObjects_TReservation::staticGetFull($this->resId);
	$old_acles = $oldres->_getACLES();
	$oldt1 =& $oldres->_tTrain_id_1;

	if (!empty($oldres->_tTrain_id_2)) {
	  $oldt2 =& $oldres->_tTrain_id_2;
	  $switching_t2 = ($newt2 && ($oldt2->trainId != $newt2->trainId));
	}
	else {
	  $oldt2 = null;
	}
      
	$dropping_t2  = ($oldt2 && !$newt2);
	$adding_t2    = (!$oldt2 && $newt2);
	$switching_t1 = ($newt1 && ($oldt1->trainId != $newt1->trainId));
	$changing_px  = !$this->aclesEqual($old_acles, $new_acles);

	//
	// The core change.  Remove all old px first.  Then add new ones.
	// We are only acting in memory on the trains, so if there are any 
	// errors, we simply won't save.
	//
	if ($switching_t1) {
	  $oldt1->adjustPassengers(ADJUST_MODE_DEL, $old_acles, $errs);
	  $newt1->adjustPassengers(ADJUST_MODE_ADD, $new_acles, $errs);
	  $changed_trains[] =& $oldt1;
	  $changed_trains[] =& $newt1;	  
	}
	else if ($changing_px) {
	  //	MNP::dp($newt1,'a');
	  //	MNP::dp($errs,'a');
	  $newt1->adjustPassengers(ADJUST_MODE_DEL, $old_acles, $errs);
	  //	MNP::dp($newt1,'b');
	  //	MNP::dp($errs,'b');
	  $newt1->adjustPassengers(ADJUST_MODE_ADD, $new_acles, $errs);
	  //	MNP::dp($newt1,'c');
	  //	MNP::dp($errs,'c');
	  $changed_trains[] =& $newt1;	  
	}
	
	if ($switching_t2) {
	  $oldt2->adjustPassengers(ADJUST_MODE_DEL, $old_acles, $errs);
	  $newt2->adjustPassengers(ADJUST_MODE_ADD, $new_acles, $errs);
	  $changed_trains[] =& $oldt2;
	  $changed_trains[] =& $newt2;
	}
	else if ($adding_t2) {
	  $newt2->adjustPassengers(ADJUST_MODE_ADD, $new_acles, $errs);
	  $changed_trains[] =& $newt2;
	}
	else if ($dropping_t2) {
	  $oldt2->adjustPassengers(ADJUST_MODE_DEL, $new_acles, $errs);
	  $changed_trains[] =& $oldt2;
	}
	else if ($newt2 && $changing_px) {
	  $newt2->adjustPassengers(ADJUST_MODE_DEL, $old_acles, $errs);
	  $newt2->adjustPassengers(ADJUST_MODE_ADD, $new_acles, $errs);
	  $changed_trains[] =& $newt2;
	}
      }
      else {
	//
	// Not change mode: just add or del px to one or two trains.
	//
	$newt1->adjustPassengers($adjust_mode, $new_acles, $errs);
	$changed_trains[] =& $newt1;
	if ($newt2) {
	  $newt2->adjustPassengers($adjust_mode, $new_acles, $errs);
	  $changed_trains[] =& $newt2;
	}
      }
	
      //
      // SAVE THE TRAINS!
      //
      global $DEBUG_ADJUST_TRAINS;
      if (isset($DEBUG_ADJUST_TRAINS)) {
	DB_DataObject::debugLevel(1);
      }
      
      foreach ($changed_trains as $train) {
	if (count($errs) > 0) {
	  break;
	}
	if (false === $train->update()) {
	  $errs[] = 'Problem saving train id ' . $train->trainId;
	}
      }

      if (isset($DEBUG_ADJUST_TRAINS)) {
	DB_DataObject::debugLevel(0);
      }

      return count($errs) < 1;
    }

    /**
     * computeDue - Based solely on rates stored in reservation.
     *
     * @access public
     * @param  {  type|objectdefinition } { $varname } [ description ]
     * @return float computed total
     */
    function computeDue()
    {
      return ((isset($this->tTrain_id_2) && $this->tTrain_id_2 > 0)
	      ? $this->adults * $this->a2Rate + $this->children * $this->c2Rate
	      : $this->adults * $this->a1Rate + $this->children * $this->c1Rate)
	+ ($this->boxLunches * DataObjects_TSeason::getBoxLunchRate())
	- $this->deposit;
    }

    /**
     * _preWrite - Shared checks for any update.
     *
     * @static
     * @access public
     * @param  arrayref $errs		errors
     * @return boolean TRUE for okay, else false with errors in $errs
     */
    function _preWrite(&$errs)
    {
      if (isset($this->errs) && count($this->errs) > 0) {
	$errs = $this->errs;
	return false;
      }

      if (empty($this->tTrain_id_1)) {
	$errs[] = 'An outbound train is required.';
	return false;
      }

      $this->getLinks(true,true);
      $this->setRates();
      $this->amountDue = (RES_CHECKEDIN == $this->status)
	? 0
	: $this->computeDue();

      if ($this->totalRiders() < 1) {
	$errs[] = 'No passengers were entered!  Please give values in <b>adults</b>, <b>children</b>, <b>specials</b>, <b>laps</b>, or  <b>escorts</b>.';
      }

      // Trains are already set by setFrom. If t1 is missing, it's an error
      // anyway.  If t2 is missing and at least one of its date or run
      // fields was entered, that's an error.
      if (count($errs) > 0) {
	return false;
      }

      // need both trains to proceed
      if (empty($this->tTrain_id_1) || empty($this->tTrain_id_2)
	  || empty($this->_tTrain_id_1) || empty($this->_tTrain_id_2)) {
	return true;
      }

      if ($this->tTrain_id_2 === $this->tTrain_id_1) {
	$errs[] = 'Out and return trains must differ.';
	return false;
      }    

      $this->checkRoundTrip($this->_tTrain_id_1, $this->_tTrain_id_2, $errs);

      return count($errs) < 1;
    }

    /**
     * Checks for round trips: two trains
     *
     * @access public
     * @param  objref t1
     * @param  objref t2
     * @param  arrayref errs
     */
    function checkRoundTrip(&$t1, &$t2, &$errs)
    {
      // Check run directions, must differ!
      $t1->getLinks();
      $t2->getLinks();

      if ($t1->_tSchedRun_id->tStation_id == $t2->_tSchedRun_id->tStation_id) {
	$errs[] = 'Trains for round trips must be in opposite directions.';
      }

      // Check run order
      if ($this->_tTrain_id_1->date == $this->_tTrain_id_2->date) {
	$time1 = $this->_tTrain_id_1->getRunTime();
	$time2 = $this->_tTrain_id_2->getRunTime();
	if ($time1->id > $time2->id) {
	  // NOTE: depend on ids being in time order.
	  $errs[] = 'Outbound train must be the one before the return train';
	}
      }
      else {
	$date1 = strtotime($this->_tTrain_id_1->date);
	$date2 = strtotime($this->_tTrain_id_2->date);
	if ($date1 > $date2) {
	  // NOTE: depend on ids being in time order.
	  $errs[] = 'Outbound train must be the one before the return train';
	}
      }
    }

    /**
     * @access private
     * @param  $adjust_mode reserve or release
     */
    function _customerChecks(&$errs)
    {
      if (empty($this->tCustomer_id) || $this->tCustomer_id < 1) {
	$errs[] = 'Internal error - no customer';
      }

      if (!is_null($this->dateReceived) && $this->deposit == 0) {
	$errs[] = '<b>Date Received</b> is set but no <b>Deposit</b> was entered.';
      }
    }

    /**
     * @access public
     * @param  $id
     * @return object, or print fatal error
     */
    function staticGetOrError($id)
    {
      assert(!is_null($id));
      $r = DataObjects_TReservation::staticGet($id);
      if (false == $r) {
	MNP::error("internal error finding reservation $id", 1);
      }
      return $r;
    }

    function createRes()
    {
      $errs = array();
      assert($this);
      if (true !== $this->_preWrite($errs)) {
	return $errs;
      }
      return(true !== $this->_AdjustBothTrains(ADJUST_MODE_ADD, $errs))
	? $errs
        : $this->insert();
    }

    function changeRes()
    {
      // Wipe whatever in-memory train we have.  _preWrite will load the
      // desired trains according to the train ids in the res.
      unset($this->_tTrain_id_1);
      unset($this->_tTrain_id_2);
      
      $errs = array();
      if (true !== $this->_preWrite($errs)) {
	return $errs;
      }
      return(true !== $this->_AdjustBothTrains(ADJUST_MODE_CHG, $errs))
	? $errs
        : $this->update();
    }

    /**
     * deleteRes - releases trains before deleting.
     *
     * @access public
     * @return boolean
     */
    function deleteRes()
    {
      $errs = array();
      if ($this->status != RES_RELEASED) {
	return (true !== $this->_AdjustBothTrains(ADJUST_MODE_DEL, $errs))
	  ? $errs
	  : $this->delete();
      }
      else {
	return $this->delete();
      }
    }


    /**
     * find all reservations for given customer [in given season]
     *
     * @access public
     * @param  int custid
     * @param  int season year
     * @return int numfound
     */
    function findCustSeasonRes($custid, $season=null)
    {
      $cols = join(',', array_keys($this->_get_table()));
      $sql = "SELECT $cols from tReservation,tTrain where tCustomer_id='$custid'
    	AND (tTrain_id_1=trainId OR tTrain_id_2=trainId)";
      if (!is_null($season) && !empty($season)) {
	$sql .= " AND YEAR(date)='$season'";
      }
      
      $this->query($sql);
      return $this->N;
    }

    /**
     * find - does a little patching up of train info for finding after setFrom()
     *
     * @access public
     * @return int numrows found
     */

    function findRes()
    {
      $jointrain = false;

      // cases:
      // - nothing
      // - (idjoin1 AND run AND date)
      // - (idjoin2 AND run AND date)
      // - (idjoin1 AND run AND date) OR (idjoin2 AND run AND date) 
      //
      $t1cond = $this->trainCond(1, $this->train1_date, $this->train1_run);
      $t2cond = $this->trainCond(2, $this->train2_date, $this->train2_run);

      if (!empty($t1cond) && !empty($t2cond)) { 
	$this->whereAdd("(($t1cond) OR ($t2cond))");
	$jointrain = true;
      }
      else if (!empty($t1cond)) { 
	$this->whereAdd($t1cond); 
	$jointrain = true;
      }
      else if (!empty($t2cond)) { 
	$this->whereAdd($t2cond); 
	$jointrain = true;
      }
    
      if ($jointrain) {
	// Hack the Res object's join field.  It's a private member so this
	// would anger the management.  It already has Res table name in it.
	$this->_join .= ', tTrain';

	// If we're doing any joining, clear select and put only Res fields
	// into SELECT.
	$this->selectAdd();
	$this->selectAdd(join(',', array_keys($this->_get_table())));
      }
      //      $this->debugLevel(1);      
      return $this->find();
    }

    /**
     * setRates - Set rates in a walkup or full reservation
     *
     * @access public
     * @param  string $type
     */
    function setRates()
    {
      if (!$this->isWalkup()) {
	assert(isset($this->_tCustomer_id));
      }

      list($this->a1Rate, $this->a2Rate, $this->c1Rate, $this->c2Rate) =
	($this->isWalkup()
	 ? DataObjects_TSeason::getTypeRates($this->walkupType)
	 : $this->_tCustomer_id->getRates());
    }

    /**
     * walkupReserve - skips customer, etc
     *
     * @access private
     * @return int inserted ID for okay, or false for error
     */
    function walkupReserve($walkupType, $checkinAlso)
    {
      $errs = array();
      $this->walkupType = $walkupType;
      $this->status = $checkinAlso
	? RES_CHECKEDIN
	: RES_RESERVED;
      if (true !== $this->_preWrite($errs)) {
	return $errs;
      }      
      return (true !== $this->_AdjustBothTrains(ADJUST_MODE_ADD, $errs))
	? $errs
	: $this->insert();
    }

    /**
     * release - modifies a train
     *
     * @access public
     * @return mixed boolean TRUE for okay or string containing error
     */
    function release()
    {
      $errs = array();
      assert($this);

      if ($this->status == RES_RELEASED) {
	return array("Reservation {$this->resId} is already released.  No action taken.");
      }
      else {
	$errs = array();
	$okay = true;

	// set the rates and recompute the fare - they would owe $ now to ride
	// Don't need train checking b/c we're not asking for seats, we're
	// giving
	$this->getLinks();
	$this->setRates();
	$this->amountDue = $this->computeDue();

	if ($this->status == RES_CHECKEDIN || $this->status == RES_RESERVED) {
	  $okay = $this->_AdjustBothTrains(ADJUST_MODE_DEL, $errs);
	}
	if ($okay) {
	  $this->status = RES_RELEASED;
	  return $this->update();
	}
	else {
	  return $errs;
	}
      }
    }

    /**
     * release all reserverations on train 'reserved' status.  For standby
     *
     * @access public
     * @param  obj $train
     * @return bool
     */
    function releaseAll(&$train)
    {
      $errs = array();
      $okay = true;
      $rsvns = DataObjects_TReservation::getTrainReservations($train);
      
      // Hash of trainid => array(a,c,le,s) - these will be hard numbers
      // to write to trains when we're done.
      $trainChanges = array();

      foreach ($rsvns as $res) {
	if ($res->status != RES_RESERVED) {
	  continue;
	}
	$res->getLinks();

	// preload array with current state of all trains involved in every
	// reservation in the list
	if (!key_exists($res->tTrain_id_1, $trainChanges)) {
	  $trainChanges[$res->tTrain_id_1] = 
	    DataObjects_TTrain::getBlankACLES();
	}
	if (!empty($res->tTrain_id_2) && 
	    !key_exists($res->tTrain_id_2, $trainChanges)) {
	  $trainChanges[$res->tTrain_id_2] = 
	    DataObjects_TTrain::getBlankACLES();
	}
	
	$res->setRates();
	$res->amountDue = $res->computeDue();
	$racles = $res->_getACLES();

	foreach ($racles as $i=>$px) {
	  $trainChanges[$res->tTrain_id_1][$i] += $px;
	  if (!empty($res->tTrain_id_2)) {
	    $trainChanges[$res->tTrain_id_2][$i] += $px;
	  }
	}

	if ($okay != true) {
	  return $errs;
	}
	$res->status = RES_RELEASED;
	$okay = $res->update();
	if ($okay != true) {
	  return $okay;
	}
      }      
      
      foreach ($trainChanges as $trainId=>$acles) {
	$t = DataObjects_TTrain::staticGet($trainId);
	$t->adjustPassengers(ADJUST_MODE_DEL, $acles, $errs);
	if (count($errs) > 0) {
	  return $errs;
	}
	$okay = $t->update();
	if ($okay != true) {
	  return $okay;
	}
      }

      return true;
    }


    /**
     * checkin - Marks reservation, implies money was received
     *
     * @access public
     * @return boolean TRUE or error string.
     */
    function checkin($payment=null)
    {
      assert($this);

      if ($this->status == RES_CHECKEDIN) {
	return
	  array("Reservation {$this->resId} is already checked in.  No action taken.");
      }
      // else if (xxx) {
      // load trains, if neither has a date today, then yack here
      //return array("We can only checkin reservations on trains for today.");
      //}
      else {
	$errs = array();
	$okay = true;
	if ($this->status == RES_RELEASED) {
	  $okay = $this->_AdjustBothTrains(ADJUST_MODE_ADD, $errs);
	}
	if ($okay) {
	  $this->status = RES_CHECKEDIN;
	  if (is_null($payment)) {	    
	    $this->amountDue = 0; 	  // PAID IN FULL
	  }
	  else {
	    $this->amountDue -= $payment; // PARTIAL payment
	  }
	  return $this->update();
	}
	else {
	  return $errs;
	}
      }
    }

    /**
     * Fuzzy match a name and date and check him in if close enough.
     *
     * @access public
     * @param  string $name
     * @param  object $train to search
     * @return TRUE or error string
     */
    function quickCheckin($name, $train)
    {
      $matches = 0;		// how many fuzzy match 0 scores
      $matched = 0;		// which one last matched

      if (empty($name) || strlen($name) < 2) {
	return 'I need a last name in the quick checkin box.';
      }

      $rsvns = DataObjects_TReservation::getTrainReservations($train);
      foreach ($rsvns as $res) {
	//	$res->getLinks();
	$score = MNP::fuzzymatch($_POST['quickCheckinName'], $res->getName());
	if (0 === $score) {
	  $matches ++;
	  $matched = $res->resId;
	}
      }

      if ($matches == 1) {
	// Exactly one good match.  Check it in.
	$res = DataObjects_TReservation::staticGet($matched);
	return $res->checkin();
      }
      elseif ($matches > 1) {
	return 'Too many matches; please try another spelling or choose from list.';
      }
      else {
	return 'No matches close enough; please try another spelling or choose from list.';
      }
    }

    // Override caching
    function getLinks($trains=true, $cust=true) 
    {
      if ($trains) {
	if (!isset($this->_tTrain_id_1) && !empty($this->tTrain_id_1)) {
	  $this->_tTrain_id_1 = new DataObjects_TTrain;
	  $this->_tTrain_id_1->get($this->tTrain_id_1);
	}
	if (!isset($this->_tTrain_id_2) && !empty($this->tTrain_id_2)) {
	  $this->_tTrain_id_2 = new DataObjects_TTrain;
	  $this->_tTrain_id_2->get($this->tTrain_id_2);
	}
      }
      if ($cust) {	
	if (!isset($this->_tCustomer_id) && !empty($this->tCustomer_id)) {
	  $this->_tCustomer_id = new DataObjects_TCustomer;
	  $this->_tCustomer_id->get($this->tCustomer_id);
	}
      }
    }

    /**
     * add my statistics to an array
     *
     * @access public
     * @param  $totals 
     */
    function computeTotals(&$totals)
    {
      if ($this->status = RES_CHECKEDIN) {
	$n = isset($this->tTrain_id_2) ? 'RT' : 'OW';
	$t = $this->getType();

	@$totals['A' . $n . $t . 'Px'] += $this->adults;
	@$totals['C' . $n . $t . 'Px'] += $this->children;
	@$totals['LES' . $n . $t . 'Px'] += $this->laps + 
	  $this->escorts + $this->specials; 
      }
    }


}
?>
