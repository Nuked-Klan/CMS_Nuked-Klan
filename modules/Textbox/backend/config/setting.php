<?php

/* nkForm configuration */

// List of fields to update

function getTextboxSettingFields() {
    return array(
        'max_shout',
        'textbox_avatar'
    );
}

// Definition of editing Textbox setting form

function getTextboxSettingFormCfg() {
    return array(
        'infoNotification'      => __('NOTIFY_TEXTBOX_INFOS_DISPLAY'),
        'items' => array(
            'max_shout' => array(
                'label'             => __('NUMBER_SHOUT'),
                'type'              => 'text',
                'size'              => 2,
                'dataType'          => 'integer',
                'required'          => true
            ),
            'textbox_avatar' => array(
                'label'             => __('DISPLAY_AVATAR'),
                'type'              => 'checkbox',
                'inputValue'        => 'on',
                'defaultValue'      => 'off'
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