<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
defined('INDEX_CHECK') or die ('You can\'t run this file alone.');

// Update param
define('UPDATE_URL', 'http://nuked-klan.org/');

// Admin access for adminInit function
define('ADMINISTRATOR_ACCESS', 2);
define('SUPER_ADMINISTRATOR_ACCESS', 9);

// Table names
define('ACTION_TABLE', $db_prefix . '_action');
define('BANNED_TABLE', $db_prefix . '_banned');
define('BLOCK_TABLE', $db_prefix . '_block');
define('CALENDAR_TABLE', $db_prefix . '_calendar');
define('COMMENT_TABLE', $db_prefix . '_comment');
define('COMMENT_MODULES_TABLE', $db_prefix . '_comment_modules');
define('CONFIG_TABLE', $db_prefix . '_config');
define('CONTACT_TABLE', $db_prefix . '_contact');
define('DEFY_TABLE', $db_prefix . '_defie');
//define('DEFY_PREF_TABLE', $db_prefix . '_defie_pref');
define('DISCUSSION_TABLE', $db_prefix . '_discussion');
define('DOWNLOAD_TABLE', $db_prefix . '_downloads');
define('DOWNLOAD_CAT_TABLE', $db_prefix . '_downloads_cat');
define('SQL_ERROR_TABLE', $db_prefix . '_erreursql');
define('FORUM_TABLE', $db_prefix . '_forums');
define('FORUM_CAT_TABLE', $db_prefix . '_forums_cat');
define('FORUM_MESSAGES_TABLE', $db_prefix . '_forums_messages');
define('FORUM_MODERATOR_TABLE', $db_prefix . '_forums_moderator');
define('FORUM_OPTIONS_TABLE', $db_prefix . '_forums_options');
define('FORUM_POLL_TABLE', $db_prefix . '_forums_poll');
define('FORUM_RANK_TABLE', $db_prefix . '_forums_rank');
define('FORUM_READ_TABLE', $db_prefix . '_forums_read');
define('FORUM_THREADS_TABLE', $db_prefix . '_forums_threads');
define('FORUM_VOTE_TABLE', $db_prefix . '_forums_vote');
define('GALLERY_TABLE', $db_prefix . '_gallery');
define('GALLERY_CAT_TABLE', $db_prefix . '_gallery_cat');
define('GAMES_TABLE', $db_prefix . '_games');
define('GAMES_MAP_TABLE', $db_prefix . '_games_map');
define('GAMES_PREFS_TABLE', $db_prefix . '_games_prefs');
define('GUESTBOOK_TABLE', $db_prefix . '_guestbook');
define('IRC_AWARDS_TABLE', $db_prefix . '_irc_awards');
define('LINKS_TABLE', $db_prefix . '_liens');
define('LINKS_CAT_TABLE', $db_prefix . '_liens_cat');
//define('MATCH_TABLE', $db_prefix . '_match');
define('MODULES_TABLE', $db_prefix . '_modules');
define('NBCONNECTE_TABLE', $db_prefix . '_nbconnecte');
define('NEWS_TABLE', $db_prefix . '_news');
define('NEWS_CAT_TABLE', $db_prefix . '_news_cat');
define('NOTIFICATIONS_TABLE', $db_prefix . '_notification');
define('PAGE_TABLE', $db_prefix . '_page');
define('PHP_SESSIONS_TABLE', $db_prefix . '_tmpses');
define('RECRUIT_TABLE', $db_prefix . '_recrute');
//define('RECRUIT_PREF_TABLE', $db_prefix . '_recrute_pref');
define('SECTIONS_TABLE', $db_prefix . '_sections');
define('SECTIONS_CAT_TABLE', $db_prefix . '_sections_cat');
define('SERVER_TABLE', $db_prefix . '_serveur');
define('SERVER_CAT_TABLE', $db_prefix . '_serveur_cat');
define('SESSIONS_TABLE', $db_prefix . '_sessions');
//define('SHOUTBOX_TABLE', $db_prefix . '_shoutbox');
define('SMILIES_TABLE', $db_prefix . '_smilies');
define('STATS_TABLE', $db_prefix . '_stats');
define('STATS_VISITOR_TABLE', $db_prefix . '_stats_visitor');
define('SUGGEST_TABLE', $db_prefix . '_suggest');
define('SURVEY_TABLE', $db_prefix . '_sondage');
define('SURVEY_CHECK_TABLE', $db_prefix . '_sondage_check');
define('SURVEY_DATA_TABLE', $db_prefix . '_sondage_data');
define('TEAM_TABLE', $db_prefix . '_team');
define('TMPSES_TABLE', $db_prefix . '_tmpses');
define('TEAM_MEMBERS_TABLE', $db_prefix . '_team_members');
define('TEAM_RANK_TABLE', $db_prefix . '_team_rank');
define('TEAM_STATUS_TABLE', $db_prefix . '_team_status');
define('TEXTBOX_TABLE', $db_prefix . '_shoutbox');
define('USERBOX_TABLE', $db_prefix . '_userbox');
define('USER_TABLE', $db_prefix . '_users');
define('USER_DETAIL_TABLE', $db_prefix . '_users_detail');
define('USER_SOCIAL_TABLE', $db_prefix .'_user_social');
define('VOTE_TABLE', $db_prefix . '_vote');
define('VOTE_MODULES_TABLE', $db_prefix . '_vote_modules');
define('WARS_TABLE', $db_prefix . '_match');
define('WARS_FILES_TABLE', $db_prefix . '_match_files');

// Table ID names
define('BLOCK_TABLE_ID', 'bid');
define('DOWNLOAD_CAT_TABLE_ID', 'cid');
define('GALLERY_TABLE_ID', 'sid');
define('GALLERY_CAT_TABLE_ID', 'cid');
define('LINKS_CAT_TABLE_ID', 'cid');
define('NEWS_CAT_TABLE_ID', 'nid');
define('SECTIONS_TABLE_ID', 'artid');
define('SECTIONS_CAT_TABLE_ID', 'secid');
define('SERVER_TABLE_ID', 'sid');
define('SERVER_CAT_TABLE_ID', 'cid');
define('SURVEY_TABLE_ID', 'sid');
define('SURVEY_DATA_TABLE_ID', 'sid');
define('TEAM_TABLE_ID', 'cid');
define('USERBOX_TABLE_ID', 'mid');
define('USER_DETAIL_TABLE_ID', 'user_id');
define('WARS_TABLE_ID', 'warid');

// Notification level
define('NOTIFICATION_INFO', 1);
define('NOTIFICATION_ERROR', 2);
define('NOTIFICATION_SUCCESS', 3);
define('NOTIFICATION_WARNING', 4);

?>