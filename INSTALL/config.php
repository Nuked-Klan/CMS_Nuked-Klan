<?php

return array(
    // Sets Nuked-Klan version to install / update
    'nkVersion' => '1.7.13',

    // Sets Nuked-Klan minimum version for install / update
    'nkMinimumVersion' => '1.7.5',

    // Sets the minimum version of PHP used by this version of Nuked-Klan
    'minimalPhpVersion' => '5.1.0',

    // Sets PHP extension list used by this version of Nuked-Klan
    'phpExtension' => array(
        'mysql'     => 'required',
        'session'   => 'required',
        'fileinfo'  => 'optional',
        'gd'        => 'required'
    ),

    // Sets upload directory list
    'uploadDir' => array(
        'upload/Download',
        'upload/Forum',
        'upload/Gallery',
        'upload/News',
        'upload/Suggest',
        'upload/User',
        'upload/Wars'
    ),

    // Sets changelog list
    'changelog' => array(
        'SECURITY',
        'OPTIMISATION',
        'ADMINISTRATION',
        'BAN_TEMP',
        'SHOUTBOX',
        'SQL_ERROR',
        'MULTI_WARS',
        'COMMENT_SYSTEM',
        'WYSIWYG_EDITOR',
        'CONTACT',
        'PASSWORD_ERROR',
        'VARIOUS_MODIF'
    ),

    // Sets info list display in footer
    'infoList' => array(
        'DISCOVERY',
        'NEWSADMIN',
        'INSTALL_AND_UPDATE',
        'COMMUNAUTY_NK'
    ),

    // Sets partners key
    'partnersKey' => 'iS5scBmNTNyE6M07Jna3',

    // Sets deprecated files list
    'deprecatedFiles' => array(
        'Includes/version.php'
    )
);

?>