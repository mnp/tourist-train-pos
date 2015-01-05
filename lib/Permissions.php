<?php

/**
* Crude Permissions Management - hardcoded but s/b a db table
*/

/** @constant GROUP_USER - view only permissions to some stuff. */
define('GROUP_USER', 1);

/** @constant GROUP_AGENT - rw permissions to some stuff. */
define('GROUP_AGENT', 2);

/** @constant GROUP_ADMIN - rw permissions to everything. */
define('GROUP_ADMIN', 3);

/** @constant GROUP_DEV - admin plus some debugging */
define('GROUP_DEV', 4);

require_once 'TAuthGroup.php';

/*
* @access public
*/
class Permissions
{
  /**
   * Lookup table mapping user to permission based on group membership
   *
   * @access public
   * @param string activity
   * @return boolean true if yes
   */
  function hasPermission($activity)
  {
    // Note: depends on group Id's being ordered by increasing permission.
    // Ie, user level is 1, dev level 4, etc.

    global $session_data;
    static $a = array('Checkin' => GROUP_AGENT,
		      'Resvn' => GROUP_AGENT,
		      'Cust' => GROUP_AGENT,
		      'Daily' => GROUP_AGENT, 
		      'Train Accounting' => GROUP_AGENT, 
		      'Sched' => GROUP_ADMIN,
		      'Season' => GROUP_ADMIN, 
		      'Reports' => GROUP_ADMIN,
		      'Maint' => GROUP_ADMIN,
		      'Help' => GROUP_USER,
		      'MOTD' => GROUP_USER);
    $level = $a[$activity];
    assert($level);
    return $level <= $session_data['groupId'];
  }
}

?>
