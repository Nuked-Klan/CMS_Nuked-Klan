<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
defined('INDEX_CHECK') or die;

function getStats($nuked)
{
    $data = array();
    $data['hash'] = sha1(HASHKEY);
    $data['time_generate'] = ($nuked['time_generate']=="on") ? 1 : 0;
    $data['dateformat'] = $nuked['dateformat'];
    $data['datezone'] = $nuked['datezone'];
    $data['version'] = $nuked['version'];
    $data['date_install'] = $nuked['date_install'];
    $data['langue'] = $nuked['langue'];
    $data['footmessage'] = strlen($nuked['footmessage']);
    $data['nk_status'] = $nuked['nk_status'];
    $data['index_site'] = $nuked['index_site'];
    $data['theme'] = $nuked['theme'];
    $data['keyword'] = strlen($nuked['keyword']);
    $data['description'] = strlen($nuked['description']);
    $data['inscription'] = ($nuked['inscription'] == "on") ? 1 : 0;
    $data['inscription_mail'] = strlen($nuked['inscription_mail']);
    $data['inscription_avert'] = ($nuked['inscription_avert'] == "on") ? 1 : 0;
    $data['inscription_charte'] = strlen($nuked['inscription_charte']);
    $data['validation'] = $nuked['validation'];
    $data['user_delete'] = ($nuked['user_delete'] == "on") ? 1 : 0;
    $data['video_editeur'] = ($nuked['video_editeur'] == "on") ? 1 : 0;
    $data['suggest_avert'] = $nuked['suggest_avert'];
    $data['irc_chan'] = (strlen($nuked['irc_chan'])>0 && $nuked['irc_chan'] != "nuked-klan") ? "1" : "0";
    $data['irc_serv'] = (strlen($nuked['irc_chan'])>0 && $nuked['irc_chan']!="nuked-klan") ? $nuked['irc_serv'] : "0";
    $data['server_ip'] = (strlen($nuked['server_ip'])>0) ? "1" : "0";
    $data['server_game'] = ($server_ip == "set" && strlen($nuked['server_game'])>0) ? $nuked['server_game'] : "0";
    $data['forum_title'] = strlen($nuked['forum_title']);
    $data['forum_desc'] = strlen($nuked['forum_desc']);
    $data['forum_rank_team'] = ($nuked['forum_rank_team']=="on") ? 1 : 0;
    $data['forum_field_max'] = $nuked['forum_field_max'];
    $data['forum_file'] = ($nuked['forum_file']=="on") ? 1 : 0;
    $data['forum_file_level'] = $nuked['forum_file_level'];
    $data['forum_file_maxsize'] = $nuked['forum_file_maxsize'];
    $data['thread_forum_page'] = $nuked['thread_forum_page'];
    $data['mess_forum_page'] = $nuked['mess_forum_page'];
    $data['hot_topic'] = $nuked['hot_topic'];
    $data['post_flood'] = $nuked['post_flood'];
    $data['gallery_title'] = strlen($nuked['gallery_title']);
    $data['max_img_line'] = $nuked['max_img_line'];
    $data['max_img'] = $nuked['max_img'];
    $data['max_news'] = $nuked['max_news'];
    $data['max_download'] = $nuked['max_download'];
    $data['hide_download'] = ($nuked['hide_download'] == "on") ? 1 : 0;
    $data['max_liens'] = $nuked['max_liens'];
    $data['max_sections'] = $nuked['max_sections'];
    $data['max_wars'] = $nuked['max_wars'];
    $data['max_archives'] = $nuked['max_archives'];
    $data['max_members'] = $nuked['max_members'];
    $data['max_shout'] = $nuked['max_shout'];
    $data['mess_guest_page'] = $nuked['mess_guest_page'];
    $data['sond_delay'] = $nuked['sond_delay'];
    $data['level_analys'] = $nuked['level_analys'];
    $data['visit_delay'] = $nuked['visit_delay'];
    $data['recrute'] = $nuked['recrute'];
    $data['recrute_charte'] = strlen($nuked['recrute_charte']);
    $data['defie_charte'] = strlen($nuked['defie_charte']);
    $data['birthday'] = $nuked['birthday'];
    $data['avatar_upload'] = ($nuked['avatar_upload'] == "on") ? 1 : 0;
    $data['avatar_url'] = ($nuked['avatar_url'] == "on") ? 1 : 0;
    $data['cookiename'] = ($nuked['cookiename'] == "nuked") ? 0 : 1;
    $data['sess_inactivemins'] = $nuked['sess_inactivemins'];
    $data['sess_days_limit'] = $nuked['sess_days_limit'];
    $data['nbc_timeout'] = $nuked['nbc_timeout'];
    $data['screen'] = $nuked['screen'];
    $data['contact_mail'] = (strlen($nuked['contact_mail'])>0) ? 1 : 0;
    $data['contact_flood'] = $nuked['contact_flood'];
    $data['dernier_envois'] = round($timediff);
    
    for($i=1; $i<=9; $i++) $data['user_count_'. $i] = 0;
    $sql = mysql_query("SELECT count(id),niveau FROM ". USER_TABLE ." GROUP BY niveau");
    while($rep = mysql_fetch_array($sql))
    {
        $data['user_count_'. $rep[1]] = $rep[0];
    }
    
    $sqlstring = "SELECT "
    . "(SELECT count(id) FROM ". NEWS_TABLE .") as news,"
    . "(SELECT count(id) FROM ". FORUM_CAT_TABLE .") as forumc,"
    . "(SELECT count(id) FROM ". FORUM_TABLE .") as forum,"
    . "(SELECT count(cid) FROM ". GALLERY_CAT_TABLE .") as galc,"
    . "(SELECT count(sid) FROM ". GALLERY_TABLE .") as gal,"
    . "(SELECT count(cid) FROM ". LINKS_CAT_TABLE .") as linkc,"
    . "(SELECT count(id) FROM ". LINKS_TABLE .") as link,"
    . "(SELECT count(warid) FROM ". WARS_TABLE .") as war,"
    . "(SELECT count(cid) FROM ". DOWNLOAD_CAT_TABLE .") as dlc,"
    . "(SELECT count(id) FROM ". DOWNLOAD_TABLE .") as dl,"
    . "(SELECT count(cid) FROM ". TEAM_TABLE .") as team,"
    . "(SELECT sum(count) FROM ". STATS_TABLE .") as pageview;";
    
    $sql = mysql_query($sqlstring);
    $rep = mysql_fetch_array($sql);
    $data['count_news'] = $rep['news'];
    $data['count_forum_cat'] = $rep['forumc'];
    $data['count_forum'] = $rep['forum'];
    $data['count_cat_gallery'] = $rep['galc'];
    $data['count_gallery'] = $rep['gal'];
    $data['count_cat_link'] = $rep['linkc'];
    $data['count_link'] = $rep['link'];
    $data['count_wars'] = $rep['war'];
    $data['count_cat_download'] = $rep['dlc'];
    $data['count_download'] = $rep['dl'];
    $data['count_teams'] = $rep['team'];
    $data['count_pagesee'] = $rep['pageview'];
    
    $sql = mysql_query("SELECT nom, niveau, admin FROM ". MODULES_TABLE);
    while($rep = mysql_fetch_array($sql))
    {
        $data['module_'. $rep[0]] = $rep[1].'%'.$rep[2];
    }
    
    return $data;
}
?>