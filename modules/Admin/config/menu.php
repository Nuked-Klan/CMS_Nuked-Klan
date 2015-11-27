<?php

/* nkList configuration */

// Define the list of menu
$menuList = array(
    'classPrefix' => 'menu',
    'sqlQuery' => 'SELECT bid, active, position, titre, nivo FROM '. BLOCK_TABLE .' WHERE type = \'menu\'',
    'fields' => array(
        'titre'     => array('label' => _NAME),
        'block'     => array('label' => _BLOCK),
        'position'  => array('label' => _POSITION),
        'nivo'      => array('label' => _LEVEL)
    ),
    'edit' => array(
        'op'                => 'editMenu',
        'text'              => _EDIT
    ),
    //'delete' => array(
    //    'op'                => 'deleteMenu',
    //    'text'              => _DELETE,
    //    'confirmTxt'        => _DELETE_CONFIRM .' %s ! '. _CONFIRM,
    //    'confirmField'      => 'titre'
    //),
    'emptytable' => _NOMENUINDB,
    'callbackRowFunction' => array(
        'functionName'      => 'formatMenuRow'
    )
);

// Define the list of menu line
$menuLineList = array(
    'classPrefix' => 'menuLine',
    'fields' => array(
        'position'  => array(
            'type'          => 'positionLink',
            'label'         => '&lt; # &gt;',
            'labelUp'       => _UP,
            'labelDown'     => _DOWN,
        ),
        'delbox'    => array(
            'type'          => 'checkbox',
            'label'         => _DELBOX,
            'checkboxName'  => 'id',
            'formAction'    => 'index.php?file=Admin&amp;page=menu&amp;op=deleteMenuLine',
            'submitTxt'     => _DEL,
            'confirmTxt'    => _SURDELLINE
        ),
        'title'     => array('label' => _TITLE),
        'url'       => array('label' => _URL),
        'comment'   => array('label' => _COMMENT),
        'blank'     => array('label' => _NEWPAGE),
        'nivo'      => array('label' => _LEVEL)
    ),
    'edit' => array(
        'op'                => 'editMenuLine',
        'text'              => _EDIT
    ),
    'emptytable' => _NOMENULINKINDB,
    'callbackRowFunction' => array(
        'functionName'      => 'formatMenuLineRow'
    )
);


?>