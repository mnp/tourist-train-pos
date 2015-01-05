<?php
/**
 * Table Definition for tPayment
 */
require_once '/home/mitch/public_html/Touristtrain/DataObjects';

class DataObjects_TPayment extends MNPDataObject 
{

    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    var $__table = 'tPayment';                        // table name
    var $id;                              // int(11)  not_null primary_key auto_increment
    var $tReservation_id;                 // int(11)  not_null
    var $date;                            // date(10)  not_null
    var $amount;                          // real(12)  not_null
    var $payOrRefund;                     // int(6)  not_null
    var $formOfPayment;                   // int(6)  not_null
    var $creditTransactionId;             // string(64)  
    var $creditAuthorizationCode;         // string(6)  
    var $cardName;                        // string(7)  
    var $checkNum;                        // int(6)  

    /* ZE2 compatibility trick*/
    function __clone() { return $this;}

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_TPayment',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
?>