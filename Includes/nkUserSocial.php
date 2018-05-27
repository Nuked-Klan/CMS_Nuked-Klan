<?php
/**
 * nkUserSocial.php
 *
 * Manage user social data
 *
 * @version     1.8
 * @link https://nuked-klan.fr Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');


/**
 * Get user social configuration. (Only active)
 *
 * @param void
 * @return array
 */
function nkUserSocial_getConfig() {
    static $dbrUserSocial;

    if (! $dbrUserSocial) {
        $dbrUserSocial = nkDB_selectMany(
            'SELECT name, translateName, cssClass, field, format, protect, openUrl
            FROM '. USER_SOCIAL_TABLE .'
            WHERE active = 1'
        );
    }

    return $dbrUserSocial;
}

/**
 * Get user social image configuration. (Only active field for available / unavailable image)
 *
 * @param void
 * @return array
 */
function nkUserSocial_getImgConfig() {
    global $theme;

    static $imgConfig;

    if (! $imgConfig) {
        $imgConfig = array();

        foreach (nkUserSocial_getActiveFields() as $field) {
            foreach (array('', 'na') as $suffix) {
                if (is_file($themeImg = 'themes/'. $theme .'/images/'. $field . $suffix .'.png'))
                    $imgConfig[$field . $suffix] = $themeImg;
                else
                    $imgConfig[$field . $suffix] = 'images/user/'. $field . $suffix .'.png';
            }
        }
    }

    return $imgConfig;
}

/**
 * Get user social fields. (Only active)
 *
 * @param void
 * @return array
 */
function nkUserSocial_getActiveFields() {
    $userSocialList = nkUserSocial_getConfig();

    return array_column($userSocialList, 'field');
}

/**
 * Get title of user social link.
 *
 * @param array $userSocial : The configuration of user social field.
 * @param array $userData : The user data.
 * @param string $nicknameField : The nickname field name of user data.
 * @return string
 */
function nkUserSocial_getLinkTitle($userSocial, $userData, $nicknameField) {
    if ($userSocial['field'] == 'email')
        return __('SEND_EMAIL');
        // module team : $userData['email']
    else if ($userSocial['field'] == 'url')
        return sprintf(__('SEE_HOME_PAGE'), $userData[$nicknameField]);
    else if ($userSocial['protect'])
        return str2htmlEntities($userData[$userSocial['field']]);
    else
        return $userData[$userSocial['field']];
}

/**
 * Get url of user social link.
 *
 * @param array $userSocial : The configuration of user social field.
 * @return string
 */
function nkUserSocial_getLinkUrl($userSocial, $url) {
    if ($userSocial['format'] === null)
        return '#';

    $url = sprintf($userSocial['format'], $url);

    if ($userSocial['protect'])
        $url = str2htmlEntities($url);// TODO : Check for double encode

    return $url;
}

/**
 * Get link attribute if user social link is open in a new page.
 *
 * @param array $userSocial : The configuration of user social field.
 * @return string
 */
function nkUserSocial_openUrlPage($userSocial) {
    // TODO : Add aim ?
    if ($userSocial['openUrl'] == 1)
        return ' onclick="window.open(this.href); return false;"';

    return '';
}

/**
 * Get list of user social button links.
 *
 * @param array $userData : The user data.
 * @param string $nicknameField : The nickname field name of user data.
 * @return string
 */
function nkUserSocial_getButtonList($userData, $nicknameField = 'nickname') {
    $userSocialList = nkUserSocial_getConfig();

    $html = '';

    foreach ($userSocialList as $userSocial) {
        if (isset($userData[$userSocial['field']]) && $userData[$userSocial['field']] != '') {
            $title   = nkUserSocial_getLinkTitle($userSocial, $userData, $nicknameField);
            $url     = nkUserSocial_getLinkUrl($userSocial, $userData[$userSocial['field']]);
            $openUrl = nkUserSocial_openUrlPage($userSocial);

            $html .= '<a class="nkButton icon '. $userSocial['cssClass'] .' small alone" href="'. $url .'"'. $openUrl .' title="'. $title .'"></a>';
        }
    }

    return $html;
}

/**
 * Get user social button links with this image or unavailable image of user social if user don't have this contact.
 *
 * @param array $userSocial : The configuration of user social field.
 * @param array $userData : The user data.
 * @param string $nicknameField : The nickname field name of user data.
 * @return string
 */
function nkUserSocial_formatImgLink($userSocial, $userData, $nicknameField = 'nickname') {
    global $visiteur, $nuked;

    $imgConfig = nkUserSocial_getImgConfig();

    if ($visiteur >= $nuked['user_social_level']
        && isset($userData[$userSocial['field']])
        && $userData[$userSocial['field']] != ''
    ) {
        $title     = nkUserSocial_getLinkTitle($userSocial, $userData, $nicknameField);
        $url       = nkUserSocial_getLinkUrl($userSocial, $userData[$userSocial['field']]);
        $openUrl   = nkUserSocial_openUrlPage($userSocial);

        return '<a href="'. $url .'"'. $openUrl .'><img class="nkNoBorder" src="'. $imgConfig[$userSocial['field']] .'" alt="" title="'. $title .'" /></a>';
    }
    else {
        return '<img class="nkNoBorder" src="'. $imgConfig[$userSocial['field'] .'na'] .'" alt="" />';
    }
}

/**
 * Get user social label for form or list header.
 *
 * @param array $userSocial : The configuration of user social field.
 * @param string $tsPrefix
 * @return string
 */
function nkUserSocial_getLabel($userSocial, $tsPrefix = '') {
    if ($userSocial['translateName'] == 1) {
        $tsLabel = $tsPrefix .'LABEL_'. strtoupper($userSocial['field']);

        if (translationExist($tsLabel))
            return __($tsLabel);
    }

    return $userSocial['name'];
}

/**
 * Get user social input configuration for a user social field.
 *
 * @param array $userSocial : The configuration of user social field.
 * @return string
 */
function nkUserSocial_getInputConfig($userSocial) {
    /*
    TODO : Replace size HTML attribute by css

    admin user add_user
    email size=30 / maxlength=80

    admin user edit_user
    size=80 / maxlength=80 (edit)

    user edit_account
    url size=40 maxlength=80

    */

    // seulement dans l'admin user
    if ($userSocial['field'] == 'icq') {
        $size = $maxlength = 15;
    }
    else if ($userSocial['field'] == 'msn' || $userSocial['field'] == 'email') {
        $size = 30;
        $maxlength = 80;
    }
    else {
        $size = $maxlength = 30;
    }

    return array(
        'label'             => nkUserSocial_getLabel($userSocial, 'USER_'),
        'type'              => 'text',
        'size'              => $size,
        'maxlength'         => $maxlength,
        'dataType'          => 'text'
    );
}

?>
