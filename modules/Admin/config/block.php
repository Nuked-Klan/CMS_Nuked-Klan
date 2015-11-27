<?php

/* nkList configuration */

// Define the list of block
$blockList = array(
    'classPrefix' => 'block',
    'sqlQuery' => 'SELECT active, position, titre, module, content, type, nivo, bid FROM '. BLOCK_TABLE,
    'defaultSortables' => array(
        'order'     => array('active', 'position'),
        'dir'       => array('DESC', 'ASC')
    ),
    'fields' => array(
        'titre'     => array('label' => _TITLE),
        'block'     => array('label' => _BLOCK),
        'position'  => array(
            'type'      => 'positionLink',
            'label'     => _POSITION,
            'labelUp'   => _BLOCKUP,
            'labelDown' => _BLOCKDOWN
        ),
        'type'      => array('label' => _TYPE),
        'nivo'      => array('label' => _LEVEL)
    ),
    'edit' => array(
        'op'                => 'edit',
        'text'              => _BLOCKEDIT
    ),
    'delete' => array(
        'op'                => 'delete',
        'text'              => _BLOCKDEL,
        'confirmTxt'        => _DELETE_CONFIRM .' %s ! '. _CONFIRM,
        'confirmField'      => 'name'
    ),
    'emptytable' => _NOBLOCKINDB,
    'callbackRowFunction' => array(
        'functionName'      => 'formatBlockRow'
    )
);

?>