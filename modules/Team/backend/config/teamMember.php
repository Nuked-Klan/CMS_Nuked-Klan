<?php

/* nkList configuration */

// Define the list of Team

function getTeamMemberListCfg() {
    return array(
        'sqlQuery' => 'SELECT TM.id, TM.date, U.pseudo AS nickname, T.titre as teamName
            FROM '. TEAM_MEMBERS_TABLE .' AS TM
            LEFT JOIN '. USER_TABLE .' AS U ON U.id = TM.userId
            LEFT JOIN '. TEAM_TABLE .' AS T ON T.cid = TM.team',
        'defaultSortables' => array(
            'order'     => array('date'),
            'dir'       => array('DESC')
        ),
        'fields' => array(
            'nickname'  => array('label' => __('NICK')),
            'date'      => array('label' => __('ARRIVAL_DATE')),
            'teamName'  => array('label' => __('TEAM'))
        ),
        'edit' => array(),
        'delete' => array()
    );
}

/* nkForm configuration */

// List of fields to update

function getTeamMemberFields() {
    return array(
        'userId',
        'team',
        'status',
        'rank'
    );
}

// Definition of editing Team member form

function getTeamMemberFormCfg() {
    return array(
        'items' => array(
            'userId' => array(
                'label'             => __('NICK'),
                'type'              => 'select',
                'options'           => array()
            ),
            'team' => array(
                'label'             => __('TEAM'),
                'type'              => 'select',
                'options'           => array()
            ),
            'status' => array(
                'label'             => __('STATUS'),
                'type'              => 'select',
                'options'           => array()
            ),
            'rank' => array(
                'label'             => __('RANK'),
                'type'              => 'select',
                'options'           => array()
            )
        ),
        'itemsFooter' => array(
            'submit' => array(
                'type'              => 'submit',
                'value'             => array('ADD_THIS_TEAM_MEMBER', 'MODIFY_THIS_TEAM_MEMBER'),
                'inputClass'        => array('button')
            )
        )
    );
}

?>