<?php

// List of fields to update
$forumRankField = array(
    'nom',
    'type',
    'post',
    'image'
);

// Definition of editing forum rank form
$forumRankForm = array(
    'id'        => 'editForumForm',
    'action'    => 'index.php?file=Forum&amp;page=admin&amp;op=saveRank',
    'method'    => 'post',
    'enctype'   => 'multipart/form-data',
    'items' => array(
        'nom' => array(
            'label'             => '<b>'. _NAME .' : </b>',
            'type'              => 'text',
            'name'              => 'nom',
            'size'              => 30
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
            'maxlength'         => 5
        )
    ),
    'type' => array(
        'type'              => 'hidden',
        'name'              => 'type',
        'value'             => 0
    ),
    'itemsFooter' => array(
        'submit' => array(
            'type'              => 'submit',
            'name'              => 'submit',
            'value'             => _CREATERANK,
            'inputClass'        => array('button')
        ),
        'backlink' => array(
            'html'              => '<a class="buttonLink" href="index.php?file=Forum&amp;page=admin&amp;op=main_rank">'. _BACK .'</a>'
        )
    )
);

?>