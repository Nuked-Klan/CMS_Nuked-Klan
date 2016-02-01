<?php

/* nkForm configuration */

// Definition of prune forum form

function getPruneForumFormCfg() {
    return array(
        'id'        => 'pruneForumForm',
        'action'    => 'index.php?admin=Forum&amp;page=prune&amp;op=do',
        'method'    => 'post',
        'items' => array(
            'prune_day' => array(
                'label'             => __('NUMBER_OF_DAY'),
                'type'              => 'text',
                'size'              => 3,
                'maxlength'         => 3
            ),
            'prune_id' => array(
                'label'             => __('FORUM'),
                'type'              => 'select',
                'options'           => array()
            )
        ),
        'itemsFooter' => array(
            'submit' => array(
                'type'              => 'submit',
                'value'             => __('SEND'),
                'inputClass'        => array('button')
            ),
            'backlink' => array(
                'html'              => '<a class="buttonLink" href="index.php?admin=Forum">'. __('BACK') .'</a>'
            )
        )
    );
}

?>