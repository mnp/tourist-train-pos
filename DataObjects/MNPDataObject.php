<?php

require_once 'MNP.php';
require_once 'DB/DataObject.php';

define('EMPTY_ACTION', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');

class MNPDataObject extends DB_DataObject 
{
  /**
   * Wrap parent debug method, storing its output to a variable.
   *
   * @access public
   * @return array objects
   */
  function debug($message, $logtype = 0, $level = 1)
  {
    global $debugMessges;
    if (isset($debugMessges)) {
      ob_start();
      parent::debug($message, $logtype, $level);
      $debugMessges .= ob_get_contents();
      ob_end_clean();
    }
    else {
      parent::debug($message, $logtype, $level);
    }
  }

  /**
   * fetchDataObjects - retrieve an array of objects.
   *
   * A find() or query() should already have been done.
   *
   * @access public
   * @return array objects
   */
  function fetchDataObjects($stop=MANYSTOP, $getLinksPlease=false)
  {
    $out = array();
    $found = 0;   
    $idfield = array_shift($this->_get_keys());

    // fixme: use HTML::Pager
    while ($this->fetch() && $found < $stop) {
      $found++;
      $x = $this->__clone();
      if ($getLinksPlease) {
	$x->getLinks();
      }
      $out[$x->$idfield] = $x;
    }
    return $out;
  }  

  function fetchAllDataObjects($getLinksPlease=false)
  {      
    $this->find();
    return $this->fetchDataObjects(MAXINT,$getLinksPlease);
  }

  function queryAllDataObjects($sql)
  {      
    $this->query($sql);
    return $this->fetchDataObjects(MAXINT, false);
  }

  /**
   * checkRequired 
   *
   * @access public
   * @return TRUE or array of errors
   */
  function checkRequired()
  {
    if (!method_exists($this, 'requiredField')) {
      return TRUE;
    }
    
    $errs = array();
    foreach ($this->_get_table() as $fieldname=>$type) 
    {
      if ($this->requiredField($fieldname) && 
	  (is_null($this->$fieldname) || empty($this->$fieldname))) {
	$errs[] = 'Field <b>' 
	  . $this->niceName($fieldname) 
	  . '</b> is required';
      }
    }
    return count($errs) == 0 ? TRUE : $errs;
  }
  
      
  // Generic operation mapper. OP must be defined here or in DB_DataObject.
  // optional: array(field=>value)
  // returns : array(status, message)
  function mapOp($op, $ids, $args=array()) 
  {  
    if (!is_array($ids)) {
      $ids = array($ids);
    }

    $nids = count($ids);
    if ($nids < 1) {
      return array(false, 'No ids were selected');
    }

    $okay = true;
    $mess = "Operation $op upon $nids {$this->__table} objects";
    $class = get_class($this);

    foreach ($ids as $id) {
      $obj = new $class();
      if ($id > 0 && $op != 'insert') {
	$obj->get($id);
      }
      
      foreach ($args as $k=>$v) {
	// error check: Make sure object has field $k.
	//FIXME? We can pass _POST for args if we don't do this
	// assert('array_key_exists($k, $obj->_get_table())');  
	$obj->$k = $v;
      }

      $okay = $obj->$op();
      if (!$okay) {
	$mess .= "<br>Failed working on id $id";
	break;
      }
    }

    return array($okay, $mess);
  }

  /**
   * setLastMod - If the dataobject has lastmod fields, set them.
   * Also sets creation date if needed.
   */
  function setLastMod()
  {
    global $session_data;

    $vars = get_class_vars(get_class($this));
    if (array_key_exists('lastModUid', $vars)) {
      $this->lastModUid = $session_data['userId'];
      $this->lastModDateTime = date("Y-m-d H:i:s");
    }
    if (array_key_exists('created', $vars)) {
      $this->created = date("Y-m-d");
    }
  }



  /**
   * override DataObject::insert
   *
   * @access public
   * @return int or error array
   */
  function insert()
  {    
    $errs = $this->checkRequired();

    if ($errs === TRUE) {
      $this->setLastMod();
      return parent::insert();
    }
    else {
      return $errs;
    }    
  }

  /**
   * unsetEmptyFields - 
   *
   * @static
   * @access (public|private)
   * @param  {  type|objectdefinition } { $varname } [ description ]
   * @return {  type|objectdefinition } [ $varname ] [ description ]
   */
  function unsetEmptyFields()
  {
  }



  /**
   * override DataObject::update
   *
   * @access public
   * @return boolean
   */
  function update($original=null)
  {
    global $MNPDebugUpdate;

    $errs = $this->checkRequired();
    if ($errs === TRUE) {
      $this->setLastMod();

      if ($MNPDebugUpdate) { 
	DataObjects_TTrain::debugLevel(1);
      }
      $x =  parent::update($original);
      if ($MNPDebugUpdate) { 
	DataObjects_TTrain::debugLevel(0);
      }
      return $x;
    }
    else {
      return $errs;
    }    
  }

  /**
   * override this DB_DataObject::setFrom because it creates
   * 0's for empty keys; not good for search queries.  Also trims
   * for same reason.
   *
   * @param    array | object  $from
   * @access   public
   * @return   boolean , true on success
   */
  function setFrom(&$from, $finding=false)
  {
    $res = parent::setFrom($from);
    if ($res !== true) {
      return $res;
    }

    if (!$finding) {
      return true;
    }

    $items = $this->_get_table();

    foreach ($items as $fieldname=>$fieldtype) {
      if (!strcmp('0', $this->$fieldname)) {
	$this->$fieldname = null;
      }
      elseif (empty($this->$fieldname)) {
	$this->$fieldname = null;
      }
      else {
	$this->$fieldname = trim($this->$fieldname);
      }
    }

    return true;
  }

  /**
   * _query - override DB/DataObject to fiddle with % queries
   *
   * @access public
   */
  function _query($string)
  {
    return parent::_query(MNP::fixupQuery($string));
  }
}

?>