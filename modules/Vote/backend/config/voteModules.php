<?php

/* nkForm configuration */

// Define the list of vote modules

function getVoteModulesFields() {
    return array_column(getVoteModulesList(), 'module');
}

// Definition of editing vote modules form

function getVoteModulesFormCfg() {
    return array(
        'items' => array(
            'htmlTitle' => '<div style="text-align: center;">'. __('AUTHORIZED_VOTE_MODULES') .'</div>'
        ),
        'itemsFooter' => array(
            'submit' => array(
                'type'              => 'submit',
                'inputClass'        => array('button')
            )
        )
    );
}

?>