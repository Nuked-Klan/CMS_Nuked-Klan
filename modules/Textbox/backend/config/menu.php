<?php

// Admin menu configuration
return array(
    __('TEXTBOX_MODNAME') => array(
        'img'   => 'modules/Admin/images/icons/speedometer.png',
    ),
    __('PREFERENCES') => array(
        'img'   => 'modules/Admin/images/icons/process.png',
        'uri'   => array('page' => 'setting')
    ),
    __('DELETE_ALL_MESSAGE') => array(
        'img'   => 'modules/Admin/images/icons/remove_from_database.png',
        'jsConfirmation' => 'deleteAllShoutboxMsg'
    )
);

?>