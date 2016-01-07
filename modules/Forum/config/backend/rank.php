<?php

/* nkList configuration */

// Define the list of forum rank
$forumRankList = array(
    'css' => array('tablePrefix' => 'forumRank', 'fieldsPrefix' => 'r'),
    'classPrefix' => '',
    'sqlQuery' => 'SELECT id, nom, type, post FROM '. FORUM_RANK_TABLE,
    'defaultSortables' => array(
        'order'     => array('type', 'post'),
        'dir'       => array('DESC', 'ASC')
    ),
    'fields' => array(
        'nom'       => array('label' => _NAME),
        'type'      => array('label' => _TYPE),
        'post'      => array('label' => _MESSAGES)
    ),
    'edit' => array(
        'op'                => 'editRank',
        'imgTitle'          => _EDITTHISRANK
    ),
    'delete' => array(
        'op'                => 'deleteRank',
        'imgTitle'          => _DELTHISRANK,
        'confirmTxt'        => _DELETE_CONFIRM .' %s ! '. _CONFIRM,
        'confirmField'      => 'nom'
    ),
    'emptytable' => _NORANKINDB,
    'callbackRowFunction' => array(
        'functionName'      => 'formatForumRankRow'
    )
);

/* nkForm configuration */

// List of fields to update
$forumRankField = array(
    'nom',
    'type',
    'post',
    'image'
);

// Definition of editing forum rank form
$forumRankForm = array(
    'checkform' => true,
    'id'        => 'editForumRankForm',
    'action'    => 'index.php?file=Forum&amp;page=admin&amp;op=saveRank',
    'method'    => 'post',
    'enctype'   => 'multipart/form-data',
    'items' => array(
        'nom' => array(
            'label'             => '<b>'. _NAME .' : </b>',
            'type'              => 'text',
            'name'              => 'nom',
            'size'              => 30,
            'dataType'          => 'text',
            'required'          => true,
            'noempty'           => true
        ),
        'image' => array(
            'label'             => '<b>'. _IMAGE .' : </b>',
            'type'              => 'text',
            'name'              => 'image',
            'value'             => 'http://',
            'size'              => 42,
            'maxlength'         => 200
        ),
        'upImageRank' => array(
            'label'             => '<b>'. _UPLOADIMAGE .' : </b>',
            'type'              => 'file',
            'name'              => 'upImageRank'
        ),
        'post' => array(
            'label'             => '<b>'. _MESSAGES .' : </b>',
            'type'              => 'text',
            'name'              => 'post',
            'size'              => 4,
            'value'             => 0,
            'maxlength'         => 5,
            'dataType'          => 'numeric',
            'required'          => true,
            'noempty'           => true
        ),
        'type' => array(
            'type'              => 'hidden',
            'name'              => 'type',
            'value'             => 0
        )
    ),
    'itemsFooter' => array(
        'submit' => array(
            'type'              => 'submit',
            'name'              => 'submit',
            'value'             => _CREATERANK,
            'inputClass'        => array('button')
        ),
        'backlink' => array(
            'html'              => '<a class="buttonLink" href="index.php?file=Forum&amp;page=admin&amp;op=main_rank">'. __('BACK') .'</a>'
        )
    )
);

?>