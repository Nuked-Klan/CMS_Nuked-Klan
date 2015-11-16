<?php
/**
 * nkStats.php
 *
 * Librairy to manage stats for Nuked-Klan.org
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');


// Stats URL
define('NK_STATS_URL', 'http://stats.nuked-klan.org/');


/**
 * Prepare stats data to send to Nuked-Klan.org
 *
 * @param string $nuked : Main configuration of CMS
 * @return string : The stats data serialized
 */
function nkStats_formatData($nuked) {
    $data = array();
    $data['hash']               = sha1(HASHKEY);
    $data['time_generate']      = ($nuked['time_generate'] == 'on') ? 1 : 0;
    $data['dateformat']         = $nuked['dateformat'];
    $data['datezone']           = $nuked['datezone'];
    $data['version']            = $nuked['version'];
    $data['date_install']       = $nuked['date_install'];
    $data['langue']             = $nuked['langue'];
    $data['footmessage']        = strlen($nuked['footmessage']);
    $data['nk_status']          = $nuked['nk_status'];
    $data['index_site']         = $nuked['index_site'];
    $data['theme']              = $nuked['theme'];
    $data['keyword']            = strlen($nuked['keyword']);
    $data['description']        = strlen($nuked['description']);
    $data['inscription']        = ($nuked['inscription'] == 'on') ? 1 : 0;
    $data['inscription_mail']   = strlen($nuked['inscription_mail']);
    $data['inscription_avert']  = ($nuked['inscription_avert'] == 'on') ? 1 : 0;
    $data['inscription_charte'] = strlen($nuked['inscription_charte']);
    $data['validation']         = $nuked['validation'];
    $data['user_delete']        = ($nuked['user_delete'] == 'on') ? 1 : 0;
    $data['video_editeur']      = ($nuked['video_editeur'] == 'on') ? 1 : 0;
    $data['suggest_avert']      = $nuked['suggest_avert'];
    $data['irc_chan']           = (strlen($nuked['irc_chan']) > 0 && $nuked['irc_chan'] != 'nuked-klan') ? '1' : '0';
    // TODO : irc_serv OR irc_chan ?
    $data['irc_serv']           = (strlen($nuked['irc_chan']) > 0 && $nuked['irc_chan'] != 'nuked-klan') ? $nuked['irc_serv'] : '0';
    $data['server_ip']          = (strlen($nuked['server_ip']) > 0) ? '1' : '0';
    // TODO : server_ip OR server_game ?
    $data['server_game']        = ($nuked['server_ip'] == 'set' && strlen($nuked['server_game']) > 0) ? $nuked['server_game'] : '0';
    $data['forum_title']        = strlen($nuked['forum_title']);
    $data['forum_desc']         = strlen($nuked['forum_desc']);
    $data['forum_rank_team']    = ($nuked['forum_rank_team'] == 'on') ? 1 : 0;
    $data['forum_field_max']    = $nuked['forum_field_max'];
    $data['forum_file']         = ($nuked['forum_file'] == 'on') ? 1 : 0;
    $data['forum_file_level']   = $nuked['forum_file_level'];
    $data['forum_file_maxsize'] = $nuked['forum_file_maxsize'];
    $data['thread_forum_page']  = $nuked['thread_forum_page'];
    $data['mess_forum_page']    = $nuked['mess_forum_page'];
    $data['hot_topic']          = $nuked['hot_topic'];
    $data['post_flood']         = $nuked['post_flood'];
    $data['gallery_title']      = strlen($nuked['gallery_title']);
    $data['max_img_line']       = $nuked['max_img_line'];
    $data['max_img']            = $nuked['max_img'];
    $data['max_news']           = $nuked['max_news'];
    $data['max_download']       = $nuked['max_download'];
    $data['hide_download']      = ($nuked['hide_download'] == 'on') ? 1 : 0;
    $data['max_liens']          = $nuked['max_liens'];
    $data['max_sections']       = $nuked['max_sections'];
    $data['max_wars']           = $nuked['max_wars'];
    $data['max_archives']       = $nuked['max_archives'];
    $data['max_members']        = $nuked['max_members'];
    $data['max_shout']          = $nuked['max_shout'];
    $data['mess_guest_page']    = $nuked['mess_guest_page'];
    $data['sond_delay']         = $nuked['sond_delay'];
    $data['level_analys']       = $nuked['level_analys'];
    $data['visit_delay']        = $nuked['visit_delay'];
    $data['recrute']            = $nuked['recrute'];
    $data['recrute_charte']     = strlen($nuked['recrute_charte']);
    $data['defie_charte']       = strlen($nuked['defie_charte']);
    $data['birthday']           = $nuked['birthday'];
    $data['avatar_upload']      = ($nuked['avatar_upload'] == 'on') ? 1 : 0;
    $data['avatar_url']         = ($nuked['avatar_url'] == 'on') ? 1 : 0;
    $data['cookiename']         = ($nuked['cookiename'] == 'nuked') ? 0 : 1;
    $data['sess_inactivemins']  = $nuked['sess_inactivemins'];
    $data['sess_days_limit']    = $nuked['sess_days_limit'];
    $data['nbc_timeout']        = $nuked['nbc_timeout'];
    $data['screen']             = $nuked['screen'];
    $data['contact_mail']       = (strlen($nuked['contact_mail']) > 0) ? 1 : 0;
    $data['contact_flood']      = $nuked['contact_flood'];

    for($i = 1; $i <= 9; $i++)
        $data['user_count_'. $i] = 0;

    $dbrUser = nkDB_selectMany(
        'SELECT count(id) AS nbUser, niveau AS level
        FROM '. USER_TABLE .'
        GROUP BY niveau'
    );

    foreach ($dbrUser as $user)
        $data['user_count_'. $user['level']] = $user['nbUser'];

    $dbrDatabase = nkDB_selectOne(
        'SELECT '
        . '(SELECT count(id) FROM '. NEWS_TABLE .') AS nbNews,'
        . '(SELECT count(id) FROM '. FORUM_CAT_TABLE .') AS nbForumCat,'
        . '(SELECT count(id) FROM '. FORUM_TABLE .') AS nbForum,'
        . '(SELECT count(cid) FROM '. GALLERY_CAT_TABLE .') AS nbGalleryCat,'
        . '(SELECT count(sid) FROM '. GALLERY_TABLE .') AS nbGallery,'
        . '(SELECT count(cid) FROM '. LINKS_CAT_TABLE .') AS nbLinksCat,'
        . '(SELECT count(id) FROM '. LINKS_TABLE .') AS nbLinks,'
        . '(SELECT count(warid) FROM '. WARS_TABLE .') AS nbWar,'
        . '(SELECT count(cid) FROM '. DOWNLOAD_CAT_TABLE .') AS nbDownloadCat,'
        . '(SELECT count(id) FROM '. DOWNLOAD_TABLE .') AS nbDownloadCat,'
        . '(SELECT count(cid) FROM '. TEAM_TABLE .') AS nbTeam,'
        . '(SELECT sum(count) FROM '. STATS_TABLE .') AS nbPageView'
    );

    $data['count_news']         = $dbrDatabase['nbNews'];
    $data['count_forum_cat']    = $dbrDatabase['nbForumCat'];
    $data['count_forum']        = $dbrDatabase['nbForum'];
    $data['count_cat_gallery']  = $dbrDatabase['nbGalleryCat'];
    $data['count_gallery']      = $dbrDatabase['nbGallery'];
    $data['count_cat_link']     = $dbrDatabase['nbLinksCat'];
    $data['count_link']         = $dbrDatabase['nbLinks'];
    $data['count_wars']         = $dbrDatabase['nbWar'];
    $data['count_cat_download'] = $dbrDatabase['nbDownloadCat'];
    $data['count_download']     = $dbrDatabase['nbDownload'];
    $data['count_teams']        = $dbrDatabase['nbTeam'];
    $data['count_pagesee']      = $dbrDatabase['nbPageView'];

    $dbrModules = nkDB_selectMany(
        'SELECT nom, niveau, admin
        FROM '. MODULES_TABLE
    );

    foreach ($dbrModules as $module)
        $data['module_'. $module['nom']] = $module['niveau'] .'%'. $module['admin'];

    return serialize($data);
}

/**
 * Send stats data to Nuked-Klan.org
 *
 * @param void
 * @return void
 */
function nkStats_send() {
    global $nuked;

    $timediff = (time() - $nuked['stats_timestamp']) / 60 / 60 / 24 / 60; // 60 Days

    if ($timediff >= 60) {
        $opts = array(
            'http' => array(
                'method'    => 'POST',
                'content'   => 'data='. nkStats_formatData($nuked)
            )
        );

        $context    = stream_context_create($opts);
        $result     = file_get_contents(NK_STATS_URL, false, $context);

        if ($result == 'YES')
            $value = array(time());
        else
            $value = array('value + 86400', 'no-escape');

        nkDB_update(CONFIG_TABLE,
            array('value'), $value,
            'name = \'stats_timestamp\''
        );
    }

    exit;
}

/**
 * Check delay of stats cron and send ajax request to send stats data to Nuked-Klan.org
 *
 * @param void
 * @return void
 */
function nkStats_cron() {
    global $nuked;

    $timediff = (time() - $nuked['stats_timestamp']) / 60 / 60 / 24 / 60; // Tous les 60 jours

    if ($timediff >= 60) {
        nkTemplate_addJSFile(JQUERY_LIBRAIRY, 'librairy');
        nkTemplate_addJSFile('media/js/nkStats.js', 'normal');
    }
}

?>