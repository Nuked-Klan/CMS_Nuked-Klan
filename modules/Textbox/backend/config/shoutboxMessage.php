<?php

/* nkList configuration */

// Define the list of Shoutbox message

function getShoutboxMessageListCfg() {
    return array(
        'sqlQuery' => 'SELECT id, date, auteur, ip FROM '. TEXTBOX_TABLE,
        'defaultSortables' => array(
            'order'     => array('id'),
            'dir'       => array('DESC')
        ),
        'fields' => array(
            'date'      => array('label' => __('DATE')),
            'auteur'    => array('label' => __('NICKNAME')),
            'ip'        => array('label' => __('IP'))
        ),
        'edit' => array(),
        'delete' => array(
            'confirmField'      => 'auteur'
        )
    );
}

/* nkForm configuration */

// List of fields to update

function getShoutboxMessageFields() {
    return array(
        'texte'
    );
}

// Definition of editing Shoutbox message form

function getShoutboxMessageFormCfg() {
    return array(
        'items' => array(
            'nickname' => array(
                'fakeLabel'         => __('NICKNAME')
            ),
            'sep1' => array(
                'html'              => '<br />'
            ),
            'texte' => array(
                'label'             => __('MESSAGE'),
                'type'              => 'textarea',
                'cols'              => 65,
                'rows'              => 10,
                'dataType'          => 'text',
                'htmlspecialchars'  => true,
                'required'          => true
            ),

        ),
        'itemsFooter' => array(
            'submit' => array(
                'type'              => 'submit',
                'value'             => __('MODIFY'),
                'inputClass'        => array('button')
            )
        )
    );
}

?>