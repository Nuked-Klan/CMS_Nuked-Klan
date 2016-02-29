<?php

/* nkList configuration */

// Define the list of forum

function getForumListCfg() {
    return array(
        'sqlQuery' => 'SELECT A.id, A.nom, A.niveau, A.level, B.nom AS category FROM '. FORUM_TABLE .' AS A LEFT JOIN '. FORUM_CAT_TABLE .' AS B ON B.id = A.cat',
        'defaultSortables' => array(
            'order'     => array('B.ordre', 'B.nom', 'A.ordre', 'A.nom')
        ),
        'fields' => array(
            'nom'       => array('label' => __('NAME')),
            'category'  => array('label' => __('CATEGORY')),
            'niveau'    => array('label' => __('LEVEL_ACCES')),
            'level'     => array('label' => __('LEVEL_POST'))
        ),
        'edit'   => array(),
        'delete' => array()
    );
}

/* nkForm configuration */

// List of fields to update

function getForumFields() {
    return array(
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
}

// Definition of editing forum form

function getForumFormCfg() {
    return array(
        'items' => array(
            'nom' => array(
                'label'             => __('NAME'),
                'type'              => 'text',
                'size'              => 30,
                'dataType'          => 'text',
                'required'          => true
            ),
            'cat' => array(
                'label'             => __('CATEGORY'),
                'type'              => 'select',
                'options'           => array()
            ),
            'comment' => array(
                'label'             => __('DESCRIPTION'),
                'type'              => 'textarea',
                'cols'              => 69,
                'rows'              => 10,
                'inputClass'        => array('editor'),
                'dataType'          => 'html'
            ),
            'image' => array(
                'label'             => __('IMAGE'),
                'type'              => 'text',
                'size'              => 42,
                'uploadField'       => 'uploadImage'
            ),
            'uploadImage' => array(
                'label'             => __('UPLOAD_IMAGE'),
                'type'              => 'file',
                'fileType'          => 'image',
                'uploadDir'         => 'upload/Forum/cat'
            ),
            'niveau' => array(
                'label'             => __('LEVEL_ACCES'),
                'type'              => 'select',
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
                'label'             => __('LEVEL_POST'),
                'type'              => 'select',
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
                'label'             => __('ORDER'),
                'type'              => 'text',
                'value'             => '0',
                'size'              => 2,
                'dataType'          => 'integer',
                'required'          => true
            ),
            'level_poll' => array(
                'label'             => __('LEVEL_POLL'),
                'type'              => 'select',
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
                'label'             => __('LEVEL_VOTE'),
                'type'              => 'select',
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
            )
        ),
        'itemsFooter' => array(
            'submit' => array(
                'type'              => 'submit',
                'value'             => array('ADD_THIS_FORUM', 'MODIFY_THIS_FORUM'),
                'inputClass'        => array('button')
            )
        )
    );
}

?>