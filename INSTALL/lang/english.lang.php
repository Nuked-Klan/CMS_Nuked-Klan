<?php
/**
 * english.lang.php
 *
 * English translation for install / update process
 *
 * @version 1.7
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

return array(
    #####################################
    # bbcode->apply()
    #####################################
    'CODE'                  => 'Code',
    'QUOTE'                 => 'Quote',
    'HAS_WROTE'             => 'has written',
    #####################################
    # db->load()
    #####################################
    'UNKNOW_DATABASE_TYPE'  => 'Unknow database type `%s`',
    #####################################
    # dbMySQL->_getDbConnectError()
    #####################################
    'DB_HOST_ERROR'         => 'Please check the server mysql name.',
    'DB_LOGIN_ERROR'        => 'Please check the username and password.',
    'DB_NAME_ERROR'         => 'Control your database name.',
    'DB_CHARSET_ERROR'      => 'Your database does not support %s charset',
    'DB_UNKNOW_ERROR'       => 'Unknow MySQL error',
    #####################################
    # dbTable->getFieldType()
    #####################################
    'FIELD_DONT_EXIST'      => 'Field `%s` don\'t exist',
    #####################################
    # dbTable->checkIntegrity()
    #####################################
    'MISSING_TABLE'         => 'Table `%s` don\'t exist',
    'MISSING_FIELD'         => 'Missing `%s` field in `%s` table',
    #####################################
    # dbTable->checkAndConvertCharsetAndCollation()
    #####################################
    'CONVERT_CHARSET_AND_COLLATION' => '`%s` table convert to `%s` charset and `%s` collation',
    #####################################
    # dbTable->createTable()
    #####################################
    'CREATE_TABLE'          => 'Create `%s` table',
    #####################################
    # dbTable->renameTable()
    #####################################
    'RENAME_TABLE'          => '`%s` table renamed to `%s`',
    #####################################
    # dbTable->dropTable()
    #####################################
    'DROP_TABLE'            => 'Drop `%s` table if exists',
    #####################################
    # dbTable->addField()
    #####################################
    'FIELD_TYPE_NO_FOUND'   => '`%s` field type no found',
    'ADD_FIELD'             => '`%s` field added to `%s` table',
    #####################################
    # dbTable->modifyField()
    #####################################
    'MODIFY_FIELD'          => '`%s` field modified in `%s` table',
    #####################################
    # dbTable->dropField()
    #####################################
    'DROP_FIELD'            => '`%s` field deleted in `%s` table',
    #####################################
    # dbTable->applyUpdateFieldListToData()
    #####################################
    'CALLBACK_UPDATE_FUNCTION_DONT_EXIST' => '`%s` callback update function don\'t exist',
    #####################################
    # install->main()
    #####################################
    'CORRUPTED_CONF_INC'    => 'Corrupted conf.inc.php file, edit the conf.inc.php file.',
    'DB_PREFIX_ERROR'       => 'The prefix is incorrect, edit the conf.inc.php file.',// TODO : ou la table est manquante...
    'LAST_VERSION_SET'      => 'You already have the last version %s of Nuked-Klan',
    'BAD_VERSION'           => 'Your version of Nuked-Klan can not be updated directly. <br/> Please first update to version %s',
    #####################################
    # install->runTableProcessAction()
    #####################################
    'MISSING_FILE'          => 'Missing file : ',
    #####################################
    # install->_formatSqlError()
    #####################################
    'DB_CONNECT_FAIL'       => 'Database connection failed !',
    'FATAL_SQL_ERROR'       => 'An SQL error has occured<br />Error : %s',
    #####################################
    # install->_writeDefaultContent()
    #####################################
    'FIRST_NEWS_TITLE'      => 'Welcome to your NuKed-KlaN website %s',
    'FIRST_NEWS_CONTENT'    => 'Welcome to your NuKed-KlaN website, the installation stage has ended, go to the administration to configure and use your website. You can login with the name and the password you indicated at the installation. If any problems occur, please inform us of it in the appropriate forum of <a href="http://www.nuked-klan.org">http://www.nuked-klan.org</a>.',
    #####################################
    # processConfiguration->_check()
    #####################################
    'MISSING_CONFIG_KEY'    => 'Missing `%s` key in INSTALL/config.php file',
    'CONFIG_KEY_MUST_BE_STRING' => 'Key `%s` must be a string',
    'CONFIG_KEY_MUST_BE_ARRAY' => 'Key `%s` must be a array',
    #####################################
    # view::__construct()
    #####################################
    'VIEW_NO_FOUND'         => 'View file `%s` no found',
    #####################################
    # media/js/runProcess.js
    #####################################
    'CHECK_TABLE_INTEGRITY' => 'Check <b>%s</b> table integrity',
    'SUCCESS'               => 'Success',
    'FAILURE'               => 'Failure',
    'CONVERTED_TABLE_SUCCESS' => 'Table <b>%s</b> successfully converted.',
    'CREATED_TABLE_SUCCESS' => 'Table <b>%s</b> successfully created.',
    'UPDATE_TABLE_SUCCESS'  => 'Table <b>%s</b> successfully updated.',
    'REMOVE_TABLE_SUCCESS'  => 'Table <b>%s</b> successfully removed.',
    'NOTHING_TO_CHECK'      => 'Nothing to check in <b>%s</b> table',
    'NO_CONVERT_TABLE'      => 'No conversion to the <b>%s</b> table',
    'NOTHING_TO_DO'         => 'Nothing to do for <b>%s</b> table',
    'CHECK_ALL_TABLE_INTEGRITY' => 'Check all table integrity',
    'CHECK_TABLE_CHARSET'   => 'Check table charset',
    'CHECK_INTEGRITY_FAILED' => 'There are %d tables corrupted',
    'TABLE_CONVERTION'      => 'All table conversion',
    'CONVERTED_TABLE_FAILED' => 'There are %d tables not converted',
    'INSTALL_SUCCESS'       => 'Installation is complete! All tables have been created.',
    'UPDATE_SUCCESS'        => 'The update is complete! All tables have been changed.',
    'INSTALL_FAILED'        => 'Installation is complete! But errors occurred, %d tables were not created.',
    'UPDATE_FAILED'         => 'The update is complete! But errors occurred, %d tables have not changed.',
    'PRINT_ERROR'           => ' - Error :',
    'UPDATE_TABLE_STEP'     => 'Update table <b>%1$s</b> : Step <b>%2$s</b>',
    'CHECK_TABLE_INTEGRITY_ERROR' => 'An error occurred when checking the table',
    'CREATED_TABLE_ERROR'   => 'An error occurred while creating the table',
    'UPDATE_TABLE_ERROR'    => 'An error occurred when editing the table',
    'STARTING_INSTALL'      => 'Starting the installation.',
    'STARTING_UPDATE'       => 'Starting the update.',
    #####################################
    # media/js/setUserAdmin.js
    #####################################
    'ERROR_NICKNAME'        => 'The nickname must be a minimum of 3 characters and can\'t contain the following characters: $^()\'?%#\<>,;:',
    'ERROR_PASSWORD'        => 'Please enter a password.',
    'ERROR_PASSWORD_CONFIRM' => 'Passwords do not match.',
    'ERROR_EMAIL'           => 'Please enter a valid mail',
    #####################################
    # tables/table.block.c.i.u.php
    #####################################
    'INSERT_DEFAULT_DATA'   => 'Insert default data of `%s` table',
    'APPLY_BBCODE'          => 'Apply BBcode to `%s` field',
    'BLOCK_LOGIN'           => 'Login',
    'NAV'                   => 'Menu',
    'NAV_HOME'              => 'Home',
    'NAV_NEWS'              => 'News',
    'NAV_FORUM'             => 'Forum',
    'NAV_DOWNLOAD'          => 'Downloads',
    'NAV_TEAM'              => 'Team',
    'NAV_MEMBERS'           => 'Members',
    'NAV_DEFY'              => 'Challenge Us',
    'NAV_RECRUIT'           => 'Recruitement',
    'NAV_ART'               => 'Articles',
    'NAV_SERVER'            => 'Servers',
    'NAV_LINKS'             => 'Links',
    'NAV_CALENDAR'          => 'Calendar',
    'NAV_GALLERY'           => 'Gallery',
    'NAV_MATCHS'            => 'Wars',
    'NAV_ARCHIV'            => 'Archives',
    'NAV_IRC'               => 'Irc',
    'NAV_GUESTBOOK'         => 'Guestbook',
    'NAV_SEARCH'            => 'Search',
    'NAV_STRATS'            => 'Strats',
    'MEMBER'                => 'Member',
    'NAV_ACCOUNT'           => 'Account',
    'ADMIN'                 => 'Admin',
    'NAV_ADMIN'             => 'Administration',
    'BLOCK_SEARCH'          => 'Search',
    'POLL'                  => 'Surveys',
    'BLOCK_STATS'           => 'Stats',
    'IRC_AWARD'             => 'Irc Awards',
    'SERVER_MONITOR'        => 'Server monitor',
    'SUGGEST'               => 'Suggest',
    'BLOCK_SHOUTBOX'        => 'Shoutbox',
    'BLOCK_PARTNERS'        => 'Partners',
    'GAME_SERVER_LOCATION'  => 'Game server location',// TODO A verifier
    'INSERT_BLOCK'          => 'Add %s block',
    #####################################
    # tables/table.config.c.i.u.php
    #####################################
    'DELETE_CONFIG'         => 'Delete `%s` configuration in `%s` table',
    'ADD_CONFIG'            => 'Add `%s` configuration in `%s` table',
    'UPDATE_CONFIG'         => 'Update `%s` configuration in `%s` table',
    #####################################
    # tables/table.forums.c.i.u.php
    #####################################
    'FORUM'                 => 'Forum',
    'TEST_FORUM'            => 'Test Forum',
    'REMOVE_EDITOR'         => 'Remove editor',
    #####################################
    # tables/table.forums_cat.c.i.php
    #####################################
    'CATEGORY'              => 'Category',
    #####################################
    # tables/table.forums_rank.c.i.php
    #####################################
    'NEWBIE'                => 'Newbie',
    'JUNIOR_MEMBER'         => 'Junior member',
    'SENIOR_MEMBER'         => 'Senior member',
    'POSTING_FREAK'         => 'Posting Freak',
    'MODERATOR'             => 'Moderator',
    'ADMINISTRATOR'         => 'Administrator',
    #####################################
    # table.forums_read.c.i.u.php
    #####################################
    // TODO ADD_FORUM_READ_DATA
    #####################################
    # tables/table.games.c.i.u.php
    #####################################
    'PREF_CS'               => 'CS Preferences',
    'OTHER_NICK'            => 'Other Nick',
    'FAV_MAP'               => 'Favorite Map',
    'FAV_WEAPON'            => 'Favorite Weapon',
    'SKIN_T'                => 'Terro Skin',
    'SKIN_CT'               => 'CT Skin',
    #####################################
    # tables/table.match.c.i.u.php
    #####################################
    'UPDATE_FIELD'          => '`%s` field updated in `%s` table',
    #####################################
    # tables/table.modules.c.i.u.php
    #####################################
    'DELETE_MODULE'         => 'Delete %s module',
    'ADD_MODULE'            => 'Add %s module',
    #####################################
    # tables/table.news_cat.c.i.php
    #####################################
    'BEST_MOD'              => 'The best MOD for Half-Life',
    #####################################
    # tables/table.notification.i.u.php
    #####################################
    'SUHOSIN'               => 'Be careful the PHP configuration of suhosin.session.encrypt is "On". Please check the documentation, in case of an issue.',
    #####################################
    # tables/table.smilies.c.i.u.php
    #####################################
    // TODO UPDATE_SMILIES
    #####################################
    # tables/table.sondage.c.i.php
    #####################################
    'LIKE_NK'               => 'Do you like Nuked-klan ?',
    #####################################
    # tables/table.sondage_data.c.i.php
    #####################################
    'ROXX'                  => 'it\'s great, carry on!',
    'NOT_BAD'               => 'I guess it\'s alright',
    'SHIET'                 => 'it sucks, stop it!',
    'WHATS_NK'              => 'What\'s nuked-klan?',
    #####################################
    # tables/table.users.c.i.u.php
    #####################################
    'UPDATE_PASSWORD'       => 'Password of `%s` field updated in `%s` table',
    #####################################
    # views/changelog.php
    #####################################
    'NEW_FEATURES_NK'       => 'New features Nuked Klan %s',
    'SECURITY'              => 'Security',
    'SECURITY_DETAIL'       => 'The security was completely reviewed.<br />We can send messages since the official website to leave you some informations.',
    'OPTIMISATION'          => 'Optimisation',
    'OPTIMISATION_DETAIL'   => 'Some parts of Nuked-Klan were optimised like the pagination system to make your website lighter.',
    'ADMINISTRATION'        => 'Administration',
    'ADMINISTRATION_DETAIL' => 'To realize an administration up to date, we preferred to start from scratch and design a system in which administrators, users, machines, and official site would be connected. For this we have set up communication systems such as notifications, actions, discussions admin. This administration has a panel that can transport you anywhere in your administration but also to warn you.',
    'BAN_TEMP'              => 'Temporary ban',
    'BAN_TEMP_DETAIL'       => 'A system of temporary ban was put in place, you have the choice to ban the user 1 day, 7 days, 1 month, 1 year, or permanently.',
    'SHOUTBOX'              => 'Shoutbox ajax',
    'SHOUTBOX_DETAIL'       => 'A new textbox in ajax block was developed. It is capable of displaying who is online, and send / view new messages without reloading the page.',
    'SQL_ERROR'             => 'SQL error handling',
    'SQL_ERROR_DETAIL'      => 'This system works both ways, when a visitor lands on a SQL error, rather than seeing the error, it is redirected to a page of apology, and an error report is sent to SQL administration.',
    'MULTI_WARS'            => 'Multi-map module wars',
    'MULTI_WARS_DETAIL'     => 'The new module allows viewing upcoming games, it also allows to choose the number of maps, then there is one score per map, and then a final score is the average score per map.',
    'COMMENT_SYSTEM'        => 'Comments system',
    'COMMENT_SYSTEM_DETAIL' => 'The new comments system allows quickly to send a comment in ajax and watch the last 4 comments.',
    'WYSIWYG_EDITOR'        => 'WYSIWYG Editor',
    'WYSIWYG_EDITOR_DETAIL' => 'This new system provides a quick view of your message, or other news after shaping.',
    'CONTACT'               => 'Contact module',
    'CONTACT_DETAIL'        => 'We have added the contact module essential to the operation of a website.',
    'PASSWORD_ERROR'        => 'Password error',
    'PASSWORD_ERROR_DETAIL' => 'When a user uses the wrong password three times, then it must copy the security code in addition to login to connect to their account.',
    'VARIOUS_MODIF'         => 'Various modifications',
    'VARIOUS_MODIF_DETAIL'  => 'In addition to the above changes, we made several changes as page 404, where even non-visible changes such as captcha.',
    'NEXT'                  => 'Continue',
    #####################################
    # views/checkCompatibility.php
    #####################################
    'CHECK_COMPATIBILITY_HOSTING' => 'Compatibility check with your hosting',
    'COMPOSANT'             => 'Component',
    'COMPATIBILITY'         => 'compatibility',
    'WEBSITE_DIRECTORY'     => 'Website directory',
    'PHP_VERSION'           => 'PHP version &ge; %s',
    'PHP_VERSION_ERROR'     => 'Erreur PHP',
    'MYSQL_EXT'             => 'Mysql extension',
    'MYSQL_EXT_ERROR'       => 'Erreur Mysql',
    'SESSION_EXT'           => 'Sessions extension',
    'SESSION_EXT_ERROR'     => 'Erreur sessions',
    'FILEINFO_EXT'          => 'File Info extension',
    'FILEINFO_EXT_ERROR'    => 'File Info error',
    'GD_EXT'                => 'GD extension',
    'GD_EXT_ERROR'          => 'Erreur GD',
    'CHMOD_TEST'            => 'Testing of CHMOD',
    'CHMOD_TEST_ERROR'      => 'Error chmod %s',
    'BAD_HOSTING'           => 'Your hosting is not compatible with the new version of Nuked-Klan.',
    'FORCE'                 => 'Force the installation',
    #####################################
    # views/chooseSendStats.php
    #####################################
    'SELECT_STATS'          => 'Enabling anonymous statistics',
    'STATS_INFO'            => '<p>To improve at best CMS Nuked Klan, taking into account the use of the site administrators NK<br/>We have implemented on this new version a system for sending anonymous statistics.</p><p>You can choose to enable or disable the system, but know that you will allow activating the Team Development / Marketing<br/>to better meet your expectations.</p><p>For full transparency, when sending statistics, you will be advised in the administration, of data sent.<br/>Know that at any time you can disable the sending of statistics in the general preferences of your administration.</p>',
    'CONFIRM_STATS'         => 'Yes, I authorize the sending of anonymous statistical to Nuked-Klan',
    'CONFIRM'               => 'Confirm',
    #####################################
    # views/cleaningFiles.php
    #####################################
    'DEPRECATED_FILES'      => 'Deprecated files detected',
    'CLEANING_FILES'        => 'One or many deprecated files cannot be deleted.<br />Please manually delete the following files :',
    'RETRY'                 => 'Retry',
    #####################################
    # views/confIncFailure.php
    #####################################
    'WEBSITE_DIRECTORY_CHMOD' => 'Can\'t write in Nuked-Klan directory<br/>Please update manually CHMOD <strong>0755</strong> on this directory.',
    'CONF_INC_CHMOD_ERROR'  => 'Can\'t change CHMOD file rights conf.inc.php<br/>Please update manually CHMOD <strong>%s</strong> on this file.',
    'WRITE_CONF_INC_ERROR'  => 'There was an error in the file generation conf.inc.php',
    'COPY_CONF_INC_ERROR'   => 'Can not create file backup conf.inc.php<br/>Please download the file and save it manually.',
    'DOWNLOAD'              => 'Download',
    #####################################
    # views/fatalError.php
    #####################################
    'ERROR'                 => 'An error has occured !!!',
    'BACK'                  => 'Back',
    'REFRESH'               => 'Refresh',
    #####################################
    # views/fullPage.php
    #####################################
    'INSTALL_TITLE'         => 'Install Nuked-klan %s',
    'UPDATE_TITLE'          => 'Update Nuked-klan %s',
    'SELECT_LANGUAGE'       => 'Language Selection',
    'SELECT_TYPE'           => 'Type of installation',
    'INSTALL'               => 'Installation',
    'UPDATE'                => 'Update',
    'YES'                   => 'Yes',
    'NO'                    => 'No',
    'QUICK'                 => 'Quick',
    'ASSIST'                => 'Assisted',
    'SELECT_SAVE'           => 'Backing up the database',
    'IN_PROGRESS'           => 'In progress',
    'FINISH'                => 'Done',
    'RESET_SESSION'         => 'Reset',
    'DISCOVERY'             => 'Discover Nuked-Klan !',
    'DISCOVERY_DESCR'       => 'You are about to install a web site based on the CMS Nuked-Klan ...</p><p>With a few clicks and within minutes, enjoy the opportunity to manage your team, guild or clan, using tools specifically designed for this purpose!</p><p>You are not a player but however you want to use Nuked-Klan to achieve your website?<br/>No problem, a general version of (SP) was also developed and is offered expressly for this purpose.</p><p>Adopt a design more suited to the spirit of your event (color palette, logos, ...) becomes, by Nuked-Klan, a veritable breeze. With an impressive collection of graphics and a change (and a creation) of themes certainly one of the easiest CMS market, inevitably you will come to a website that suits you.</p><p>Thank you for the interest and confidence you bring us everyday ... and all these years!',
    'NEWSADMIN'             => 'A new administration',
    'NEWSADMIN_DESCR'       => 'More ergonomic and more complete, the new administration has in this release will satisfy the most picky of you.</p><p>Options as indispensable listing of SQL errors and actions made on the website, the ability to leave notifications between administrators, ... are now directly integrated into the administration panel.</p><p>We also thought about the themes of designers and developers, offering them the possibility to define a precise management of the various elements of design, directly via the internal administration of the site.</p><p>With a security completely revised version, which should ensure the durability and reliability of your website.</p><p>Always attentive to your wants and needs, very few expected options are emerging in this version. Thus, the ability to adjust the time zone of your site, ... (Mention some improvements).',
    'INSTALL_AND_UPDATE'    => 'Installation and update',
    'INSTALL_AND_UPDATE_DESCR' => 'The procedures for installing and updating have been completely revisited and simplified.</p><p>Step by step, everything is now separeted and commented to deal with any problems you might encounter.</p><p>No more loss of data during an update, backup your existing database is automatically executed.</p><p>During installation and update, all steps are now archived in a log file. In case of trouble, this log file will allow our team to assist you during installation procedures (or update) optimally.</p><p>We propose, now, to participate in the evolution of the CMS by sending (anonymous) statistics. With this, we will be able to respond accurately and perfect your expectations, in the coming versions.',
    'COMMUNAUTY_NK'         => 'The NK community',
    'COMMUNAUTY_NK_DESCR'   => 'A thriving community ever, with members of helpfulness is deep and with many skills.<br/>This is a significant benefit which you will benefit by adopting Nuked-Klan and joining the said community.<br/>Naturally, you integrate this large family, always concerned for the welfare of its members.</p><p>Many fan-sites revolve around the CMS. Evidence of enthusiasm and excitement that comes from the use of Nuked-Klan, they represent the backbone of the CMS.</p><p>For this reason (and many others), they bring to our team of developers and the community want to move forward, together, in good humor and with a diligent mind communication.</p><p>This is how we will evolve over the years, always listening to your expectations and your needs.</p><p>Because Nuked-Klan is, above all, your CMS!',
    #####################################
    # views/getPartners.php
    #####################################
    'NO_PARTNERS'           => 'An error occurred while retrieving the list of partners ...',
    #####################################
    # views/installSuccess.php
    #####################################
    'INSTALL_SUCCESS'       => 'Installation is complete',
    'INFO_PARTNERS'         => 'Find our partners and promotional codes, <br/> to make the most of their products/services.',
    'WAIT'                  => 'Please wait...',
    'ACCESS_SITE'           => 'Access your website',
    #####################################
    # views/main.php
    #####################################
    'WELCOME_INSTALL'       => 'Welcome on Nuked-Klan %s',
    'GUIDE_INSTALL'         => 'The installation guide will help you though all stages of the website creation<br /><b>Please do not delete the nuked-klan copyright whilst using nuked-klan.</b>',
    'START_INSTALL'         => 'Start the installation',
    'DETECT_UPDATE'         => 'The wizard has detected an installation of the version : %s of Nuked-Klan',
    'START_UPDATE'          => 'Start the update',
    #####################################
    # views/maliciousScript.php
    #####################################
    'MALICIOUS_SCRIPT_DETECTED' => 'Malicious script detected',
    'DELETE_TURKISH_FILE'   => 'Can\'t delete file. Please delete it manually and check again if it is present.<br/>&nbsp;File: / modules/404/lang.turkish.lang.php',
    'CHECK_AGAIN'           => 'Check again',
    #####################################
    # views/runProcess.php
    #####################################
    'CREATE_DB'             => 'Creating the database',
    'UPDATE_DB'             => 'Updating the Database',
    'WAITING'               => 'Click Start to begin ...',
    'START'                 => 'Start',
    #####################################
    # views/selectLanguage.php
    #####################################
    'FRENCH'                => 'French',
    'ENGLISH'               => 'English',
    'SUBMIT'                => 'Submit',
    #####################################
    # views/selectProcessType.php
    #####################################
    'CHECK_TYPE_INSTALL'    => 'Choice of type of installation',
    'INSTALL_SPEED'         => 'Quick Installation',
    'INSTALL_ASSIST'        => 'Assisted installation',
    'UPDATE_SPEED'          => 'Quick Update',
    'UPDATE_ASSIST'         => 'Assisted Update',
    #####################################
    # views/selectSaveBdd.php
    #####################################
    'TO_SAVE'               => 'Save',
    'SAVE_YOUR_DATABASE'    => 'You can save your database by clicking the link below.',
    #####################################
    # views/setConfig.php
    #####################################
    'CONFIG'                => 'Configuration',
    'DB_HOST'               => 'Mysql Server',
    'INSTALL_DB_HOST'       => 'This is the MySQL server address of your hosting, it contains all your data texts, members, messages ... In general, it is localhost, but in all cases, the address indicated on your registration email your host or in the administration of your hosting.',
    'DB_USER'               => 'User',
    'INSTALL_DB_USER'       => 'This is your identifier allowing you to connect to your MySQL database.',
    'DB_PASSWORD'           => 'Password',
    'INSTALL_DB_PASSWORD'   => 'This is the password for your login that lets you connect to your MySQL database.',
    'DB_PREFIX'             => 'Prefix',
    'INSTALL_DB_PREFIX'     => 'The prefix used to install several times Nuked-Klan on a single MySQL database using a prefix different each time, by default it is \'nuked\', but you can change the way you want.',
    'DB_NAME'               => 'Database name',
    'INSTALL_DB_NAME'       => 'This is the name of your MySQL database, often you need to go in the administration of your hosting to create a database, but sometimes it sure is already provided in your registration email hosting .',
    #####################################
    # views/setUserAdmin.php
    #####################################
    'CREATE_USER_ADMIN'     => 'Creating Administrator Account',
    'NICKNAME'              => 'Nickname',
    'PASSWORD'              => 'Password',
    'PASSWORD_CONFIRM'      => 'Password (confirm)',
    'EMAIL'                 => 'Email',
    #####################################
    # views/setAdminError.php
    #####################################
    'ERROR_FIELDS'          => 'You have not filled the form fields.',
);

?>