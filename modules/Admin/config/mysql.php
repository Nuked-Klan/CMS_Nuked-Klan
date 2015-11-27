<?php

/* nkList configuration */

// Define the MySQL table list for optimization
$optimizeMysqlList = array(
    'classPrefix' => 'mysqlTable',
    'sqlQuery' => 'SHOW TABLE STATUS FROM `'. $global['db_name'] .'`',
    'fields' => array(
        'table'     => array('label' => _TABLE),
        'size'      => array('label' => _SIZE),
        'status'    => array('label' => _STATUT),
        'gain'      => array('label' => _SPACESAVED)
    ),
    'callbackRowFunction' => array(
        'functionName'      => 'formatMysqlTableRow'
    )
);

?>