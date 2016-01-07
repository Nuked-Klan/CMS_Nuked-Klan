<?php

/* nkForm configuration */

// Definition of prune forum form
$pruneForumForm = array(
    'id'        => 'pruneForumForm',
    'action'    => 'index.php?file=Forum&amp;page=admin&amp;op=doPrune',
    'method'    => 'post',
    'enctype'   => 'multipart/form-data',
    'items' => array(
        'day' => array(
            'label'             => '<b>'. _NUMBEROFDAY .' : </b>',
            'type'              => 'text',
            'name'              => 'day',
            'id'                => 'prune_day',
            'size'              => 3,
            'maxlength'         => 3
        ),
        'prune_id' => array(
            'label'             => '<b>'. _FORUM .' : </b>',
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