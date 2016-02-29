<?php

/* nkList configuration */

// Define the list of Forum moderator

function getModeratorListCfg() {
    return array(
        'sqlQuery' => 'SELECT FM.id, U.pseudo AS nickname, F.nom AS forumName FROM '. FORUM_MODERATOR_TABLE .' AS FM INNER JOIN '. FORUM_TABLE .' AS F ON F.id = FM.forum INNER JOIN '. USER_TABLE .' AS U ON U.id = FM.userId',
        'defaultSortables' => array(
            'order'     => array('F.nom', 'U.pseudo')
        ),
        'fields' => array(
            'nickname'  => array('label' => __('NAME')),
            'forumName' => array('label' => __('FORUM'))
        ),
        'edit'   => array(),
        'delete' => array()
    );
}

/* nkForm configuration */

// List of fields to update

function getModeratorFields() {
    return array(
        'userId',
        'forum'
    );
}

// Definition of editing Forum moderator form

function getModeratorFormCfg() {
    return array(
        'items' => array(
            'userId' => array(
                'label'             => __('NICKNAME'),
                'type'              => 'select',
                'options'           => array(),
                'dataType'          => 'text',
                'required'          => true
            ),
            'forum' => array(
                'label'             => __('FORUM'),
                'type'              => 'select',
                'options'           => array(),
                'dataType'          => 'text',
                'required'          => true
            )
        ),
        'itemsFooter' => array(
            'submit' => array(
                'type'              => 'submit',
                'value'             => array('ADD_THIS_MODERATOR', 'MODIFY_THIS_MODERATOR'),
                'inputClass'        => array('button')
            )
        )
    );
}

?>