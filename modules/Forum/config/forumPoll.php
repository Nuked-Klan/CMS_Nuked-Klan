<?php

/* nkForm configuration */

// List of fields to update

function getForumPollFields() {
    return array(
        'title'
    );
}

// Definition of editing forum poll form

function getForumPollFormCfg() {
    return array(
        'dataName'  => 'forumPoll',
        'formStyle' => 'table',
        'items' => array(
            'title' => array(
                'label'             => __('QUESTION'),
                'type'              => 'text',
                'size'              => 40,
                'dataType'          => 'text',
                'required'          => true
            )
        ),
        'itemsFooter' => array(
            'submit' => array(
                'type'              => 'submit',
                'value'             => array('ADD_THIS_POLL', 'MODIF_THIS_POLL'),
                'inputClass'        => array('nkButton')
            )
        )
    );
}

?>