<?
/*
* Table Definition for tSchedRun
*/



require_once('DB/DataObject.php');

class DataObjects_TSchedRun extends MNPDataObject {

    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    var $__table = 'tSchedRun';                       // table name
    var $runId;                           // int(6)  not_null primary_key auto_increment
    var $tSeason_id;                      // int(11)  not_null
    var $tStation_id;                     // int(11)  not_null
    var $tTime_id;                        // int(11)  not_null

    /* ZE2 compatibility trick*/
    function __clone() { return $this;}

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_TSchedRun',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    function staticCreate($sea, $time, $sta)
    {
      $s = new DataObjects_TSchedRun;

      $s->tSeason_id  = $sea;
      $s->tStation_id = $sta;
      $s->tTime_id    = $time;
      $id = $s->insert();
      return array($id, "created new scheduled run $id");
    }

    function staticUpdate($sea, $time, $sta, $id)
    {
      $s = new DataObjects_TSchedRun;
      $s->get($id);
      // FIXME: errchk
      $s->tSeason_id  = $sea;
      $s->tStation_id = $sta;
      $s->tTime_id    = $time;
      return array($s->update(), "updated scheduled run $id");
    }

    function staticDelete($id)
    {
      $s = new DataObjects_TSchedRun;
      $s->get($id);
      return array($s->delete(), "deleted run $id");      
    }

    function toString()
    {
      $this->getLinks();
      return $this->_tStation_id->code . ' ' . $this->_tTime_id->toString();
    }

    function toLongString()
    {
      $this->getLinks();
      return $this->_tStation_id->name . ' at ' . $this->_tTime_id->toString();
    }
    
    /**
     * @return array of seasonobjects
     */
    function getAll($season=null)
    {
      static $out;
      global $current_season;
      
      if (isset($out)) {
	return $out;
      }
      
      $out = array();      
      $r = new DataObjects_TSchedRun;
      $r->tSeason_id = is_null($season) ? $current_season : $season;
      $r->find();
      while($r->fetch()) {
	$out[] = $r->__clone();
      }
      return $out;
    }

    /**
     * @return array of id=>name
     */
    function getAllNames($season=null)
    {
      static $names;
      
      if (isset($name)) {
	return $names;
      }

      if (is_null($season)) {
	global $current_season;
	$season = $current_season;
      }

      $names = array();      
      $runs  = DataObjects_TSchedRun::getAll($season);
      foreach ($runs as $r) {
	$names[$r->runId] = $r->toString();
      }
      return $names;
    }
    
    // selected==0 means the first entry, which could be "NONE"

    function formCode($formname, $name="scheduledRun", $selected=null, 
		      $season=null, $offer_none=0)
    {
      $runs = DataObjects_TSchedRun::getAllNames($season);
      return MNP::selector_string($name, $runs, 0, $offer_none, $selected);
    }

    function longFormCode($formname, $name, $selected) 
    {
      $f = DataObjects_TSchedRun::formCode($formname, $name, $selected);
      $pats = array('/HC/', '/KY/');
      $repl = array('Metropolis -', 'Gotham City  -');
      return preg_replace($pats, $repl, $f);
    }
}
?>