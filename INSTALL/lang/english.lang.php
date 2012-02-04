<?php
///////////////////////////////////////////////
/////// GLOBAL 
///////////////////////////////////////////////
define('_SUBMIT', 'Submit');
define('_CONTINUE', 'Continue');
define("_CONFIRM", "Confirm");
define("_HELP","Help");
define('_FRENCH', 'French');
define('_ENGLISH', 'English');
define('_INSTALL', 'Installation');
define('_UPDATE', 'Update');
define('_YES', 'Yes');
define('_NO', 'No');
define('_START', 'Start');
define('_RETRY', 'Retry');
define('_INPROGRESS', 'In progress');
define('_FINISH', 'Done');
define('_BACK', 'Back');
define('_WAIT', 'Please wait...');
define('_ERROR', 'An error has occured !!!');
define('_ERRORTRY', 'An error has occured, please wait.');
///////////////////////////////////////////////
/////// TEXTES BAS DE PAGE 
///////////////////////////////////////////////
define("_DECOUVERTE","Nuked-klan 1.7.9: la découverte");
define("_NEWSADMIN","Une nouvelle administration");
define("_PROCHE","Un site officiel proche de sa communautée");
define("_SIMPLIFIE","Une installation simplifiée");
define("_DECOUVERTE1","Vous allez installer Nuked-Klan 1.7.9, cette version orientée gamers permettra à votre team de bénéficier rapidement d'un site web à votre image.</p><p>Vous pourrez créer une vie à votre groupe, le rassembler facilement, gérer des recrutements, des matchs ou un serveur très facilement.");
define("_NEWSADMIN1","Pour cette nouvelle version, nous avons réalisé une nouvelle administration plus intuitive et plus agréable.</p><p>Vous pourrez suivre les actions des administrateurs, voir les notifications, accéder à un chat privé entre admininistrateurs et découvrir de multiples nouvelles fonctionnalités comme le bannissement temporaire.");
define("_PROCHE1","Connecter 24h/24h avec Nuked-Klan.org, nous pouvons vous envoyer des messages, des avertissements sur certains modules et vous prévenir des mises à jour disponibles pour votre site.<br />Notre support est disponible quelque soit votre problème.");
define("_SIMPLIFIE1","Une installation plus design, plus intuitive, mais surtout une nouvelle installation avec assistance. Si vous avez déjà manié l'installation d'un CMS tel que Nuked-Klan alors choisissez l'installation rapide sauf si vous voulez voir les nouvelles fonctionnalités.</p><p>Cependant avec l'installation avec assistance, nous vous détaillons et accompagnons étape par étape pour chaque case de formulaire, et si malgré cela, vous n'y arrivez pas, pas de soucis, nous sommes là pour vous aider !");
///////////////////////////////////////////////
/////// MENU
///////////////////////////////////////////////
define('_SELECTLANG', 'Language Selection');
define('_SELECTTYPE', 'Type of installation');
define('_SELECTSTATS', 'Enabling anonymous statistics');
define('_SELECTSAVE', 'Backing up the database');
define('_RESETSESSION', 'Reset');
///////////////////////////////////////////////
/////// ACCUEIL
///////////////////////////////////////////////
define("_WELCOMEINSTALL","Welcome on Nuked-Klan 1.7.9");
define("_GUIDEINSTALL","The installation guide will help you though all stages of the website creation<br /><b>Please do not delete the nuked-klan copyright whilst using nuked-klan.</b>");
define('_STARTINSTALL', 'Start the installation');
define('_STARTUPDATE', 'Start the update');
define('_DETECTUPDATE', 'The wizard has detected an installation of the version :');
define('_DETECTUPDATEEND', 'of Nuked-Klan');
define('_BADVERSION', 'Your version of Nuked-Klan can not be updated directly. <br/> Please first update to version 1.7.8 or 1.7.9 RC5.3');
///////////////////////////////////////////////
/////// TEST DE COMPATIBILITE
///////////////////////////////////////////////
define('_CHECKCOMPATIBILITYHOSTING', 'Compatibility check with your hosting');
define('_ZIPEXT', 'Zip extension');
define('_ZIPEXTERROR', 'Zip error');
define('_FILEINFOEXT', 'File Info extension ');
define('_FILEINFOEXTERROR', 'File Info error ');
define('_HASHEXT', 'Hash extension');
define('_HASHEXTERROR', 'Erreur hash');
define('_GDEXT', 'GD extension');
define('_GDEXTERROR', 'Erreur GD');
define('_TESTCHMOD', 'Testing the CHMOD');
define('_TESTCHMODERROR', 'Erreur chmod');
define('_COMPOSANT', 'component');
define('_COMPATIBILITY', 'compatibility');
define('_BADHOSTING', 'Your hosting is not compatible with the new version of Nuked-Klan.');
define('_PHPVERSION', 'PHP version &ge; 5.1');
define('_PHPVERSIONERROR', 'Erreur PHP');
define('_MYSQLEXT', 'Mysql extension');
define('_MYSQLEXTERROR', 'Erreur Mysql');
define('_SESSIONSEXT', 'Sessions extension');
define('_SESSIONEXTERROR', 'Erreur sessions');
define('_FORCE', 'Force the installation');
///////////////////////////////////////////////
/////// STATISTIQUES
///////////////////////////////////////////////
define('_TXTSTATS', '<p>To improve at best CMS Nuked Klan, taking into account the use of the site administrators NK<br/>We have implemented on this new version a system for sending anonymous statistics.</p><p>You can choose to enable or disable the system, but know that you will allow activating the Team Development / Marketing<br/>to better meet your expectations.</p><p>For full transparency, when sending statistics, you will be advised in the administration, of data sent.<br/>Know that at any time you can disable the sending of statistics in the general preferences of your administration.</p>');
define('_CONFIRMSTATS', 'Yes, I authorize the sending of anonymous statistical to Nuked-Klan');
///////////////////////////////////////////////
/////// SAUVEGARDE BDD
///////////////////////////////////////////////
define('_TOSAVE', 'Save');
define('_SAVE', 'Backup');
define('_NOTHANKS', 'No thanks!');
define('_DBSAVED', 'Database saved');
define('_DBSAVEDTXT', 'Your database has been saved, you can download it here :');
///////////////////////////////////////////////
/////// CHOIX DU TYPE D'INSTALLATION
///////////////////////////////////////////////
define('_CHECKTYPEINSTALL', 'Choice of type of installation');
define("_INSTALLSPEED","Quick Installation");
define("_INSTALLASSIST","Assisted installation");
define("_UPDATESPEED","Quick Update");
define("_UPDATEASSIST","Assisted Update");
///////////////////////////////////////////////
/////// INSTALLATION RAPIDE
///////////////////////////////////////////////
define("_DBHOST","Mysql Server");
define("_DBUSER","User");
define("_DBPASS","Password");
define("_DBPREFIX","Prefix");
define("_DBNAME","Database name");
define("_CONFIG","Configuration");
define('_ERROR_HOST', 'Database connection failed! <br/> Please check the server mysql name.');
define('_ERROR_USER', 'Database connection failed! <br/> Please check the username and password.');
///////////////////////////////////////////////
/////// INSTALLATION ASSISTEE
///////////////////////////////////////////////
define("_NEWNK179","Nouveautés Nuked Klan 1.7.9");
define("_SECURITE","Sécurité");
define("_SECURITE1","Cette nouvelle version a retravaillé entièrement la sécurité, les injections SQL et hexadécimales ou même par cookie, upload, et même les mots de passe qui ne sont plus en md5.<br />Nous pouvons aussi vous envoyer des messages depuis le site officiel, afin de vous avertir, informer ou autre...");
define("_OPTIMISATION","Optimisation");
define("_OPTIMISATION1","Nous avons optimisé quelques codes comme le système de pagination afin de rendre votre site légérement moins lourd. Cependant nous n'avons pu optimiser tout le code ne s'agissant que d'une mise à jour 1.7.X.");
define("_ADMINISTRATION","Administration");
define("_ADMINISTRATION1","Afin de réaliser une administration au goût du jour, nous avons préféré repartir de zéro, et concevoir un système dans lequel administrateurs, utilisateurs,
		machines, et site officiel seraient reliés.
		Pour cela, nous avons mis en place des systèmes de communication comme les notifications, les actions, les discussions admin.
		Cette administration possède un panneau capable de vous transporter n'importe où dans votre administration mais aussi de vous avertir.");
define("_BANTEMP","Ban temporaire");
define("_BANTEMP1","Un système de bannissement temporaire a été mis en place, vous avez donc le choix de bannir l'utilisateur 1 jour, 7 jours, 1 mois, 1 an, ou définitivement.");
define("_SHOUTBOX","Shoutbox ajax");
define("_SHOUTBOX1","Un nouveau bloc textbox a été développé, il est capable de dire qui est en ligne, il est en ajax, c'est à dire que vous pouvez envoyer/afficher des nouveaux messages sans recharger
		la page.");
define("_ERRORSQL","Gestions des erreurs SQL");
define("_ERRORSQL1","Ce système est à double sens, lorsqu'un visiteur tombe sur une erreur SQL, plutôt que de voir l'erreur, il est redirigé vers une page d'excuse, et un
		rapport de l'erreur SQL est envoyé dans l'administration.");
define("_MULTIWARS","Multi-map module wars");
define("_MULTIWARS1","Le nouveau module permet de visionner les prochains matchs, il permet aussi de choisir le nombre de maps, il y a alors un score par map, puis un score final
		qui est la moyenne des scores par map.");
define("_COMSYS","Système commentaires");
define("_COMSYS1","Le nouveau système de commentaires permet rapidement d'envoyer un commentaire en ajax et de visionner les 4 derniers commentaires.");
define("_EDITWYS","Editeur WYSIWYG");
define("_EDITWYS1","Ce nouveau système permet d'avoir une visualisation rapide de votre message, news ou autre après mise en forme.");
define("_CONT","Module Contact");
define("_CONT1","Nous avons ajouté le module contact indispensable au fonctionnement d'un site web.");
define("_ERREURPASS","Erreur mot de passe");
define("_ERREURPASS1","Lorsqu'un utilisateur se trompe de mot de passe 3 fois de suite, il doit alors recopier un code de sécurité en plus de son login afin de se connecter à son compte.");
define("_DIFFMODIF","Différentes modifications");
define("_DIFFMODIF1","En plus des modifications précédentes, nous avons effectué diverses modifications comme la page 404, où même des modifications non visibles comme le captcha.");
define("_INSTALLDBHOST","Il s'agit ici de l'adresse du serveur MySQL de votre hébergement, celui-ci contient toutes vos données textes, membres, messages... En général, il s'agit de localhost, mais dans tous les cas, l'adresse est indiquée dans votre mail d'inscription de votre hébergeur ou dans l'administration de votre hébergement.");
define("_INSTALLDBUSER","Il s'agit de votre identifiant qui vous permet de vous connecter à votre base SQL.");
define("_INSTALLDBPASS","Il s'agit du mot de passe de votre identifiant qui vous permet de vous connecter à votre base SQL.");
define("_INSTALLDBPREFIX", "Le prefix permet d'installer plusieurs fois nuked-klan sur une seule base SQL en utilisant un prefix différent à chaque fois, par défaut, il s'agit de nuked, mais vous pouvez le changer comme vous le voulez.");
define("_INSTALLDBNAME","Il s'agit du nom de votre base de données MySQL, souvent vous devez vous rendre dans l'administration de votre hébergement pour créer une base de données, mais quelques fois celle-ci vous est déjà fournie dans le mail d'inscription à votre hébergement.");
///////////////////////////////////////////////
/////// CREATION BDD (INSTALLATION)
///////////////////////////////////////////////
define('_CREATEDB', 'Creating the database');
define('_STARTDB', 'Start creating');
define('_SQLCONNECTOK', 'The database connection has been successful.');
define('_WAITING', 'Click Start to begin ...');
define('_STARTINGINSTALL', 'Starting the installation.');
define('_LOGITXTSUCCESS', 'successfully created.');
define('_LOGITXTERROR', 'An error occurred while creating the table');
define('_LOGITXTENDSUCCESS', 'Installation is complete! All tables have been created.');
define('_LOGITXTENDERRORSTART', 'Installation is complete! But errors occurred,');
define('_LOGITXTENDERROREND', ' tables were not created.');
define('_PRINTERROR', ' - Error :');
define('_WRONGTABLENAME', 'The table name is wrong.');
///////////////////////////////////////////////
/////// CREATION BDD (MISE A JOUR)
///////////////////////////////////////////////
define('_UPDATEDB', 'Updating the Database');
define('_STARTINGUPDATE', 'Starting the update.');
define('_LOGUTXTSUCCESS', 'successfully updated.');
define('_LOGUTXTUPDATE', 'successfully updated.');
define('_LOGUTXTUPDATE2', 'successfully updated.');
define('_LOGUTXTREMOVE', 'successfully removed.');
define('_LOGUTXTREMOVE2', 'successfully removed.');
define('_LOGUTXTERROR', 'An error occurred when editing the table');
define('_LOGUTXTENDSUCCESS', 'The update is complete! All tables have been changed.');
define('_LOGUTXTENDERRORSTART', 'The update is complete! But errors occurred, ');
define('_LOGUTXTENDERROREND', ' tables have not changed.');
define('_DELTURKISH', 'Can\'t delete file. Please delete it manually and restart the update.<br/>nbsp;file: / modules/404/lang.turkish.lang.php');
///////////////////////////////////////////////
/////// CREATION COMPTE ADMIN
///////////////////////////////////////////////
define('_CHECKUSERADMIN', 'Creating Administrator Account');
define('_PSEUDO', 'Pseudo');
define('_PASS', 'Password');
define('_PASS2', 'Password (confirm)');
define('_MAIL', 'Mail');
define('_ERRORFIELDS', 'You have not filled the form fields.');
define('_ERROR_PSEUDO', 'The nickname must be a minimum of 3 characters and can\'t contain the following characters: $^()\'?%#\<>,;:');
define('_ERROR_PASS', 'Please enter a password.');
define('_ERROR_PASS2', 'Passwords do not match.');
define('_ERROR_MAIL', 'Please enter a valid mail');
///////////////////////////////////////////////
/////// FIN INSTALLATION
///////////////////////////////////////////////
define('_INSTALLSUCCESS', 'Installation is complete');
define('_INFOPARTNERS', 'Find our partneraires and promotional codes, <br/> to make the most of their products/services.');
define('_NOPARTNERS', 'An error occurred while retrieving the list of partners ...');
define('_ACCESS_SITE', 'Access your website');
///////////////////////////////////////////////
/////// ERREUR CREATION FICHIER CONF.INC.PHP
///////////////////////////////////////////////
define('_ERRORGENERATECONFINC', 'There was an error in the file generation conf.inc.php');
define('_CONF.INC', 'Please download the content above and place the file in the root of your website.');
define('_INFODLSAVECONFINC', 'Please download the content above and keep this file (it\'s a backup).');
define('_BADCHMOD', 'Can\'t write file <b> conf.inc.php </ b>, check write permissions (CHMOD)!');
define('_DOWNLOAD', 'Download');
define('_CHMOD', 'Can\'t change CHMOD file rights conf.inc.php <br/> Please update manually CHMOD <strong> 0644 </ strong> on this file.');
define('_COPY', 'Can not create file backup conf.inc.php <br/> Please download the file and save it manually.');
///////////////////////////////////////////////
/////// FONCTION BBCODE
///////////////////////////////////////////////
define("_CODE","Code");
define("_QUOTE","Quote");
define("_HASWROTE","has written");
///////////////////////////////////////////////
/////// CONTENU DEMO INSERER DANS LA BDD
///////////////////////////////////////////////
define("_FIRSTNEWSTITLE","Welcome to your NuKed-KlaN website");
define("_FIRSTNEWSCONTENT","Welcome to your NuKed-KlaN website, the installation stage has ended, go to the administration to configure and use your website. You can login with the name and the password you indicated at the installation. If any problems occur, please inform us of it in the appropriate forum of <a href=\"http://www.nuked-klan.org\">http://www.nuked-klan.org</a>.");
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
define("_BLOKLOGIN","Login");
define("_BLOKSEARCH","Search");
define("_BLOKSHOUT","Shoutbox");
define("_BLOKSTATS","Stats");
define("_BLOKPARTNERS","Partners");
define("_ADMIN","Admin");
define("_MEMBER","Member");
define("_POLL","Surveys");
define("_SUGGEST","Suggest");
define("_IRCAWARD","Irc Awards");
define("_SERVERMONITOR","Server monitor");
define('_NEWBIE', 'Newbie');
define('_JUNIORMEMBER', 'Junior member');
define('_SENIORMEMBER', 'Senior member');
define('_POSTINGFREAK', 'Posting Freak');
define('_MODERATOR', 'Moderator');
define('_ADMINISTRATOR', 'Administrator');
define("_PREFCS","CS Preferences");
define("_OTHERNICK","Other Nick");
define("_FAVMAP","Favorite Map");
define("_FAVWEAPON","Favorite Weapon");
define("_SKINT","Terro Skin");
define("_SKINCT","CT Skin");
define("_BESTMOD","The best MOD for Half-Life");
define("_LIKENK","Do you like Nuked-klan?");
define("_ROXX","it\'s great, carry on!");
define("_NOTBAD","I guess it\'s alright");
define("_SHIET","it sucks, stop it!");
define("_WHATSNK","What\'s nuked-klan?");
?>