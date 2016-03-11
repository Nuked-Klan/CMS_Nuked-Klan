<?php

/* nkList configuration */

// Define the list of Team rank

function getTeamRankListCfg() {
    return array(
        'sqlQuery' => 'SELECT id, titre, ordre FROM '. TEAM_RANK_TABLE,
        'defaultSortables' => array(
            'order'     => array('ordre', 'titre')
        ),
        'fields' => array(
            'titre'     => array('label' => __('TITLE')),
            'ordre'     => array('label' => __('ORDER'))
        ),
        'edit' => array(),
        'delete' => array()
    );
}

/* nkForm configuration */

// List of fields to update

function getTeamRankFields() {
    return array(
        'titre',
        'ordre',
        'image',
        'color'
    );
}

// Definition of editing Team rank form

function getTeamRankFormCfg() {
    return array(
        'items' => array(
            'titre' => array(
                'label'             => __('TITLE'),
                'type'              => 'text',
                'size'              => 30,
                'dataType'          => 'text',
                'required'          => true
            ),
            'image' => array(
                'label'             => __('IMAGE_URL'),
                'type'              => 'text',
                'size'              => 42,
                'uploadField'       => 'uploadImage'
            ),
            'uploadImage' => array(
                'label'             => __('UPLOAD_IMAGE'),
                'type'              => 'file',
                'allowedExtension'  => array('jpg', 'jpeg', 'png', 'gif'),
                'uploadDir'         => 'upload/User/Rank/'
            ),
            'color' => array(
                'label'             => __('COLOR'),
                'type'              => 'color',
                'dataType'          => 'color',
                'required'          => true
            ),
            'ordre' => array(
                'label'             => __('ORDER'),
                'type'              => 'text',
                'size'              => 1,
                'value'             => '0',
                'dataType'          => 'integer',
                'required'          => true
            )
        ),
        'itemsFooter' => array(
            'submit' => array(
                'type'              => 'submit',
                'value'             => array('CREATE_RANK', 'MODIFY_THIS_RANK'),// ADD_THIS_RANK
                'inputClass'        => array('button')
            )
        )
    );
}

?>
