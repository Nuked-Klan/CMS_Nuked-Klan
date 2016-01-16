<?php

/* nkForm configuration */

// Definition of prune forum form
$pruneForumForm = array(
    'id'        => 'pruneForumForm',
    'action'    => 'index.php?file=Forum&amp;page=admin&amp;op=doPrune',
    'method'    => 'post',
    'items' => array(
        'day' => array(
            'label'             => _NUMBEROFDAY,
            'type'              => 'text',
            'name'              => 'day',
            'id'                => 'prune_day',
            'size'              => 3,
            'maxlength'         => 3
        ),
        'prune_id' => array(
            'label'             => _FORUM,
            'type'              => 'select',
            'name'              => 'prune_id',
            'options'           => array()
        )
    ),
    'itemsFooter' => array(
        'submit' => array(
            'type'              => 'submit',
            'name'              => 'submit',
            'value'             => __('SEND'),
            'inputClass'        => array('button')
        ),
        'backlink' => array(
            'html'              => '<a class="buttonLink" href="index.php?file=Forum&amp;page=admin">'. __('BACK') .'</a>'
        )
    )
);

?>