<?php

/* nkList configuration */

// Define the list of forum category

function getForumCategoryListCfg() {
    return array(
        'sqlQuery' => 'SELECT id, nom, ordre FROM '. FORUM_CAT_TABLE,
        'defaultSortables' => array(
            'order'     => array('ordre', 'nom')
        ),
        'fields'   => array(
            'nom'       => array('label' => __('CATEGORY')),
            'ordre'     => array('label' => __('ORDER'))
        ),
        'edit'     => array(),
        'delete'   => array()
    );
}

/* nkForm configuration */

// List of fields to update

function getForumCategoryFields() {
    return array(
        'nom',
        'image',
        'niveau',
        'ordre'
    );
}

// Definition of editing forum category form

function getForumCategoryFormCfg() {
    return array(
        'checkform' => true,
        'infoNotification'      => __('NOTIFY_FORUM_IMAGE_SIZE'),
        'items' => array(
            'htmlForumCategoryImage' => '',
            'nom' => array(
                'label'             => __('NAME'),
                'type'              => 'text',
                'size'              => 30,
                'dataType'          => 'text',
                'required'          => true,
                'noempty'           => true
            ),
            'image' => array(
                'label'             => __('IMAGE'),
                'type'              => 'text',
                'size'              => 42
            ),
            'uploadImage' => array(
                'label'             => __('UPLOAD_IMAGE'),
                'type'              => 'file',
                'fileType'          => 'image',
                'uploadDir'         => 'upload/Forum/cat',
                'urlField'          => 'image'
            ),
            'niveau' => array(
                'label'             => __('LEVEL'),
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
                'dataType'          => 'numeric',
                'required'          => true,
                'noempty'           => true
            )
        ),
        'itemsFooter' => array(
            'submit' => array(
                'type'              => 'submit',
                'value'             => array('CREATE_CATEGORY', 'MODIFY_THIS_CATEGORY'),
                'inputClass'        => array('button')
            )
        )
    );
}

?>