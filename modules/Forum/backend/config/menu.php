<?php

// Admin menu configuration
return array(
    _n('FORUM') => array(
        'img'   => 'modules/Admin/images/icons/speedometer.png'
    ),
    __('ADD_FORUM') => array(
        'img'   => 'modules/Admin/images/icons/add_page.png',
        'op'    => 'edit'
    ),
    __('MODERATOR_MANAGEMENT') => array(
        'img'   => 'modules/Admin/images/icons/teamusers.png',
        'page'  => 'moderator'
    ),
    __('CATEGORY_MANAGEMENT') => array(
        'img'   => 'modules/Admin/images/icons/folder_full.png',
        'page'  => 'category'
    ),
    __('RANK_MANAGEMENT') => array(
        'img'   => 'modules/Admin/images/icons/ranks.png',
        'page'  => 'rank'
    ),
    __('PRUNE') => array(
        'img'   => 'modules/Admin/images/icons/remove_from_database.png',
        'page'  => 'prune'
    ),
    __('PREFERENCES') => array(
        'img'   => 'modules/Admin/images/icons/process.png',
        'page'  => 'setting'
    )
);

?>