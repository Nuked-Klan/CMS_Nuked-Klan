<?php

/* nkList configuration */

// Define the list of Team status

function getTeamStatusListCfg() {
    return array(
        'sqlQuery' => 'SELECT id, name FROM '. TEAM_STATUS_TABLE,
        'defaultSortables' => array(
            'order'     => array('name')
        ),
        'fields' => array(
            'name'     => array('label' => __('NAME'))
        ),
        'edit' => array(),
        'delete' => array()
    );
}

/* nkForm configuration */

// List of fields to update

function getTeamStatusFields() {
    return array(
        'name'
    );
}

// Definition of editing Team status form

function getTeamStatusFormCfg() {
    return array(
        'items' => array(
            'name' => array(
                'label'             => __('NAME'),
                'type'              => 'text',
                'size'              => 25,
                'maxlength'         => 25,
                'dataType'          => 'text',
                'required'          => true
            )
        ),
        'itemsFooter' => array(
            'submit' => array(
                'type'              => 'submit',
                'value'             => array('ADD_THIS_TEAM_STATUS', 'MODIFY_THIS_TEAM_STATUS'),
                'inputClass'        => array('button')
            )
        )
    );
}

?>