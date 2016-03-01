<?php

// Admin menu configuration
return array(
    __('ADMIN_SERVER') => array(
        'img'   => 'modules/Admin/images/icons/speedometer.png'
    ),
    __('ADD_SERVER') => array(
        'img'   => 'modules/Admin/images/icons/windows_terminal.png',
        'uri'   => array('op' => 'edit')
    ),
    __('CATEGORY_MANAGEMENT') => array(
        'img'   => 'modules/Admin/images/icons/folder_full.png',
        'uri'   => array('page' => 'category')
    ),
    __('ADD_CATEGORY') => array(
        'img'   => 'modules/Admin/images/icons/add_to_folder.png',
        'uri'   => array('page' => 'category', 'op' => 'edit')
    )
    //__('PREFERENCES') => array(
    //    'img'   => 'modules/Admin/images/icons/process.png',
    //    'uri'   => array('page' => 'setting')
    //)
);

?>