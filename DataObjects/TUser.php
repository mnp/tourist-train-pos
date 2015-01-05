<?
/*
* Table Definition for tUser
*/



require_once('DB/DataObject.php');

class DataObjects_TUser extends MNPDataObject {

    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    var $__table = 'tUser';                           // table name
    var $id;                              // int(6)  not_null primary_key auto_increment
    var $user_uid;                        // string(255)  not_null
    var $user_pass;                       // string(32)  not_null
    var $tAuthGroup_id;                   // int(6)  not_null
    var $comment;                         // string(80)  

    /* ZE2 compatibility trick*/
    function __clone() { return $this;}

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_TUser',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    function getAll() 
    {
      static $out;
      if (isset($out)) {
	return $out;
      }
      $u = new DataObjects_TUser;
      $u->find();
      while($u->fetch()) {
	$out[$u->id] = $u->user_uid;
      }
      return $out;
    }

    /**
     * @access public
     * @return string html selector
     */
    function formCode()
    {
      return MNP::selector_string('userSelect', 
				  DataObjects_TUser::getAll(), 
				  0,
				  0,
				  is_null($this->id) ? null : $this->id);
    }  

    /**
     * @access public
     * @return string user name
     */
    function toString()
    {
      return $this->user_id;
    }
}
?>