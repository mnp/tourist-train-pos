<?php

/*
 * This takes an ID and looks up a pure sql query string from the database.
 * It executes it and dumps it raw.  Too simple maybe.
 */

require_once '../lib/base.php';

$page_activity = 'Reports';
$page_title    = 'Reports';
$formname      = 'Reports';
require ADMIN_TEMPLATES . '/common-header.inc';
require ADMIN_TEMPLATES . '/form-header.inc';

$boxes = array(
array('Train Capacities: ',
      MNP::date_button_string($formname,'traincaps_date', date('Y-m-d')),
      MNP::popup_action('Go', 
			ROOTURL . '/src/traincaps.php',
			'traincaps_date',
			"document.$formname.traincaps_date.value")),
array('Daily Reservations: ',
      MNP::date_button_string($formname,'reslist_date', date('Y-m-d')),
      MNP::popup_action('Go', 
			ROOTURL . '/src/reslist.php',
			'reslist_date',
			"document.$formname.reslist_date.value")),

array('Passenger Roster: ',
      DataObjects_TTrain::staticFormCode($formname, "train", array(1, $today)),
      MNP::popup_action2('Go', 
		ROOTURL . '/src/passenger-roster.php',
		 array('date' => "document.$formname.train_date.value",
		       'train' => "document.$formname.train_run.value"))),

array('Box Lunches: ',
      MNP::date_button_string($formname,'boxlunch_date', date('Y-m-d')),
      MNP::popup_action('Go', 
			ROOTURL . '/src/lunch-report.php',
			'boxlunch_date',
			"document.$formname.boxlunch_date.value")),

array('Passenger Reports -',
      'From:' 
      . MNP::date_button_string($formname, 'date1', date('Y-m-d'))
      . 'To: '
      . MNP::date_button_string($formname, 'date2', date('Y-m-d')),
      '<br>',
      MNP::popup_action2('Passenger Origins',
			 ROOTURL . '/src/passenger-origins.php',
			 array('date1' => "document.$formname.date1.value",
			       'date2' => "document.$formname.date2.value")),
      '<br>',
      MNP::popup_action2('Referral Sources',
			 ROOTURL . '/src/passenger-referrals.php',
			 array('date1' => "document.$formname.date1.value",
			       'date2' => "document.$formname.date2.value")),
      '<br>',
      MNP::popup_action2('No-Show Reservations With Deposits',
			 ROOTURL . '/src/accounts-payable.php',
			 array('date1' => "document.$formname.date1.value",
			       'date2' => "document.$formname.date2.value")),
      '<br>',
      MNP::popup_action2('Ridership by Month',
			 ROOTURL . '/src/passenger-time.php',
			 array('date1' => "document.$formname.date1.value",
			       'date2' => "document.$formname.date2.value")))
);

foreach ($boxes as $b) { echo MNP::headerBox($b); }


include ADMIN_TEMPLATES . '/form-footer.inc';
include ADMIN_TEMPLATES . '/common-footer.inc';

?>
