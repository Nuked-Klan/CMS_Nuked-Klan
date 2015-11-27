<?php

/* nkList configuration */

// Define the list of modules
$modulesList = array(
    'classPrefix' => 'modules',
    'sqlQuery' => 'SELECT id, nom, niveau, admin FROM '. MODULES_TABLE,
    'defaultSortables' => array(
        'order'     => array('nom')
    ),
    'fields' => array(
        'nom'       => array('label' => _NAME),
        'status'    => array('label' => _STATUS),
        'niveau'    => array('label' => _LEVELACCES),
        'admin'     => array('label' => _LEVELADMIN)
    ),
    'edit' => array(
        'op'                => 'edit',
        'text'              => _MODULEEDIT
    ),
    'emptytable' => _NOMODULEINDB,
    'callbackRowFunction' => array(
        'functionName'      => 'formatModuleRow'
    )
);

?>