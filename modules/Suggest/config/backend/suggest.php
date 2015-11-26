<?php

/* nkList configuration */

// Define the list of suggest
$suggestList = array(
    'classPrefix' => 'suggest',
    'sqlQuery' => 'SELECT id, module, date, user_id FROM '. SUGGEST_TABLE,
    'defaultSortables' => array(
        'order'     => array('module', 'date')
    ),
    'fields' => array(
        'id'        => array('label' => _SUGGESTID),
        'cat'       => array('label' => _CAT),
        'author'    => array('label' => _NICK),
        'date'      => array('label' => _DATE)
    ),
    'emptytable' => _NOSUGGEST,
    'callbackRowFunction' => array('functionName' => 'formatSuggestRow')
);

?>