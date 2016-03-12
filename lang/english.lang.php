<?php
defined('INDEX_CHECK') or die ('You can\'t run this file alone.');

// For 1.7.x compatibility

// install / update
define('_NKVERSION', '1.8');
define("_WELCOMEINSTALL",'Welcome to the '._NKVERSION.' version of Nuked-Klan ');
define("_GUIDEINSTALL","The installation guide will help you though all stages of the website creation<br /><b>Please do not delete the nuked-klan copyright whilst using nuked-klan.</b> ");
define("_TYPEINSTALL","What do you want to do?");
define("_INSTALL","Speed Installation");
define("_INSTALLPASPAS","Installation with assistance");
define("_UPGRADE","Upgrade with assistance");
define("_INSTALLNK",'Nuked-Klan '._NKVERSION.' Installation');
define("_INSTALLPROGRESS","Installation in progress...");
define('_UNABLE_TO_SAVE_CONFIG_FILE', 'Unable to save your configuration file. Please verify that %s is writeable');
define('_DIRECTORY_NOT_WRITEABLE', '%s is no writeable. Please verify its permissions');
define("_CREATES",".....Created");
define("_MODIFS",".....Modified");
define("_INSERTFIELD","Database installation finished !");
define("_INSERTFINISH","Finished!");
define("_GODCONF","Administrator's configuration");
define("_CONFIG","Configuration");
define("_GODNICK","Nick");
define("_GODPASS","Password");
define("_PASSCONFIRM","confirm");
define("_GODMAIL","email");
define("_GODFAILDED","Nickname or Password failed");
define("_NKUPGRADE",'Update from Nuked-Klan 1.7 to '._NKVERSION);
define("_BADVERSION",'The nuked-klan version you have cannot be upgraded to the '._NKVERSION.' version !');
define("_CONGRATULATION","Congratulation : The installation ended sucessfully...");
define("_REDIRECT","You will be redirected to the homepage of your website");
define("_CLICIFNO","Click here if nothing happens");
define("_ERRORCHMOD","Please remove the <b>install.php</b> and the <b>update.php</b> files from your FTP !<br />change these following files's access permissions to CHMOD 777 :<br /><br /><b>- images/icones/<br />- upload/Download/<br />- upload/Forum/<br />- upload/Gallery/<br />- upload/News/<br />- upload/Suggest/<br />- upload/User/<br />- upload/Wars/</b>");
define("_GOHOME","Click here to go to your website.");
//define("_FIRSTNEWSTITLE","Welcome to your NuKed-KlaN website");
//define("_FIRSTNEWSCONTENT","Welcome to your NuKed-KlaN website, the installation stage has ended, go to the administration to configure and use your website. You can login with the name and the password you indicated at the installation. If any problems occur, please inform us of it in the appropriate forum of <a href=\"http://www.nuked-klan.org\">http://www.nuked-klan.org</a>.");
define("_DBHOST","MySQL Host");
define("_DBUSER","User");
define("_DBPASS","Password");
define("_DBPREFIX","Prefix");
define("_DBNAME","Database's name");
define("_CHMOD","before continuing, please change the access permissions<br /> of the files : conf.inc.php and install.php (or update.php)");
define("_NEXT","Next");
define("_RETRY","Retry");
define("_CONFIGSAVE","Configuration saved");
define("_ERRORCONNECTDB","Database connexion failed, Check your database informations !");
define("_BADCHMOD","Warning, the <b>config.php</b> file can't be written on, please check the file access permissions (CHMOD) !");
define("_CLICNEXTTOINSTALL","Click on next to continue the installation");
define("_CLICNEXTTOUPGRADE","Click on next to continue the upgrade");

define("_ETAPE2","Stage 2");
define("_CHOIX","Explenation");

define("_ETAPE3","Stage 3");
define("_CONFIGSQL", "Sql Configuration");
define("_CONFIGSQLASSIS", "Sql Configuration with remarks");
define("_ETAPE4","Stage 4");
define("_UPGRADESPEED","Speed upgrade");

define("_SECURITE","Security");
define("_DECOUVERTE",'Nuked-klan '._NKVERSION.' : discover the CMS');
define("_NEWSADMIN","A new administration pannel");
define("_PROCHE","An official website close to it's community");
define("_SIMPLIFIE","A simple installation");
define("_DECOUVERTE1",'You will install nuked-klan '._NKVERSION.', this gamer version will help your team/clan to access a simple, secure, efficient and personal website.<p>You will be able to create activity, manage recruitments, plan wars or moderate your server easily.</p>');
define("_NEWSADMIN1","On this new version we created an intuitive, more enjoyable and more useful administration panel. <p>You have the possibility of following the directors actions, seeing the notifications, accessing a private admin chat and you will discover uncountable new features such as temporary bans.</p>");
// ICI
define("_PROCHE1","Connect onto nuked-klan.org, that way we can send you news, warn you for potential hackers and even automaticly update your site. <br /> In addition, our support is always available whenever you have a problem!");
define("_SIMPLIFIE1","The install is not only more logical and much simpler due to it's light design but you will even be assisted with help and conotations. If you already know how to handle a CMS such as nuked klan then chose the quick install. <p> However, if you chose the normal install with the installation assistance, we detail and accompany every step of your installation. </ p>");

define("_NEWNKNEWRELEASE",'New stuff from nuked klan '._NKVERSION);
define("_SECURITE1","This new version has renewed it's security system. We have modernised everything between uploads, password encryption and cookies. <br /> In addition, a signature system has been established, so if you install a dangerous module, we will notify you. We also set up a automatic update system that downloads and installs nuked klan patches whenever a vulnerability is discovered. <br /> We also inform you of the latest news on nuked klan without sending you an email if you chose to accept it ...");
define("_OPTIMISATION","Optimisation");
define("_OPTIMISATION1","Some other features have been added but as we didn't want to make a major update you will have to wait for our next version (which will be released soon)");
define("_ADMINISTRATION1","In order to make a modern and usable admin pannel we prefered starting it from 'scratch' allowing us to connect your website to our servers to allow you more flexebility. You will not have to bother for any hackers as we will warn you how and when to fight them thank to our new artificial intelligence administration.");
define("_BANTEMP","Temporary Ban");
define("_BANTEMP1","A system of temporary ban has been put in place, you get the choice of banning users 1 day, 7 days, 1 month, 1 year or permanently.");
define("_SHOUTBOX","Shoutbox ajax");
define("_SHOUTBOX1","Our new shoutbox allows you to see live messages. No more page reloading : it's in real time!");
define("_ERRORSQL","Management of sql errors");
define("_ERRORSQL1","Whenever our C.M.S. has an error in it's SQL (database) commands, it will report them to you, to nuked-klan.org and it will send an apology to the user, instead of showing the error to the user like our old versions used to do.");
define("_MULTIWARS","Multi-map wars module");
define("_MULTIWARS1","At last we show you the incomming wars instead of only showing the played ones! We also allowed you to chose as many maps as you want instead of allowing you only three.");
define("_COMSYS","Commenting system");
define("_COMSYS1","This new commenting system is coded in AJAX and therefor allows you to rapidly post an comment without bothering for any reloading");
define("_EDITWYS","wysiwyg editor");
define("_EDITWYS1","What You See Is What You Get is a new feature allowing you to see the text as you will get it instead of seeing the old and ugly bbCode tags.");
define("_MISAJ","Update");
define("_MISAJ1","Whenever a bug is detected we will sneak on your website and implement a patch reparing it notifying noone but you.");
define("_CONT","Module Contact");
define("_CONT1","We have added as a default the contact module which is essential to make a website to work.");
define("_ERREURPASS","Password error");
define("_ERREURPASS1","Whenever a user submits the wrong password three times, he will have to complete a captcha in addition to their usual login to log onto his account. This will double the security against hackers.");
define("_DIFFMODIF","Various changes");
define("_DIFFMODIF1","In addition to the changes above, we have made many other such as page 404 (page not found) or other back-office stuff like boosting your website loading speed.");
define("_INSTALLHOST","This is the MYSQL host server adress, it is where all the text, user and message content is... It usualy is 'localhost', nevertheless you can make sure of it/find it in the mail you recieved when registering onto a webhost or on the webhost admin panel.");
define("_INSTALLDBUSER","This is the username used to connect onto the sql database.");
define("_INSTALLDBPASS","This is the password that allows you to connect onto the sql database.");
define("_INSTALLDBPREFIX", "The prefix gives you the possibility to create two installs on the same sql database. The default value is nuked, if however you already have an nuked klan install, we suggest you change it into whatever pleases you.");
define("_INSTALLDBDBNAME","This is the name of database where all the data will be. You have to create it/or use an existing one. You may create one on the webhost admin panel.");
// Check installation:
define("_CHECKCURRING", "<p>Check system requirement ...<br /></p>\n");
define("_PHPVERSION", 'PHP version &ge; 5.1');
define("_MYSQLEXT", 'Extension MySQL');
define("_SESSIONSEXT", 'Extension session');
define("_QUESPHPVERSION", 'You have to ask your hosting service for PHP 5.1. Have you a .htaccess ?');
define("_SESSIONPATH", 'You have to make this directory : ');
define("_DIRECTORY", 'Session directory');
define("_MHASH", 'Extension mhash');
define("_FINFO", "<p>Try to use mimetype to replace finfo</p>");
define("_FORCE", "They are one or more fatal error in your configuration. We can't install Nuked-Klan.\n");
define("_NEXTLANG", "<p>You can force install on click</p>\n");
define("_SYSTEMINSTALL","Your system is ready to start installation.<br />\n");
define("_NEXTSTEP", "Next Step");
// new update
define('_SELECTLANG', 'Choose language');
define('_CHECKVERSION', 'Check current version');
define('_CHECKCOMPATIBILITY', 'Check compatibility');
define('_CHECKACTIVATION', 'Stats\'s activation');
define('_DBSAVE', 'Database\'s save');
define('_CHECKTYPEUPDATE', 'Check type of update');
define('_CURRENTVERSIONUSED', 'Version actually in use is');
define('_CONFIRM', 'Confirm');
define('_NOOTHERVERSION', 'No! I\'m using another version');
define('_PLSSELECTVERSION', 'Please choose your version of Nuked-klan');
define('_WARNCHANGEVERSION', '<b>WARNING !!!</b> Change your version only if you know what you do.<br/>Every mistakes could remove your data.');
define('_CHECKCOMPATIBILITYHOSTING', 'Check your hosting\'s compatibility');
define('_ZIPEXT', 'Zip Extension');
define('_FILEINFOEXT', 'File Info Extension');
define('_HASHEXT', 'Hash Extension');
define('_GDEXT', 'GD Extension');
define('_TESTCHMOD', 'Check CHMOD');
define('_COMPOSANT', 'Composant');
define('_COMPATIBILITY', 'Compatibility');
define('_BADHOSTING', 'Your hosting isn\'t compatible with the new version of Nuked-Klan.');
define('_CONTINUE', 'Continue');
define('_EDITCONFIG', 'Configuration\'s update');

// Common
define('_NONE', 'None');
define('_TITLE', 'Title');
define('_COLOR', 'Color');
define("_ADMIN","Admin");
define("_ADMINISTRATION","Administration");
define("_MEMBERS","Members");
define("_SITEURL","Website's url");
define("_NICK","Nick");
define("_PASSWORD","Pass");
define("_YOUVE","You have");
define("_MODULE","Module");
define("_TLINK","Links");
define("_MESS","message");
define("_ACCOUNT","Account");
define("_3TYPEMIN","3 characters Minimum for the nickname");
define("_4TYPEMIN","4 caracters Minimum for the password");
define("_PASSFAILED","Please re-enter the same password in both fields");
define("_AT","@");
define("_THE","on");// define("_THE","the"); ?
define("_BY","by");
define("_DATE","Date");
define("_URL","Url");
define("_COMMENT","Comment");
define("_RESULT","Results");
define("_REQUIRED","required");
define("_OPTIONAL","optional");
define("_EDIT","Edit");
define("_DEL","Delete");
define("_MORE","Extended Text");
define("_ORDERBY","Order by");
define("_IMAGE","Picture");
define("_NONICK","Please enter your nick !");
define("_HELP","Help");
define("_DESCR","Description");
define("_CAT","Category");
define("_NAME","Name");
define("_BADMAIL","Invalid email address!");
define("_REPLACE","Overwrite");
define("_REPLACEIT","To overwrite it please check the box \""._REPLACE."\"");
define("_AUTOR", "Author");
define("_COUNTRY","Country");
define("_ADDTHE","Added on");
define("_MAIL","Mail");
define("_ALL","All");
define("_CONFIRM_TO_DELETE","You are about to remove");
define("_MO","MB");
define("_KO","KB");
define("_MODIF","Modify");
define("_TEAM","Team");

// Menu
define("_NAV","Menu");
define("_NAVHOME","Home");
define("_NAVNEWS","News");
define("_NAVFORUM","Forum");
define("_NAVDOWNLOAD","Downloads");
define("_NAVTEAM","Team");
define("_NAVMEMBERS","Members");
define("_NAVDEFY","Challenge Us");
define("_NAVRECRUIT","Recruitement");
define("_NAVART","Articles");
define("_NAVSERVER","Servers");
define("_NAVLINKS","Links");
define("_NAVCALENDAR","Calendar");
define("_NAVGALLERY","Gallery");
define("_NAVMATCHS","Wars");
define("_NAVARCHIV","Archives");
define("_NAVIRC","Irc");
define("_NAVGUESTBOOK","Guestbook");
define("_NAVSEARCH","Search");
define("_NAVSTRATS","Strats");
define("_NAVACCOUNT","Account");
define("_NAVADMIN","Administration");
define("_MEMBER","Member");

// Block management
define("_BLOCK","Block");
define("_POSITION","Position");
define("_TYPE","Type");
define("_LEVEL","Level");
define("_LEFT","Left");
define("_RIGHT","Right");
define("_CENTERBLOCK","Center");
define("_FOOTERBLOCK","Footer");
define("_OFF","Unactive");
define("_HTMLBLOCK","Html Block");
define("_MODBLOCK","Module Block");
define("_PAGESELECT","Select the pages where you wish the block to be displayed");
define("_MODIFBLOCK","Modify this block ");

// bbcode
define("_SIZE","Size");
define("_CODE","Code");
define("_ENTERSITEURL","Enter the website's url");
define("_ENTERSITENAME","Enter the website's name");
define("_ENTERIMGURL","Enter the image's url");
define("_ENTERFLASHURL","Enter the flash-animation's url");
define("_ENTERWIDTH","Enter the width");
define("_ENTERHEIGHT","Enter the height");
define("_ENTERTEXT","Enter the text");
define("_TAPEYOURTEXT","write your text here");
define("_ENTERMAIL","Enter email adress");
define("_TEXT","Text");
define("_QUOTE","Quote");
define("_LIST","List");
define("_BOLD","Bold");
define("_ITAL","Italic");
define("_UNDERLINE","Underline");
define("_BBOLD","Bold (Alt + b)");
define("_BITAL","Italic (Alt + i)");
define("_BCENTER","Center (Alt + c)");
define("_BUNDERLINE","Underline (Alt + u)");
define("_BSCREEN","Image (Alt + g)");
define("_BFLASH","Flash (Alt + s)");
define("_BURL","Url (Alt + w)");
define("_BLIST","List (Alt + l)");
define("_BQUOTE","Quote (Alt + q)");
define("_BCODE","Code (Alt + p)");
define("_BMAIL","Email (Alt + m)");
define("_RED","Red");
define("_DARKRED","Dark red");
define("_BLUE","Blue");
define("_DARKBLUE","Dark Blue");
define("_ORANGE","Orange");
define("_BROWN","Brown");
define("_YELLOW","Yellow");
define("_GREEN","Green");
define("_VIOLET","Violet");
define("_OLIVE","Olive");
define("_CYAN","Cyan");
define("_INDIGO","Indigo");
define("_WHITE","White");
define("_BLACK","Black");
define("_POLICE","Police");
define("_HASWROTE","has written");
define("_BBCLOSE","close tags");
define("_BBHELP","BBcode Help");

// block_center.php
define("_BLOKLOGIN","Login");
define("_BLOKSEARCH","Search");
define("_BLOKSHOUT","Shoutbox");
define("_BLOKSTATS","Stats");
define("_LATESTWAR","Latest wars");
define("_NEXTWAR","Next wars");
define("_IRCAWARD","Irc Awards");
define("_SERVERMONITOR","Server monitor");

// block_event.php
define("_JAN","January");
define("_FEB","February");
define("_MAR","March");
define("_APR","April");
define("_MAY","May");
define("_JUN","June");
define("_JUL","July");
define("_AUG","August ");
define("_SEP","September");
define("_OCT","October");
define("_NOV","November");
define("_DEC","December");

// block_login.php
define("_LOGOUT","Logout");
define("_BLOGIN","Connect");
define("_WELCOME","Welcome");
define("_LOGIN","Login");
define("_SAVE","Save");
define("_REGISTER","Registration");
define("_FORGETPASS"," Lost your Pass");
define("_ADMINS","Admins");
define("_LASTMEMBER","Last");
define("_MESSPV","Private message");
define("_NOTREAD","Unread");
define("_READ","Read");
define("_SHOWAVATAR","Show avatar");


// block_rss.php
define("_TITREACTU","View the feed title");
define("_NBRRSS","Number of links displayed");

// block_survey.php
define("_OTHERPOLL","Other Polls");
define("_POLL","Surveys");
define("_VOTE","vote");

// block_theme.php
define("_BTHEMESELECT", "Choice of theme");

// news
define("_NEWSPOSTBY","Posted by");
define("_NEWSCOMMENT","Comments");

// pagination
define("_PAGE","Page");
define("_PREVIOUSPAGE","Previous page");
define("_NEXTPAGE","Next page");

// captcha
define('_MSGCAPTCHA', 'You have made too many attempts, captcha is activate!');

// secu_html
define('_HTMLNOCORRECT', 'HTML code is incorrectly formatted');

// admin
define("_PREFS","Preferences");
define("_MOVEUP","Move up");
define("_MOVEDOWN","Move down");
define("_NEWPAGE","New page");
define("_PREFUPDATED","Preferences were successfully modified");

//define("_ADMINBLOCK","Manage Blocks");
//define("_THEREISNOW","There are currently");
//define("_ONLINE","online.");
//define("_NOMEMBERONLINE","No members are online");
//define("_MEMBERONLINE","Members online");
//define("_SENDMESS","Send him a private message ?");
//define("_ONLINES","On line");
//define("_MODIFMENU","Modify the menu");
//define("_INSERT","Insert");
//define("_PAGEPOLL","You are currently on the Surveys page");
//define("_NOPOLL","There isn't any Survey yes");
//define("_POLLID","Poll id");
//define("_THEREWAS","There is");
//define("_ATTHISPOLL","has this survey ");

define("_SUGGEST","Suggest");
//define("_ONESUGGEST","submit a suggest ?");

//define("_GOTO_PRIVATE_MESSAGES", "Click here to view your messenger");
//define("_CLICK_TO_CLOSE", "Clic to close this message");
//define("_MORESMILIES","All smilies");
//define("_BLOKPARTNERS","Partners");
//define("_TMAIL","Mail");

return array(
    // common
    'BACK'              => 'Back',
    'YES'               => 'Yes',
    'NO'                => 'No',
    'SEND'              => 'Send',
    'VISITOR'           => 'Guest',
    //define("_VISITORS","visitors");
    'IMAGE'             => 'Picture',
    'NONE'              => 'None',
    'MEMBER'            => 'Member',
    'TYPE'              => 'Type',
    'PREFERENCES'       => 'Preferences',
    'ALL'               => 'All',
    'TOKEN_NO_VALID'    => 'Form token isn\'t valid !',
    'DATE'              => 'Date',
    'EDIT'              => 'Edit',
    'TITLE'             => 'Title',
    'COLOR'             => 'Color',
    'URL'               => 'Url',
    'BY'                => 'by',
    'STATS'             => 'Statistics',
    'THE'               => 'the',
    'RESULT'            => 'Results',
    'QUOTE'             => 'Quote',
    'AUTHOR'            => 'Author',
    'FILE'              => 'file',
    'LINE'              => 'line',
    'LINK'              => 'Link',
    'CODE'              => 'Code',
    'NONE_CATEGORY'     => 'None',

    // module name
    'ARCHIVES_MODNAME'  => 'Archives',
    'CALENDAR_MODNAME'  => 'Calendar',
    'COMMENT_MODNAME'   => 'Comments',
    'CONTACT_MODNAME'   => 'Contact',
    'DEFY_MODNAME'      => 'Defy',
    'DOWNLOAD_MODNAME'  => 'Download',
    'EQUIPE_MODNAME'    => 'Equipe',
    'FORUM_MODNAME'     => 'Forum',
    'GALLERY_MODNAME'   => 'Gallery',
    'GAME_MODNAME'      => 'Games',
    'GUESTBOOK_MODNAME' => 'Guestbook',
    'IRC_MODNAME'       => 'IRC',
    'LINKS_MODNAME'     => 'Web Links',
    'MEMBERS_MODNAME'   => 'Members',
    'NEWS_MODNAME'      => 'News',
    'PAGE_MODNAME'      => 'Page',
    'RECRUIT_MODNAME'   => 'Recruit',
    'SEARCH_MODNAME'    => 'Search',
    'SECTIONS_MODNAME'  => 'Sections',
    'SERVER_MODNAME'    => 'Servers',
    'STATS_MODNAME'     => 'Stats',
    'SUGGEST_MODNAME'   => 'Suggestions',
    'SURVEY_MODNAME'    => 'Survey',
    'TEAM_MODNAME'      => 'Team',
    'TEXTBOX_MODNAME'   => 'Shoutbox',
    'VOTE_MODNAME'      => 'Vote',
    'WARS_MODNAME'      => 'Wars',

    // module RSS title
    'NEWS_RSS_TITLE'    => 'Latest %d news',
    'SECTIONS_RSS_TITLE' => 'Latest %d articles',
    'DOWNLOAD_RSS_TITLE' => 'Latest %d downloads',
    'LINKS_RSS_TITLE'   => 'Latest %d links',
    'GALLERY_RSS_TITLE' => 'Latest %d images',
    'FORUM_RSS_TITLE'   => 'Latest %d topics',

    // ban.php
    // nkHandle_bannedUser function (nuked.php)
    'BAN_FINISHED'      => ' isn\'t ban, this period is arrived at expiration: [<a href="index.php?file=Admin&page=user&op=main_ip">Link</a>].',
    // ban.php
    '1DAY'              => '1 day',
    '7DAY'              => '1 week',
    '1MONTH'            => '1 month',
    '1YEAR'             => '1 year',
    'FOREVER'           => 'Forever',
    
    // adminInit function (nuked.php)
    'MODULE_OFF'        => 'Sorry, this Module hasn\'t been activated !',
    'NO_ENTRANCE'       => 'Sorry you do not have the permissions of opening this page',
    'ZONE_ADMIN'        => 'This zone is reserved for the Admins, sorry...',
    
    // getCheckNicknameError function (nuked.php)
    'BAD_NICKNAME'      => 'Invalid Nickname, some characters are prohibited.',
    'RESERVED_NICKNAME' => 'This Nickname is already used.',
    'BANNED_NICKNAME'   => 'This Nickname is banned.',
    'NICKNAME_TOO_LONG' => 'Your Nickname is too long.',
    // getCheckEmailError function (nuked.php)
    'BAD_EMAIL'         => 'Your email address is invalid.',
    'BANNED_EMAIL'      => 'This email is banned.',
    'RESERVED_EMAIL'    => 'This email is already used.',
    // nkAction_checkConstant - Includes/nkAction.php
    'MISSING_NKACTION_PARAMETERS'  => '%s parameters is not defined in nkAction configuration !',
    // nkAction_checkConfigurationFile - Includes/nkAction.php
    'MISSING_CFG_FILE'  => 'Configuration file %s don\'t exist !',
    'MISSING_FUNCTION'  => 'Function %s don\'t exist !',
    // nkAction_getSuccessMsg - Includes/nkAction.php
    'DATA_ADDED'        => 'Data was successfully added.',
    'DATA_MODIFIED'     => 'Data was successfully modified.',
    'DATA_DELETED'      => 'Data was successfully removed.',
    // nkAction_edit - Includes/nkAction.php
    'DATA_NO_EXIST'     => 'Data don\'t exist !',
    // nkAction_save - Includes/nkAction.php
    'PREFERENCES_UPDATED' => 'Preferences were successfully modified',
    // nkAction_deleteConfirmation - Includes/nkAction.php
    'OPERATION_CANCELED' => 'Operation was canceled!',
    
    // nkAction_delete - Includes/nkAction.php
    'MISSING_ID_URI'    => 'Id %s is not defined !',
    // nkAction_list - Includes/nkAction.php
    'EDIT_THIS_DATA'    => 'Edit this data',
    'CONFIRM_TO_DELETE_DATA' => 'You are about to remove %s ! Confirm',
    'DELETE_THIS_DATA'  => 'Remove this data',
    'NO_DATA_IN_DB'     => 'No data in the database',
    'ADD_DATA'          => 'Add data',
    // validCaptchaCode - Includes/nkCaptcha.php
    'CT_NO_TOKEN'       => 'Token not found !<br />Please use the form.',
    'CT_BAD_TOKEN'      => 'Bad token !<br />Please use the form.',
    'CT_BAD_JS'         => 'Javascript validation failed !<br /> Please enable javascript.',
    'CT_BAD_FIELD'      => 'NoBot validation failed !<br /> Please use the form.',
    // nkDB_execute - Includes/nkDB/*
    'SQL_ERROR_DETECTED' => 'A SQL error have been detected',
    // nkSitemap_write - Includes/nkSitemap.php
    'WRITE_SITEMAP_FAILED' => 'Can\'t write sitemap.fml file in Nuked-Klan directory<br/>Please update manually CHMOD <strong>0755</strong> on this directory.',
    // nkUpload_check - Includes/nkUpload.php
    'UPLOAD_DIRECTORY_NO_EXIST' => 'The upload directory don\'t exist !',
    'UPLOAD_DIRECTORY_NO_WRITEABLE' => 'The upload directory isn\'t writeable !',
    'UPLOAD_IMAGE_FAILED' => 'Upload image failed !',
    'UPLOAD_FILE_FAILED' => 'Upload file failed !',
    'UPLOAD_HTACCESS_PROHIBITED' => 'Upload .htaccess file is prohibited !',
    'UPLOAD_IMAGE_TOO_BIG' => 'Your image is too large, image size is limited to %d KB',
    'UPLOAD_FILE_TOO_BIG' => 'Your file is too large, file size is limited to %d KB',
    'BAD_FILE_EXTENSION' => 'File extension is not allowed %s',
    'BAD_IMAGE_FORMAT'   => 'Bad image file type!!! Only jpg, png or gif are authorized',
    'IMAGE_ALREADY_EXIST' => 'A image with the same name already exists on your ftp.',
    'FILE_ALREADY_EXIST' => 'A file with the same name already exists on your ftp.',
    'REPLACE_FILE'      => 'To overwrite it please check the box "Overwrite"',
    'OVERWRITE'         => 'Overwrite',
    // nkUserSocial_getLinkTitle - Includes/nkUserSocial.php
    'SEND_EMAIL'        => 'Send email',
    'SEE_HOME_PAGE'     => 'View user\'s website %s',
    'USER_LABEL_EMAIL'  => 'Mail public',
    'LABEL_EMAIL'       => 'Mail',
    // views/frontend/banishmentMessage.php
    'IP_BANNED'         => 'You are banned. The access of the website you had has been restrained.',
    'REASON'            => 'Reason :',
    'CONTACT_WEBMASTER' => 'For more information, please contact the webmaster',
    'DURING'            => 'During',
    // views/frontend/notification.php
    // views/frontend/nkAlert/userEntrance.php
    'CLOSE_WINDOW'      => 'Close Windows',
    // views/frontend/nkAlert/nkInstallDirTrue.php
    'REMOVE_DIR_INST'   => 'Please delete Nuked-Klan\'s installation folder (/INSTALL/)',
    // views/frontend/nkAlert/nkInstallFileTrue.php
    'REMOVE_INSTALL_FILES' => 'Please delete files install.php or update.php from your FTP.',
    // views/frontend/nkAlert/nkNewPrivateMsg.php
    'NEW_PRIVATE_MESSAGE' => array(1 => 'You received %d new message', 2 => 'You received %d new messages'),
    'GO_TO_PRIVATE_MESSAGES' => 'Click here to view your messenger',
    // views/frontend/nkAlert/nkSiteClosedLogged.php
    'YOUR_SITE_IS_CLOSED' => 'Your website is under Maintenance and can only be viewed by administrators. If you aren\'t logged in any more, please do so here :',
    // views/frontend/nkAlert/noExist.php
    'NO_EXIST'          => 'Sorry either this page does not exist or the address that you typed is incorrect',
    // Sorry this page does not exist or the address which you typed is incorrect
    // TODO : See modules/404/lang/french.lang.php
    
    // views/frontend/nkAlert/userEntrance.php
    'USER_ENTRANCE'     => 'Sorry, this part of the website is for registered Users only.',
    'LOGIN_USER'        => 'Login',
    'REGISTER_USER'     => 'Register', // Registration
    // views/frontend/websiteClosed.php
    'WEBSITE_CLOSED'    => 'Sorry, this website is currently closed, Please try again later.',
    // Includes/blocks/block_survey.php TODO : Temporary
    // sondage - modules/Survey/index.php TODO : Temporary
    // views/frontend/modules/Forum/viewTopic.php
    // views/frontend/modules/Vote/voteForm.php
    'TO_VOTE'           => 'Vote',
    
    // views/frontend/modules/Forum/main.php
    // bloc login
    'WHO_IS_ONLINE'     => 'Who is on-line?',
);

?>
