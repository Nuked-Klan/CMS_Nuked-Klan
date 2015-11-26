<?php

/* nkList configuration */

// Define the list of comment
$commentList = array(
    'classPrefix' => 'comment',
    'sqlQuery' => 'SELECT id, im_id, date, autor, autor_id, module FROM '. COMMENT_TABLE,
    'defaultSortables' => array(
        'order'     => array('id'),
        'dir'       => array('DESC'),
    ),
    'fields' => array(
        'date'      => array('label' => _DATE),
        'autor'     => array('label' => _NICK),
        'module'    => array('label' => _MODULE)
    ),
    'edit' => array(
        'op'                => 'editComment',
        'text'              => _EDITTHISCOM
    ),
    'delete' => array(
        'op'                => 'deleteComment',
        'text'              => _DELTHISCOM,
        'confirmTxt'        => _DELCOMMENT .' %s ! '. _CONFIRM,
        'confirmField'      => 'autor'
    ),
    'emptytable' => _NOCOMMENT,
    'callbackRowFunction' => array('functionName' => 'formatCommentRow')
);

?>