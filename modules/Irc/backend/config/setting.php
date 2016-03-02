<?php

/* nkForm configuration */

// List of fields to update

function getIrcSettingFields() {
    return array(
        'irc_chan',
        'irc_serv'
    );
}

// Definition of editing Irc setting form

function getIrcSettingFormCfg() {
    return array(
        'items' => array(
            'irc_chan' => array(
                'label'             => __('IRC_CHAN'),
                'labelFormat'       => '%s : #',
                'type'              => 'text',
                'size'              => 15
            ),
            'irc_serv' => array(
                'label'             => __('IRC_SERVER'),
                'type'              => 'text',
                'size'              => 20
            )
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