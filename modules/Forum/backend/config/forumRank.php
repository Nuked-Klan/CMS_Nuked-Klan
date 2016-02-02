<?php

/* nkList configuration */

// Define the list of forum rank

function getForumRankListCfg() {
    return array(
        'sqlQuery' => 'SELECT id, nom, type, post FROM '. FORUM_RANK_TABLE,
        'defaultSortables' => array(
            'order'     => array('type', 'post'),
            'dir'       => array('DESC', 'ASC')
        ),
        'fields' => array(
            'nom'       => array('label' => __('NAME')),
            'type'      => array('label' => __('TYPE')),
            'post'      => array('label' => __('MESSAGES'))
        ),
        'edit'   => array(),
        'delete' => array()
    );
}

/* nkForm configuration */

// List of fields to update

function getForumRankFields() {
    return array(
        'nom',
        'type',
        'post',
        'image'
    );
}

// Definition of editing forum rank form

function getForumRankFormCfg() {
    return array(
        'items' => array(
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
                'value'             => 'http://',
                'size'              => 42,
                'maxlength'         => 200
            ),
            'uploadImage' => array(
                'label'             => __('UPLOAD_IMAGE'),
                'type'              => 'file',
                'fileType'          => 'image',
                'uploadDir'         => 'upload/Forum/rank',
                'urlField'          => 'image'
            ),
            'post' => array(
                'label'             => __('MESSAGES'),
                'type'              => 'text',
                'size'              => 4,
                'value'             => 0,
                'maxlength'         => 5,
                'dataType'          => 'numeric',
                'required'          => true,
                'noempty'           => true
            ),
            'type' => array(
                'type'              => 'hidden',
                'value'             => 0
            )
        ),
        'itemsFooter' => array(
            'submit' => array(
                'type'              => 'submit',
                'value'             => array('CREATE_RANK', 'MODIFY_THIS_RANK'),
                'inputClass'        => array('button')
            )
        )
    );
}

?>