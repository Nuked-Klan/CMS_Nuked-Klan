<?php

// List of fields to update
$forumField = array(
    'nom',
    'comment',
    'cat',
    'moderateurs',
    'image',
    'niveau',
    'level',
    'ordre',
    'level_poll',
    'level_vote'
);

// Definition of editing forum form
$forumForm = array(
    'id'        => 'editForumForm',
    'action'    => 'index.php?file=Forum&amp;page=admin&amp;op=saveForum',
    'method'    => 'post',
    'enctype'   => 'multipart/form-data',
    'items' => array(
        'nom' => array(
            'label'             => '<b>'. _NAME .' : </b>',
            'type'              => 'text',
            'name'              => 'titre',
            'size'              => 30
        ),
        'cat' => array(
            'label'             => '<b>'. _CAT .' : </b>',
            'type'              => 'select',
            'name'              => 'cat',
            'options'           => array()
        ),
        'comment' => array(
            'label'             => '<b>'. _DESCR .' : </b>',
            'type'              => 'text',
            'name'              => 'description',
            'rows'              => 10,
            'rows'              => 69,
            'inputClass'        => array('editor')
        ),
        'image' => array(
            'label'             => '<b>'. _IMAGE .' : </b>',
            'type'              => 'text',
            'name'              => 'urlImageForum',
            'size'              => 42
        ),
        'upImageForum' => array(
            'label'             => '<b>'. _UPLOADIMAGE .' : </b>',
            'type'              => 'file',
            'name'              => 'upImageForum'
        ),
        'niveau' => array(
            'label'             => '<b>'. _LEVELACCES .' : </b>',
            'type'              => 'select',
            'name'              => 'niveau',
            'options'           => array(
                0 => 0,
                1 => 1,
                2 => 2,
                3 => 3,
                4 => 4,
                5 => 5,
                6 => 6,
                7 => 7,
                8 => 8,
                9 => 9
            )
        ),
        'level' => array(
            'label'             => '<b>'. _LEVELPOST .' : </b>',
            'type'              => 'select',
            'name'              => 'level',
            'options'           => array(
                0 => 0,
                1 => 1,
                2 => 2,
                3 => 3,
                4 => 4,
                5 => 5,
                6 => 6,
                7 => 7,
                8 => 8,
                9 => 9
            )
        ),
        'ordre' => array(
            'label'             => '<b>'. _ORDER .' : </b>',
            'type'              => 'text',
            'name'              => 'ordre',
            'value'             => '0',
            'size'              => 2
        ),
        'level_poll' => array(
            'label'             => '<b>'. _LEVELPOLL .' : </b>',
            'type'              => 'select',
            'name'              => 'level_poll',
            'options'           => array(
                0 => 0,
                1 => 1,
                2 => 2,
                3 => 3,
                4 => 4,
                5 => 5,
                6 => 6,
                7 => 7,
                8 => 8,
                9 => 9
            )
        ),
        'level_vote' => array(
            'label'             => '<b>'. _LEVELVOTE .' : </b>',
            'type'              => 'select',
            'name'              => 'level_vote',
            'options'           => array(
                0 => 0,
                1 => 1,
                2 => 2,
                3 => 3,
                4 => 4,
                5 => 5,
                6 => 6,
                7 => 7,
                8 => 8,
                9 => 9
            )
        ),
        'moderatorList' => array(
            'fakeLabel'         => '<b>'. _MODO .' : </b>',
            'html'              => ''
        ),
        'moderateurs' => array(
            'label'             => '<b>'. _MODERATEUR .' : </b>',
            'type'              => 'select',
            'name'              => 'modo',
            'options'           => array()
        )
    ),
    'itemsFooter' => array(
        'submit' => array(
            'type'              => 'submit',
            'name'              => 'submit',
            'value'             => _ADDTHISFORUM,
            'inputClass'        => array('button')
        ),
        'backlink' => array(
            'html'              => '<a class="buttonLink" href="index.php?file=Forum&amp;page=admin&amp;op=main_cat">'. _BACK .'</a>'
        )
    )
);

?>