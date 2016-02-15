<?php

/* nkForm configuration */

// Define the list of user social

function getUserSocialFields() {
    return array_column(getUserSocialList(), 'name');
}

// Definition of editing user social form

function getUserSocialFormCfg() {
    return array(
        'dataName'  => 'userSocial',
        'action'    => 'index.php?file=Admin&amp;page=user&amp;op=send_config',
        'method'    => 'post',
        'items' => array(
            'checkAll' => array(
                'type'              => 'button',
                'value'             => __('_CHECKALL'), // _UNCHECKALL
                'inputClass'        => array('button')
            ),
            'sep' => array(
                'html'              => '&nbsp;',
            ),

            'user_social_level' => array(
                'label'             => __('_LEVELREQUIRED'),
                'type'              => 'select',
                'options'           => array(
                    0 => 0,
                    1 => 1,
                    2 => 2,
                    3 => 3,
                    4 => 4,
                    5 => 5,
                    6 => 6,
                    7 => 7,
                    8 => 8,
                    9 => 9
                )
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
                'html'              => '<a class="buttonLink" href="index.php?file=Admin">'. __('BACK') .'</a>'
            )
        )
    );
}

?>