<?php

// Admin menu configuration
return array(
    __('ADMIN_TEAM') => array(
        'img'   => 'modules/Admin/images/icons/teamusers.png'
    ),
    __('RANK_MANAGEMENT') => array(
        'img'   => 'modules/Admin/images/icons/ranks.png',
        'uri'   => array('page' => 'rank')
    ),
    __('TEAM_MEMBER_MANAGEMENT') => array(
        'img'   => 'modules/Admin/images/icons/members.png',
        'uri'   => array('page' => 'member')
    ),
    __('TEAM_STATUS_MANAGEMENT') => array(
        'img'   => 'modules/Admin/images/icons/validuser.png',
        'uri'   => array('page' => 'status')
    )
);

?>