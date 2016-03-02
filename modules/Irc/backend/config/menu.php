<?php

// Admin menu configuration
return array(
    __('ADMIN_IRC') => array(
        'img'   => 'modules/Admin/images/icons/speedometer.png'
    ),
    __('ADD_AWARD') => array(
        'img'   => 'modules/Admin/images/icons/ranks.png',
        'uri'   => array('op' => 'edit')
    ),
    __('PREFERENCES') => array(
        'img'   => 'modules/Admin/images/icons/process.png',
        'uri'   => array('page' => 'setting')
    )
);

?>