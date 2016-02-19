<?php

/* nkList configuration */

// Define the list of Team

function getTeamListCfg() {
    return array(
        'sqlQuery' => 'SELECT T.cid, T.titre, T.ordre, G.name AS gameName FROM '. TEAM_TABLE .' AS T LEFT JOIN '. GAMES_TABLE .' AS G ON G.id = T.game',
        'defaultSortables' => array(
            'order'     => array('game', 'ordre')
        ),
        'fields' => array(
            'titre'     => array('label' => __('NAME')),
            'gameName'  => array('label' => __('GAME')),
            'ordre'     => array('label' => __('ORDER'))
        ),
        'edit' => array(),
        'delete' => array()
    );
}

/* nkForm configuration */

// List of fields to update

function getTeamFields() {
    return array(
        'titre',
        'tag',
        'tag2',
        'image',
        'coverage',
        'ordre',
        'game'
    );
}

// Definition of editing Team form

function getTeamFormCfg() {
    return array(
        'items' => array(
            'titre' => array(
                'label'             => __('NAME'),
                'type'              => 'text',
                'size'              => 32,
                'dataType'          => 'text',
                'required'          => true,
                'noempty'           => true
            ),
            'ordre' => array(
                'label'             => __('ORDER'),
                'type'              => 'text',
                'size'              => 2,
                'dataType'          => 'numeric',
                'required'          => true,
                'noempty'           => true
            ),
            'tag' => array(
                'label'             => __('TAG_PREFIX'),
                'type'              => 'text',
                'size'              => 10,
                'dataType'          => 'text',
                'required'          => true,
                'noempty'           => true
            ),
            'tag2' => array(
                'label'             => __('TAG_SUFFIX'),
                'type'              => 'text',
                'size'              => 10,
                'dataType'          => 'text',
                'required'          => true,
                'noempty'           => true
            ),
            'coverage' => array(
                'label'             => __('IMAGE'),
                'type'              => 'text',
                'size'              => 42,
                'uploadField'       => 'uploadCoverage'
            ),
            'coverageImg' => array(
                'html'              => ''
            ),
            'uploadCoverage' => array(
                'label'             => __('UPLOAD_IMAGE'),
                'type'              => 'file',
                'fileType'          => 'image',
                'uploadDir'         => 'upload/Team/coverage'
            ),
            'imageNotification' => array(
                'html'              => printNotification(__('NOTIFY_LOGO_TEAM'), 'information', array(), true)
            ),
            'image' => array(
                'label'             => __('TEAM_LOGO'),
                'type'              => 'text',
                'size'              => 42,
                'uploadField'       => 'uploadImage'
            ),
            'uploadImage' => array(
                'label'             => __('UPLOAD_IMAGE'),
                'type'              => 'file',
                'fileType'          => 'image',
                'uploadDir'         => 'upload/Team'
            ),
            'game' => array(
                'label'             => __('GAME'),
                'type'              => 'select',
                'options'           => array()
            ),
            'sep' => array(
                'html'              => '&nbsp;'
            )
        ),
        'itemsFooter' => array(
            'submit' => array(
                'type'              => 'submit',
                'value'             => array('ADD_THIS_TEAM', 'MODIFY_THIS_TEAM'),
                'inputClass'        => array('button')
            )
        )
    );
}

?>