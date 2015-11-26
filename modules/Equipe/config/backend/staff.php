<?php

/* nkList configuration */

// Define the staff list
$staffList = array(
    'classPrefix' => 'staff',
    'sqlQuery' => 'SELECT S.membre_id, S.date, SC.nom AS catName FROM '. $nuked['prefix'] .'_staff AS S LEFT JOIN '. $nuked['prefix'].'_staff_cat AS SC ON SC.id = S.categorie_id',
    'defaultSortables' => array(
        'order'     => array('date'),
        'dir'       => array('DESC')
    ),
    'fields' => array(
        'username'  => array('label' => 'Pseudo'),
        'date'      => array('label' => 'Date d\'arrivé'),
        'catName'   => array('label' => 'Catégorie')
    ),
    'edit' => array(
        'op'                => 'editStaff',
        //'text'              => _EDITTHISSTAFF
    ),
    'delete' => array(
        'op'                => 'deleteStaff',
        //'text'              => _DELTHISSTAFF,
        'confirmTxt'        => _DELETE_CONFIRM .' %s ! '. _CONFIRM,
        //'confirmField'      => 'membre_id'
    ),
    //'emptytable' => _NOSTAFFINDB,
    'callbackRowFunction' => array(
        'functionName'      => 'formatStaffRow'
    )
);

?>