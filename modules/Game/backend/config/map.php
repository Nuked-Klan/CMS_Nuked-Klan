<?php

/* nkList configuration */

// Define the list of game map

function getMapListCfg() {
    return array(
        'sqlQuery' => 'SELECT GM.id, GM.name, G.name AS gameName FROM '. GAMES_MAP_TABLE .' AS GM LEFT JOIN '. GAMES_TABLE .' AS G ON G.id = GM.game',
        'defaultSortables' => array(
            'order'     => array('name')
        ),
        'fields' => array(
            'name'      => array('label' => __('NAME')),
            'gameName'  => array('label' => __('GAME'))
        ),
        'edit'   => array(),
        'delete' => array()
    );
}

/* nkForm configuration */

// List of fields to update

function getMapFields() {
    return array(
        'name',
        'image',
        'game',
        'description'
    );
}

// Definition of editing game map form

function getMapFormCfg() {
    return array(
        'items' => array(
            'name' => array(
                'label'             => __('NAME'),
                'type'              => 'text',
                'size'              => 30,
                'dataType'          => 'text',
                'required'          => true
            ),
            'game' => array(
                'label'             => __('GAME'),
                'type'              => 'select',
                'optionsName'       => array('Game', 'game')
            ),
            'image' => array(
                'label'             => __('IMAGE'),
                'type'              => 'text',
                'size'              => 42,
                'uploadField'       => 'uploadImage'
            ),
            'imagePreview' => array(
                'html'              => ''
            ),
            'uploadImage' => array(
                'label'             => __('UPLOAD_IMAGE'),
                'type'              => 'file',
                'allowedExtension'  => array('jpg', 'jpeg', 'png', 'gif'),
                'uploadDir'         => 'upload/Game/map'
            ),
            'description' => array(
                'label'             => __('DESCRIPTION'),
                'type'              => 'textarea',
                'subType'           => 'advanced',
                'cols'              => 69,
                'rows'              => 10,
                'dataType'          => 'html'
            )
        ),
        'itemsFooter' => array(
            'submit' => array(
                'type'              => 'submit',
                'value'             => array('ADD_THIS_MAP', 'MODIFY_THIS_MAP'),
                'inputClass'        => array('button')
            )
        )
    );
}

?>
