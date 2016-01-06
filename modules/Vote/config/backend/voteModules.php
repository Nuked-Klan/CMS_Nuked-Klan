<?php

/* nkForm configuration */

// Definition of editing vote modules form
$voteModulesForm = array(
    'id'        => 'voteModulesForm',
    'action'    => 'index.php?file=Vote&amp;page=admin&amp;op=saveVoteModules',
    'method'    => 'post',
    'items' => array(
        'htmlTitle' => '<div style="text-align: center;">'. __('AUTHORIZED_VOTE_MODULES') .'</div>'
    ),
    'itemsFooter' => array(
        'submit' => array(
            'type'              => 'submit',
            'name'              => 'submit',
            'value'             => __('SEND'),
            'inputClass'        => array('button')
        ),
        'backlink' => array(
            'html'              => '<a class="buttonLink" href="index.php?file=Admin">'. __('BACK') .'</a>'
        )
    )
);

?>