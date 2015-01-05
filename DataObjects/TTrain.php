<?
/*
* Table Definition for tTrain
*/

require_once 'DB/DataObject.php';
require_once 'TSchedRun.php';
require_once 'TSchedRun.php';
require_once 'TTime.php';

define('ADJUST_MODE_DEL', 1);
define('ADJUST_MODE_ADD', 2);
define('ADJUST_MODE_CHG', 3);	// not used here, but in TReservation

class DataObjects_TTrain extends MNPDataObject {

    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    var $__table = 'tTrain';                          // table name
    var $trainId;                         // int(6)  not_null primary_key auto_increment
    var $tSchedRun_id;                    // int(6)  not_null
    var $date;                            // date(10)  not_null
    var $seats;                           // int(6)  not_null
    var $comfort;                         // int(6)  
    var $aRes;                            // int(6)  
    var $cRes;                            // int(6)  
    var $lRes;                            // int(6)  
    var $eRes;                            // int(6)  
    var $sRes;                            // int(6)  
    var $adultBegTix;                     // int(11)  
    var $adultEndTix;                     // int(11)  
    var $childBegTix;                     // int(11)  
    var $childEndTix;                     // int(11)  
    var $groupBegTix;                     // int(11)  
    var $groupEndTix;                     // int(11)  
    var $lcsBegTix;                       // int(11)  
    var $lcsEndTix;                       // int(11)  
    var $aRT;                             // real(12)  
    var $aOW;                             // real(12)  
    var $aTRT;                            // real(12)  
    var $aTOW;                            // real(12)  
    var $cRT;                             // real(12)  
    var $cOW;                             // real(12)  
    var $cTRT;                            // real(12)  
    var $cTOW;                            // real(12)  
    var $oRT;                             // real(12)  
    var $oOW;                             // real(12)  

    /* ZE2 compatibility trick*/
    function __clone() { return $this;}

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_TTrain',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

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
      static $names = array('adultBegTix' => 'Adult Beg Ticket #',
			    'adultEndTix' => 'Adult End Ticket #',
			    'childBegTix' => 'Child Beg Ticket #',
			    'childEndTix' => 'Child End Ticket #',
			    'lcsBegTix' => 'L/C/S Beg Ticket #',
			    'lcsEndTix' => 'L/C/S End Ticket #',
			    'aRT' => 'Adult RT $',
			    'aOW' => 'Adult OW $',
			    'aTRT' => 'Tour Adult RT $',
			    'aTOW' => 'Tour Adult OW $',
			    'cRT' => 'Child RT $',
			    'cOW' => 'Child OW $',
			    'cTRT' => 'Child Tour RT $',
			    'cTOW' => 'Child Tour OW $',
			    'oRT' => 'Special RT $',
			    'oOW' => 'Special OW $'
			    );

      // See comment atop lib/DailyTrainColumn.php
      if (intval(substr($this->date, 0, 4)) < 2004) {
	$names['groupBegTix'] = 'Group Beg Ticket #';
	$names['groupEndTix'] = 'Group End Ticket #';
      }

      return key_exists($fieldname, $names) ? $names[$fieldname] : null;
    }

    /**
     * @access public
     * @return boolean if okay
     */
    function lockTables()
    {
      return true;
    }

    /**
     * @access public
     * @return boolean if okay
     */
    function unLockTables()
    {
      return true;
    }

    /**
     * @access public
     * @return int
     */
    function getTotalReservations()
    {
      return $this->aRes + $this->cRes + $this->lRes + $this->eRes 
	+ $this->sRes;
    }

    /**
     * @access public
     * @return int
     */
    function getAvailableSeats()
    {
      return $this->seats - $this->comfort - $this->getTotalReservations();
    }

    /**
     * Array of all res and wup (or zeros) in table order.  For testing.
     *
     * @access public
     * @return array of int
     */
    function getWupRes()
    {
      return array(@$this->aRes,
		   @$this->cRes,
		   @$this->lRes,
		   @$this->eRes,
		   @$this->sRes);
    }

    function getACLES()
    {
      return array('aRes' => @$this->aRes,
		   'cRes' => @$this->cRes,
		   'lRes' => @$this->lRes,
		   'eRes' => @$this->eRes,
		   'sRes' => @$this->sRes);
    }

    function getBlankACLES()
    {
      return array('aRes' => 0,
		   'cRes' => 0,
		   'lRes' => 0,
		   'eRes' => 0,
		   'sRes' => 0);
    }

    function adp($a, $title=null)  
    {      
      foreach ($a as $t) {
	$t->dp($title);
      }
    }

    function dp($title=null) 
    {
      /* shielded */ MNP::dp($this->toString(), "$title, train {$this->trainId}");
    }

    function staticCreate($fromdate, $todate, $run, $seats)
    {
      $dates = MNP::dates_in_range($fromdate, $todate);
      $count = 0;
      foreach($dates as $date) {
	$newtrain = new DataObjects_TTrain;
	$newtrain->tSchedRun_id = $run;
	$newtrain->seats        = $seats;
	$newtrain->date         = $date;
	$id = $newtrain->insert();
	if (!$id) {
	  break;
	}
	$count++;
      }
      return array($id, "creating $count trains");
    }

    /**
     * return string date, time, and station for a run
     *
     * @access public
     * @return string
     */
    function toString()
    {    
      if (!isset($this->_tSchedRun_id)) {
	$this->getLinks();
      }      
      return $this->date . ' ' . $this->_tSchedRun_id->toString();
    }

    function toLongString() 
    {
      $s = $this->toBriefString();
      $pats = array('/HC/', '/KY/');
      $repl = array('Metropolis - ', 'Gotham City - ');
      return preg_replace($pats, $repl, $s);
    }
    
    function getDepartureStation()
    {    
      if (!isset($this->_tSchedRun_id)) {
	$this->getLinks();
      }      
      return $this->_tSchedRun_id->tStation_id;
    }

    /**
     * return HTML link to set reservation's outbound train
     *
     * @access public
     * @return string
     */
    function toLink($descr)
    {    
      if (!isset($this->_tSchedRun_id)) {
	$this->getLinks();
      }      

      return MNP::link(null, 
		       $descr == 'Outbound' ? 'setTrain1' : 'setTrain2',
		       $this->date . ' ' . $this->_tSchedRun_id->toString()  ,
		       $this->trainId);
    }
    
    /**
     * colored - 
     *
     * @access public
     * @param  string
     * @param  int
     * @return string
     */
    function colored($str, $seats)
    {
      if ($seats > YELLOWLEVEL) {
	return $str;
      }
      return MNP::background(($seats < REDLEVEL) ? 'error' : 'warning', $str);
    }

    /**
     * return string time and station for a run
     *
     * @access public
     * @return string
     */
    function toBriefString()
    {    
      if (!isset($this->_tSchedRun_id)) {
	$this->getLinks();
      }      
      return (isset($this->_tSchedRun_id) && false != $this->_tSchedRun_id)
	? $this->_tSchedRun_id->toString()
	: 'tBS-oops';
    }

    /**
     * getRunTime - return run time of a train object
     *
     * @access public
     * @return ref to a time object
     */
    function &getRunTime()
    {
      assert($this);

      if (!isset($this->_tSchedRun_id)) {
	$this->getLinks();
	if (!isset($this->_tSchedRun_id)) {
	  return null;	  
	}
      }

      if (!isset($this->_tSchedRun_id->_tTime_id)) {
	$this->_tSchedRun_id->getLinks();
	
	if (!isset($this->_tSchedRun_id->_tTime_id)) {
	  return null;	  
	}
      }

      return $this->_tSchedRun_id->_tTime_id;      
    }

    /**
     * Returns train selector consisting of a scheduled run selector and a
     * date button.  All schedruns from given season (or current one) are
     * shown. If trainId is given, it will be selected.  If trainId=0,
     * --NONE-- is selected.
     *
     * @access public
     * @return string
     */
    function staticFormCode($formname, $name='train', $trainId, $offer_none=0, $extras='')
    {
      if (is_object($trainId)) {
	$date = $trainId->date;
	$run  = $trainId->tSchedRun_id;
      }
      else if (!is_null($trainId) && is_int($trainId) && $trainId > 0) {
	$train = new DataObjects_TTrain;
	$train->get($trainId);
	$date = $train->date;
	$run = $train->tSchedRun_id;
      }
      else if (!is_null($trainId) && is_array($trainId)) {
	list($run, $date) = $trainId;
      }
      else {
	$run = 0;
	$date = '';
      }
      
      $season = empty($date) ? null : MNP::dateToSeason($date);
      
      return
	DataObjects_TSchedRun::formCode($formname,
					$name . '_run',
					$run,
					$season, 
					$offer_none)
	. ' '
	. MNP::date_button_string($formname, $name . '_date', $date)
	. $extras;
    }

    /**
     * get all tains for a date, in time order
     * 
     * @access public
     * @param  string date
     * @return array of train objects
     */
    function &getDateTrains($date, $departingStationId=null)
    {
      $out = array();

      // HACK: hardcoding 3 for Office.  Same as no station: show any.
      if (is_null($departingStationId) || $departingStationId == 3) {
	$station = '';
	$tstation = '';	
      }
      else {	
	// just the user's station
	$station = " AND tSchedRun.tStation_id=tStation.id "
	  	  . "AND tStation.id=$departingStationId ";
	$tstation = ',tStation';	
      }

      $t = new DataObjects_TTrain();
      $cols = join(',', array_keys($t->_get_table()));
      $sql = "SELECT $cols "
	. "FROM tTrain,tSchedRun,tTime$tstation " 
	. "WHERE date='$date' "
	. "AND runId=tSchedRun_id "
	. $station
	. "AND tTime_id=tTime.id "
	. "ORDER BY runTime;";
           
      $t->query($sql);
      while ($t->fetch()) {
	$t->_tSchedRun_id = DataObjects_TSchedRun::staticGet($t->tSchedRun_id);
	$t->_tSchedRun_id->_tTime_id = DataObjects_TTime::staticGet($t->_tSchedRun_id->tTime_id);
	$out[$t->trainId] = $t;
      }

      return $out;      
    }

    /**
     * nextDeparture - find next train leaving given station
     *
     * @static
     * @access public
     * @param  int stationId station train will leave from
     * @return reference to the next train
     */
    function &nextDeparture ($date=null, $time=null, $departingStationId=null)
    {
      global $today;

      if (is_null($date)) {
	$runDate = ' AND date>=CURDATE()';
	$runTime = " AND runTime>=CURTIME()";	
      }
      else if (!is_null($date) && is_null($time)) {
	$runDate = " AND date='$date'";
	$runTime = ($date == $today)
	  ? ''					// same case as null, null
	  : " AND runTime>='00:00:00'"; 	// anything after midnight
      }
      else if (is_null($date) && !is_null($time)) {
	die("unexpected (null, !null) to nextDeparture");
      }
      else /* (!null, !null) */ {
	$runDate = " AND date='$date'";
	$runTime = " AND runTime>='$time'";
      }
      
      // HACK: hardcoding 3 for Office.  Same as no station: show any.
      if (is_null($departingStationId) || $departingStationId == 3) {
	$station = '';
	$tstation = '';	
      }
      else {
	// just the user's station
	$station = " AND tSchedRun.tStation_id=tStation.id "
	  	  . "AND tStation.id=$departingStationId ";
	$tstation = ',tStation';	
      }

      $train = new DataObjects_TTrain;
      $cols = join(',', array_keys($train->_get_table()));
      $sql = "SELECT $cols FROM tSchedRun,tTrain,tTime{$tstation}" // cols!!!!
	. ' WHERE tTrain.tSchedRun_id=tSchedRun.runId'
	. ' AND tSchedRun.tTime_id=tTime.id'
	. $runDate
	. $runTime
	. $station
	. ' ORDER BY date, tTime.runTime'
	. ' LIMIT 1'	;

      $train->query($sql);
      
      if($train->fetch()) {
	$train->getLinks();
	return $train;
      }
      else {
	//	MNP::error('next departure - no train found');
	return null;
      }
    }

    /**
     * Return the immediate return train corresponding to an outbound one.
     *
     * @access public
     * @param  object $outboundTrain 
     * @return object train, or NULL
     */
    function returningTrain($outboundTrain)
    {
      //
      // Want the NEXT time after outbound's.  NOTE: This depends (and
      // elsewhere!!) upon runTime IDs being sorted by time.
      //
      $timeObj = $outboundTrain->getRunTime();     
      $timeObj = DataObjects_TTime::staticGet($timeObj->id + 1);
      $timestr = $timeObj->runTime;

      switch ($outboundTrain->_tSchedRun_id->tStation_id) {
      case 1: $stationId = 2; break;
      case 2: $stationId = 1; break;
      default: die('bad switch');
      }
      
      return DataObjects_TTrain::nextDeparture($outboundTrain->date, $timestr, $stationId);
    }


    /**
     * Search array of trains, as from getDateTrains
     *
     * @access public
     * @param array $trains
     * @param array $id
     * @return object train, null for not found 
     */
    function &arrayFindTrainId(&$trains, $id)
    {
      foreach ($trains as $train) {
	if ($train->trainId == $id) {
	  return $train;
	}
      }
      return null;
    }

    /**
     * Search array of trains, as from getDateTrains
     *
     * @access public
     * @param array $trains
     * @param array $afterTime
     * @return object train; will be last one if time is bigger
     */
    function &searchTrainArray(&$trains, $afterTime)
    {      
      foreach ($trains as $train) {
	$gettime = @$train->_tSchedRun_id->_tTime_id->runTime;
	$traintime = strtotime($train->date . ' ' . $gettime);
	if (empty($gettime) || $traintime === -1) {
	  die("bogus time: $gettime, $traintime");
	}
	if ($traintime >= $afterTime) {
	  return $train;
	}
      }
      return $train;
    }

    /**
     * Find a train with RUN on DATE.
     *
     * Note it doesn't need to do validation of its inputs because if they
     * are not found in the DB then it doesn't matter, still an error.
     *
     * @access public 
     * @param  int runId
     * @param  string DateStr
     * @return object train, or NULL for error.
     */
    function findDateRunTrain($runId, $dateStr)
    {
      if (empty($runId) || $runId == 0 || empty($dateStr)) {
	return null;
      }
      $t = new DataObjects_TTrain();
      $t->whereAdd("tSchedRun_id=$runId");
      $t->whereAdd("date='$dateStr'");
      return $t->find(true)
	? $t
	: null;      
    }

    /**
     * Used for prewrite to show errors and helpful links.  
     * Used when findDateRunTrain didn't.
     *
     * @access public
     * @param int $runId
     * @param string $date
     * @param string $descr
     * @param array $errs
     */
    function makeTrainErrors($runId, $date, $descr, &$errs)
    {
      $runs = array();      
      if ($runId && $date) {

	$trains = DataObjects_TTrain::getDateTrains($date);
	if (count($trains) < 1) {
	  $errs[] = "<b>$descr train</b>: There are no trains scheduled on $date.";
	}
	else {
	  $sr = DataObjects_TSchedRun::staticGet($runId);
	  if (false === $sr) {
	    $links = 'OOPS';
	    $srstr = 'HMMM';	    
	  }
	  else {
	    $srstr = $sr->toString();	    
	    $links = '';
	    foreach ($trains as $t) {
	      $links .= '<li>' . $t->toLink($descr) . '</li>';
	    }
	  }
	  $errs[] = "<b>$descr train</b>: There is no $srstr train on $date. Scheduled trains on that date are: <ul> $links </ul>";
	}
      }
      else if ($runId xor $date) {
	// both missing is okay, but not only one
	$errs[] = "<b>$descr Train</b> (both <b>time</b> and <b>date</b>) is required."; 
      }
      else {
	$errs[] = "<b>$descr Train</b> is required...."; 
      }
    }
    
    /**
     * adjustPassengers - Add px to train (reserve) or subtract (refund)
     *
     * If a change is possible, it is made in the object only; TRUE is 
     * returned but the obj is not saved.  If the change is not possible, 
     * no changes are made and FALSE is returned.
     *
     * @access public
     * @param  bool $mode true means add, false subtract
     * @param  array $args keys are slot name, value smallint
     * @return boolean true; or false and $errs array holds errors
     */
    function adjustPassengers($mode, $args, &$errs) 
    { 
      $wanted = 0;
      $availSeats = $this->getAvailableSeats();
      $takenSeats = $this->seats - $availSeats;

      foreach ($args as $k=>$v) {
	$v = intval($v);
	if ($v == 0) {
	  continue;
	}
	assert(preg_match('/[acles](Wup|Res)/', $k));	
	$wanted += $v;

	if (($mode == ADJUST_MODE_DEL) && $this->$k < $v) {
	  $errs[] = $this->toBriefString() . " Train : excess $k seats refunded: "
	    . ($this->$k ? $this->$k : '0')
	    . " currently reserved, $v requested for refund.";
	  return false;
	}
      }

      if (($mode == ADJUST_MODE_ADD) && $wanted > $availSeats) {
	$errs[] = $this->toBriefString()
	  . " Train: Insufficient seats: $availSeats available, $wanted requested.";
	return false;
      }
      else if (($mode == ADJUST_MODE_DEL) && $wanted > $takenSeats) {
	$errs[] = $this->toBriefString() . " Train: Excess seats refunded: $takenSeats currently reserved, " 
	  . $netChange . " requested for refund.";
	return false;
      }
      else {
	// okay.  make the netChange.
	// TODO: give a note about zero seats available after this change?
	foreach ($args as $k=>$v) {
	  $this->$k += (($mode == ADJUST_MODE_ADD) ? $v : -$v);
	}

	return true;
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
      if (preg_match('/^(.*?)(Beg|End)Tix$/', $fieldname, $matches)) {
	$a = $matches[1] . 'BegTix';
	$b = $matches[1] . 'EndTix';
	return MNP::input_number($fieldname, $value, false, 12, 
				 "onChange=\"return orderTicketPairs('$a', '$b');\"");
      }
      else {
	return null;
      }
    }

    function makeArbCode($formname) 
    {
      return MNP::wrapJavascript('
	// put the bigger ticket number on top
	function orderTicketPairs(begfield, endfield) {
	  diff = "diff_" + begfield + "_" + endfield;
	  a = document.TrainAccounting.elements[begfield].value;
	  b = document.TrainAccounting.elements[endfield].value;
	  document.TrainAccounting.elements[diff].value = Math.abs(a - b);
	}
	');
    }
}
?>