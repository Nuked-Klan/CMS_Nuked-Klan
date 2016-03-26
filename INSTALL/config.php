<?php
/**
 * config.php
 *
 * Install / update process configuration
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */

return array(
    // Sets Nuked-Klan version to install / update
    'nkVersion' => '1.8',

    // Sets Nuked-Klan minimum version for install / update
    'nkMinimumVersion' => '1.7.5',

    // Sets the minimum version of PHP used by this version of Nuked-Klan
    'minimalPhpVersion' => '5.4',

    // Sets PHP extension list used by this version of Nuked-Klan
    'phpExtension' => array(
        'mysql'     => 'required',
        'mysqli'    => 'optional',
        'session'   => 'required',
        'fileinfo'  => 'optional',
        'gd'        => 'required'
    ),

    // Sets upload directory list
    'uploadDir' => array(
        'images/icones',
        'upload/Download',
        'upload/Forum',
        'upload/Forum/cat',
        'upload/Forum/rank',
        'upload/Gallery',
        'upload/News',
        'upload/Sections',
        'upload/Suggest',
        'upload/Team',
        'upload/Team/coverage',
        'upload/Tinymce',
        'upload/Tinymce/thumbnail',
        'upload/User',
        'upload/User/Rank',
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
        'NEW_VERSION_CONCEPT',
        'GITHUB_NK',
        'COMMUNAUTY_NK',
        'NEW_MODULES',
        'NEW_TEMPLATE'
    ),

    // Sets partners key
    'partnersKey' => 'iS5scBmNTNyE6M07Jna3',

    // Sets deprecated files list
    'deprecatedFiles' => array(
        'captcha.php',
        'Includes/font/',
        'Includes/version.php',
        'modules/Textbox/submit.php'
    )
);

?>
