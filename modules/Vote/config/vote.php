<?php

/* nkForm configuration */

// List of fields to update

function getVoteFields() {
    return array(
        'vote'
    );
}

// Definition of editing Vote form

function getVoteFormCfg() {
    return array(
        'dataName'  => 'vote',
        'items' => array(
            'htmlTitle' => '<div>'. __('ONE_VOTE_ONLY') .'</div>',
            'vote' => array(
                'label'             => __('NOTE'),
                'type'              => 'select',
                'options'           => array(
                    0  => 0,
                    1  => 1,
                    2  => 2,
                    3  => 3,
                    4  => 4,
                    5  => 5,
                    6  => 6,
                    7  => 7,
                    8  => 8,
                    9  => 9,
                    10 => 10
                ),
                'dataType'          => 'numeric',
                'required'          => true,
                'noempty'           => true,
                'range'             => array('min' => 0, 'max' => 10)
            )
        ),
        'itemsFooter' => array(
            'submit' => array(
                'type'              => 'submit',
                'value'             => __('TO_VOTE'),
                'inputClass'        => array('nkButton')
            )
        )
    );
}

?>