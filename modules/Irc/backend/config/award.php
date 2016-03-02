<?php

/* nkList configuration */

// Define the list of Irc awards

function getAwardListCfg() {
    return array(
        'sqlQuery' => 'SELECT id, date, text FROM '. IRC_AWARDS_TABLE,
        'defaultSortables' => array(
            'order'     => array('id'),
            'dir'       => array('DESC')
        ),
        'fields' => array(
            'date'          => array('label' => __('DATE')),
            'text'          => array('label' => __('TEXT'))
        ),
        'edit' => array(),
        'delete' => array()
    );
}

/* nkForm configuration */

// List of fields to update

function getAwardFields() {
    return array(
        'text'
    );
}

// Definition of editing Irc award form

function getAwardFormCfg() {
    return array(
        'items' => array(
            'text' => array(
                'label'             => __('TEXT'),
                'type'              => 'textarea',
                'subType'           => 'advanced',
                'cols'              => 60,
                'rows'              => 40,
                'dataType'          => 'html',
                'required'          => true
            )
        ),
        'itemsFooter' => array(
            'submit' => array(
                'type'              => 'submit',
                'value'             => array('ADD_THIS_AWARD', 'MODIFY_THIS_AWARD'),
                'inputClass'        => array('button')
            )
        )
    );
}

?>