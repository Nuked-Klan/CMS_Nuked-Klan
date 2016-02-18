<?php
/**
 * french.lang.php
 *
 * French translation for install / update process
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

return array(
    #####################################
    # bbcode->apply()
    #####################################
    'CODE'                  => 'Code',
    'QUOTE'                 => 'Citation',
    'HAS_WROTE'             => 'a écrit',
    #####################################
    # db->load()
    #####################################
    'UNKNOW_DATABASE_TYPE'  => 'Type de base de données `%s` inconnu',
    #####################################
    # dbMySQL->_getDbConnectError()
    #####################################
    'DB_HOST_CONNECT_ERROR' => 'Veuillez contrôler le nom du serveur %s.',
    'DB_LOGIN_CONNECT_ERROR' => 'Veuillez contrôler le nom d\'utilisateur et le mot de passe.',
    'DB_NAME_CONNECT_ERROR' => 'Veuillez contrôler le nom de la base de données.',
    'DB_CHARSET_CONNECT_ERROR' => 'Votre base de données ne supporte pas l\'interclassement %s',
    #####################################
    # dbTable->getFieldType()
    #####################################
    'FIELD_DONT_EXIST'      => 'Le champ `%s` n\'existe pas',
    #####################################
    # dbTable->checkIntegrity()
    #####################################
    'MISSING_TABLE'         => 'La table `%s` n\'existe pas',
    'MISSING_FIELD'         => 'Champs `%s` manquant dans la table `%s`',
    #####################################
    # dbTable->checkAndConvertCharsetAndCollation()
    #####################################
    'CONVERT_CHARSET_AND_COLLATION' => 'Table `%s` convertie avec pour interclassement `%s` et collation `%s`',
    #####################################
    # dbTable->createTable()
    #####################################
    'CREATE_TABLE'          => 'Création de la table `%s`',
    #####################################
    # dbTable->renameTable()
    #####################################
    'RENAME_TABLE'          => 'Table `%s` renommé en `%s`',
    #####################################
    # dbTable->dropTable()
    #####################################
    'DROP_TABLE'            => 'Supression de la table `%s` si existante',
    #####################################
    # dbTable->addField()
    #####################################
    'FIELD_TYPE_NO_FOUND'   => 'Type du champ `%s` non définit',
    'ADD_FIELD'             => 'Champ `%s` ajouté à la table `%s`',
    #####################################
    # dbTable->modifyField()
    #####################################
    'MODIFY_FIELD'          => 'Champ `%s` modifié à la table `%s`',
    #####################################
    # dbTable->dropField()
    #####################################
    'DROP_FIELD'            => 'Champ `%s` supprimé de la table `%s`',
    #####################################
    # dbTable->addFieldIndex()
    #####################################
    'ADD_FIELD_INDEX'       => 'Index ajouté au champ `%s` de la table `%s`',
    #####################################
    # dbTable->dropForeignKey()
    #####################################
    'FOREIGN_KEY_DONT_EXIST' => 'Clé étrangère `%s` manquante dans la table `%s`',
    'DROP_FOREIGN_KEY'      => 'Clé étrangère `%s` supprimé de la table `%s`',
    #####################################
    # dbTable->setUpdateFieldData()
    #####################################
    'AND'                   => 'et',
    #####################################
    # dbTable->applyUpdateFieldListToData()
    #####################################
    'CALLBACK_UPDATE_FUNCTION_DONT_EXIST' => 'La fonction de rappel `%s` n\'existe pas',
    #####################################
    # process->main()
    #####################################
    'CORRUPTED_CONF_INC'    => 'Fichier conf.inc.php corrompu, veuillez éditer le fichier conf.inc.php.',
    'DB_PREFIX_ERROR'       => 'Le prefix est erroné, veuillez éditer le fichier conf.inc.php.',// TODO : ou la table est manquante...
    'LAST_VERSION_SET'      => 'Vous avez déjà la dernière version %s de Nuked-Klan',
    'BAD_VERSION'           => 'Votre version de Nuked-Klan ne peut pas être mise à jour directement.<br/>Veuillez d\'abord mettre à jour vers la version %s',
    #####################################
    # process->runTableProcessAction()
    #####################################
    'MISSING_FILE'          => 'Fichier introuvable : ',
    #####################################
    # process->_formatSqlError()
    #####################################
    'DB_CONNECT_FAIL'       => 'Connexion à la base de données impossible !',
    'FATAL_SQL_ERROR'       => 'Une erreur SQL est survenue<br />Erreur : %s',
    #####################################
    # process->_writeDefaultContent()
    #####################################
    'FIRST_NEWS_TITLE'      => 'Bienvenue sur votre site NuKed-KlaN %s',
    'FIRST_NEWS_CONTENT'    => 'Bienvenue sur votre site NuKed-KlaN, votre installation s\'est, à priori, bien déroulée, rendez-vous dans la partie administration pour commencer à utiliser votre site tout simplement en vous loguant avec le pseudo indiqué lors de l\'install. En cas de problèmes, veuillez le signaler sur <a href="http://www.nuked-klan.org">http://www.nuked-klan.org</a> dans le forum prévu à cet effet.',
    #####################################
    # processConfiguration->_check()
    #####################################
    'MISSING_CONFIG_KEY'    => 'Clé `%s` manquante dans le fichier INSTALL/config.php',
    'CONFIG_KEY_MUST_BE_STRING' => 'La clé `%s` doit être une chaîne de caractère',
    'CONFIG_KEY_MUST_BE_ARRAY' => 'La clé `%s` doit être un tableau',
    #####################################
    # view::__construct()
    #####################################
    'VIEW_NO_FOUND'         => 'Le fichier de la vue `%s` est manquant',
    #####################################
    # media/js/setDbConfiguration.js
    #####################################
    'DB_HOST_ERROR'         => 'Veuillez saisir le nom du serveur %s.',
    'DB_USER_ERROR'         => 'Veuillez saisir le nom d\'utilisateur.',
    'DB_PASSWORD_ERROR'     => 'Veuillez saisir un mot de passe.',
    'DB_PREFIX_ERROR'       => 'Veuillez saisir un prefix pour les tables de la base de données.',
    'DB_NAME_ERROR'         => 'Veuillez saisir un nom pour la base de données.',
    //'DB_PORT_ERROR'         => 'Veuillez saisir un port correct pour la connection au serveur',
    #####################################
    # media/js/runProcess.js
    #####################################
    'CHECK_TABLE_INTEGRITY' => 'Vérification de l\'integrité de la table <b>%s</b>',
    'SUCCESS'               => 'Réussi',
    'FAILURE'               => 'Echec',
    'CONVERTED_TABLE_SUCCESS' => 'Table <b>%s</b> convertie avec <span style="color:green;">succès</span>.',
    'FOREIGN_KEY_ADD_TO_TABLE_SUCCESS' => 'Clés étrangères ajoutée à la table <b>%s</b> avec <span style="color:green;">succès</span>.',
    'CREATED_TABLE_SUCCESS' => 'Table <b>%s</b> crée avec <span style="color:green;">succès</span>.',
    'UPDATE_TABLE_SUCCESS'  => 'Table <b>%s</b> mise à jour avec <span style="color:green;">succès</span>.',
    'REMOVE_TABLE_SUCCESS'  => 'Table <b>%s</b> supprimée avec <span style="color:green;">succès</span>.',
    'NO_TABLE_TO_DROP'      => 'Aucune table <b>%s</b> a supprimée.',
    'NOTHING_TO_CHECK'      => 'Rien à vérifier pour la table <b>%s</b>',
    'NO_CONVERT_TABLE'      => 'Aucune conversion pour la table <b>%s</b>',
    'NOTHING_TO_DO'         => 'Aucune modification à effectuée pour la table <b>%s</b>',
    'STEP'                  => 'Etape',
    'DROP_ALL_TABLE'        => 'Supression des tables',
    'DROP_ALL_TABLE_FAILED' => 'Il y a %d tables non supprimées',
    'CREATE_ALL_TABLE'      => 'Création des tables',
    'CREATE_ALL_TABLE_FAILED' => 'Il y a %d tables non crées',
    'ADD_FOREIGN_KEY_ALL_TABLE' => 'Ajout des clés étrangères des tables',
    'ADD_FOREIGN_KEY_ALL_TABLE_FAILED' => 'Il y a %d tables sans leurs clés étrangères',
    'CHECK_ALL_TABLE_INTEGRITY' => 'Vérification de l\'integrité des tables',
    'CHECK_TABLE_CHARSET'   => 'Vérification de l\'encodage des tables',
    'CHECK_INTEGRITY_FAILED' => 'Il y a %d tables corrompues',
    'TABLE_CONVERTION'      => 'Conversion des tables',
    'CONVERTED_TABLE_FAILED' => 'Il y a %d tables non converties',
    'INSTALL_PROCESS_SUCCESS' => 'L\'installation est terminée ! Toutes les tables ont bien été crées.',
    'UPDATE_PROCESS_SUCCESS' => 'La mise à jour est terminée ! Toutes les tables ont bien été modifiées.',
    'INSTALL_FAILED'        => 'L\'installation est terminée ! Mais des erreurs sont survenues, %d tables n\'ont pas été crées.',
    'UPDATE_FAILED'         => 'La mise à jour est terminée ! Mais des erreurs sont survenues, %d tables n\'ont pas été modifiées.',
    'PRINT_ERROR'           => ' - Erreur :',
    'UPDATE_TABLE_STEP'     => 'Mise à jour de la table <b>%1$s</b> : Etape <b>%2$s</b>',
    'UPDATE_ALL_TABLE'      => 'Mise à jour des tables',
    'UPDATE_ALL_TABLE_FAILED' => 'Il y a %d tables non mise(s) à jour',
    'CHECK_TABLE_INTEGRITY_ERROR' => 'Une erreur est survenue lors de la vérification de la table',
    'CREATED_TABLE_ERROR'   => 'Une erreur est survenue lors de la création de la table',
    'UPDATE_TABLE_ERROR'    => 'Une erreur est survenue lors de la modification de la table',
    'STARTING_INSTALL'      => 'Démarrage de l\'installation.',
    'STARTING_UPDATE'       => 'Démarrage de la mise à jour.',
    #####################################
    # media/js/setSuperAdministrator.js
    #####################################
    'ERROR_NICKNAME'        => 'Le pseudo doit faire minimum 3 caractères et ne peut contenir les caractères suivants : $^()\'?%#\<>,;:',
    'ERROR_PASSWORD'        => 'Veuillez saisir un mot de passe.',
    'ERROR_PASSWORD_CONFIRM' => 'Les mots de passe ne correspondent pas.',
    'ERROR_EMAIL'           => 'Veuillez saisir un e-mail valide',
    #####################################
    # tables/table.action.c.fk.i.u.php
    #####################################
    'UPDATE_AUTHOR_DATA'    => 'Mise à jour des données de l\'utilisateur dans les champs `%s` de la table `%s`',
    #####################################
    # tables/table.block.c.i.u.php
    #####################################
    'INSERT_DEFAULT_DATA'   => 'Insertion des données par défaut de la table `%s`',
    'APPLY_BBCODE'          => 'Application du BBcode sur le champ `%s` de la table `%s`',
    'BLOCK_LOGIN'           => 'Login',
    'NAV'                   => 'Menu',
    'NAV_CONTENT'           => 'Contenu',
    'NAV_NEWS'              => 'News',
    'NAV_ARCHIV'            => 'Archives',
    'NAV_ART'               => 'Articles',
    'NAV_CALENDAR'          => 'Calendrier',
    'NAV_STATS'              => 'Statistiques',
    'NAV_COMMUNITY'         => 'Communauté',
    'NAV_FORUM'             => 'Forum',
    'NAV_GUESTBOOK'         => 'Livre d\'Or',
    'NAV_IRC'               => 'IrC',
    'NAV_MEMBERS'           => 'Membres',
    'NAV_CONTACT_US'        => 'Nous contacter',
    'NAV_MEDIAS'            => 'Médias',
    'NAV_DOWNLOAD'          => 'Téléchargements',
    'NAV_GALLERY'           => 'Galerie',
    'NAV_LINKS'             => 'Liens Web',
    'NAV_GAMES'             => 'Jeux',
    'NAV_TEAM'              => 'Team',
    'NAV_DEFY'              => 'Nous Défier',
    'NAV_RECRUIT'           => 'Recrutement',
    'NAV_SERVER'            => 'Serveurs',
    'NAV_MATCHS'            => 'Matchs',
    'MEMBER'                => 'Membre',
    'NAV_ACCOUNT'           => 'Compte',
    'NAV_ADMIN'             => 'Administration',
    'BLOCK_SEARCH'          => 'Recherche',
    'POLL'                  => 'Sondage',
    'BLOCK_STATS'           => 'Stats',
    'IRC_AWARD'             => 'Irc Awards',
    'SERVER_MONITOR'        => 'Serveur monitor',
    'SUGGEST'               => 'Suggestion',
    'BLOCK_SHOUTBOX'        => 'Tribune libre',
    'BLOCK_PARTNERS'        => 'Partenaires',
    'GAME_SERVER_RENTING'   => 'Location de serveurs de jeux',
    'INSERT_BLOCK'          => 'Ajout du block %s',
    #####################################
    # tables/table.config.c.i.u.php
    #####################################
    'DELETE_CONFIG'         => 'Suppression de la configuration pour `%s` de la table `%s`',
    'ADD_CONFIG'            => 'Ajout de la configuration pour `%s` de la table `%s`',
    'UPDATE_CONFIG'         => 'Mise à jour de la configuration pour `%s` de la table `%s`',
    #####################################
    # tables/table.forums.c.i.u.php
    #####################################
    'FORUM'                 => 'Forum',
    'TEST_FORUM'            => 'Forum Test',
    'UPDATE_NB_THREAD'      => 'Mise à jour du total de sujets dans le champ `%s` de la table `%s`',
    'UPDATE_NB_MESSAGE'     => 'Mise à jour du total de messages dans le champ `%s` de la table `%s`',
    'REMOVE_EDITOR'         => 'Suppression de l\'éditeur',
    #####################################
    # tables/table.forums_cat.c.i.php
    #####################################
    'CATEGORY'              => 'Catégorie',
    #####################################
    # tables/table.forums_rank.c.i.php
    #####################################
    'NEWBIE'                => 'Noob',
    'JUNIOR_MEMBER'         => 'Jeune membre',
    'SENIOR_MEMBER'         => 'Membre averti',
    'POSTING_FREAK'         => 'Posteur Fou',
    'MODERATOR'             => 'Modérateur',
    'ADMINISTRATOR'         => 'Administrateur',
    'UPDATE_RANK_IMG'       => 'Mise à jour de l\'image du rang forum dans le champ `%s` de la table `%s`',
    #####################################
    # table.forums_threads.c.fk.i.u.php
    #####################################
    'UPDATE_NB_REPLIES'     => 'Mise à jour du total de réponses dans le champ `%s` de la table `%s`',
    #####################################
    # tables/table.games.c.i.u.php
    #####################################
    'PREF_CS'               => 'Préférences CS',
    'OTHER_NICK'            => 'Autre pseudo',
    'FAV_MAP'               => 'Map favorite',
    'FAV_WEAPON'            => 'Arme favorite',
    'SKIN_T'                => 'Skin Terro',
    'SKIN_CT'               => 'Skin CT',
    #####################################
    # tables/table.match.c.i.u.php
    #####################################
    'UPDATE_FIELD'          => 'Champ `%s` mise à jour dans la table `%s`',
    #####################################
    # tables/table.modules.c.i.u.php
    #####################################
    'DELETE_MODULE'         => 'Sppression du module %s',
    'ADD_MODULE'            => 'Ajout du module %s',
    #####################################
    # tables/table.news_cat.c.i.php
    #####################################
    'BEST_MOD'              => 'Le meilleur MOD pour Half-Life',
    #####################################
    # tables/table.notification.i.u.php
    #####################################
    'SUHOSIN'               => 'Attention la configuration PHP de suhosin.session.encrypt est sur "On". Veuillez vous référer à la documentation, en cas de problème.',
    #####################################
    # tables/table.smilies.c.i.u.php
    #####################################
    // TODO UPDATE_SMILIES
    #####################################
    # tables/table.sondage.c.i.php
    #####################################
    'LIKE_NK'               => 'Aimez-vous Nuked-klan ?',
    #####################################
    # tables/table.sondage_data.c.i.php
    #####################################
    'ROXX'                  => 'Ca déchire, continuez !',
    'NOT_BAD'               => 'Mouais, pas mal...',
    'SHIET'                 => 'C\'est naze, arrêtez-vous !',
    'WHATS_NK'              => 'C\'est quoi Nuked-Klan ?',
    #####################################
    # tables/table.users.c.i.u.php
    #####################################
    'UPDATE_PASSWORD'       => 'Mot de passe du champ `%s` mis à jour dans la table `%s`',
    'UPDATE_COUNTRY'        => 'Fichier du drapeau du pays du champ `%s` mis à jour dans la table `%s`',
    'UPDATE_HOMEPAGE'       => 'Url de la page perso du champ `%s` mis à jour dans la table `%s`',
    #####################################
    # views/changelog.php
    #####################################
    'NEW_FEATURES_NK'       => 'Nouveautés Nuked Klan %s',
    'SECURITY'              => 'Sécurité',
    'SECURITY_DETAIL'       => 'La sécurité a été entèrement revue.<br />Nous pouvons aussi vous envoyer des messages depuis le site officiel, afin de vous avertir, informer ou autre...',
    'OPTIMISATION'          => 'Optimisation',
    'OPTIMISATION_DETAIL'   => 'Certaines parties de Nuked-Klan ont été optimisées comme le système de pagination afin de rendre votre site légérement moins lourd.',
    'ADMINISTRATION'        => 'Administration',
    'ADMINISTRATION_DETAIL' => 'Afin de réaliser une administration au goût du jour, nous avons préféré repartir de zéro, et concevoir un système dans lequel administrateurs, utilisateurs, machines, et site officiel seraient reliés. Pour cela, nous avons mis en place des systèmes de communication comme les notifications, les actions, les discussions admin. Cette administration possède un panneau capable de vous transporter n\'importe où dans votre administration mais aussi de vous avertir.',
    'BAN_TEMP'              => 'Ban temporaire',
    'BAN_TEMP_DETAIL'       => 'Un système de bannissement temporaire a été mis en place, vous avez donc le choix de bannir l\'utilisateur 1 jour, 7 jours, 1 mois, 1 an, ou définitivement.',
    'SHOUTBOX'              => 'Shoutbox ajax',
    'SHOUTBOX_DETAIL'       => 'Un nouveau bloc textbox en ajax a été développé. Celui-ci est capable d\'afficher qui est en ligne, et d\'envoyer/afficher des nouveaux messages sans recharger la page.',
    'SQL_ERROR'             => 'Gestions des erreurs SQL',
    'SQL_ERROR_DETAIL'      => 'Ce système est à double sens, lorsqu\'un visiteur tombe sur une erreur SQL, plutôt que de voir l\'erreur, il est redirigé vers une page d\'excuse, et un rapport de l\'erreur SQL est envoyé dans l\'administration.',
    'MULTI_WARS'            => 'Multi-map module wars',
    'MULTI_WARS_DETAIL'     => 'Le nouveau module permet de visionner les prochains matchs, il permet aussi de choisir le nombre de maps, il y a alors un score par map, puis un score final qui est la moyenne des scores par map.',
    'COMMENT_SYSTEM'        => 'Système commentaires',
    'COMMENT_SYSTEM_DETAIL' => 'Le nouveau système de commentaires permet rapidement d\'envoyer un commentaire en ajax et de visionner les 4 derniers commentaires.',
    'WYSIWYG_EDITOR'        => 'Editeur WYSIWYG',
    'WYSIWYG_EDITOR_DETAIL' => 'Ce nouveau système permet d\'avoir une visualisation rapide de votre message, news ou autre après mise en forme.',
    'CONTACT'               => 'Module Contact',
    'CONTACT_DETAIL'        => 'Nous avons ajouté le module contact indispensable au fonctionnement d\'un site web.',
    'PASSWORD_ERROR'        => 'Erreur mot de passe',
    'PASSWORD_ERROR_DETAIL' => 'Lorsqu\'un utilisateur se trompe de mot de passe 3 fois de suite, il doit alors recopier un code de sécurité en plus de son login afin de se connecter à son compte.',
    'VARIOUS_MODIF'         => 'Différentes modifications',
    'VARIOUS_MODIF_DETAIL'  => 'En plus des modifications précédentes, nous avons effectué diverses modifications comme la page 404, où même des modifications non visibles comme le captcha.',
    'NEXT'                  => 'Continuer',
    #####################################
    # views/checkCompatibility.php
    #####################################
    'CHECK_COMPATIBILITY_HOSTING' => 'Vérification de la compatibilité avec votre hébergement',
    'COMPONENT'             => 'Composant',
    'COMPATIBILITY'         => 'Compatibilité',
    'WEBSITE_DIRECTORY'     => 'Répertoire du site web',
    'PHP_VERSION'           => 'PHP version &ge; %s',
    'PHP_VERSION_ERROR'     => 'Erreur PHP',
    'MYSQL_EXT'             => 'Extension MySQL',
    'MYSQL_EXT_ERROR'       => 'Erreur Mysql',
    'SESSION_EXT'           => 'Extension des sessions',
    'SESSION_EXT_ERROR'     => 'Erreur sessions',
    'FILEINFO_EXT'          => 'Extension File Info',
    'FILEINFO_EXT_ERROR'    => 'Erreur fileinfo',
    'GD_EXT'                => 'Extension GD',
    'GD_EXT_ERROR'          => 'Erreur GD',
    'CHMOD_TEST'            => 'Test du CHMOD',
    'CHMOD_TEST_ERROR'      => 'Erreur chmod %s',
    'NO_READABLE_DIRECTORY' => 'Le dossier %s n\'a pas les droits d\'écriture',
    'BAD_HOSTING'           => 'Votre hébergement n\'est pas compatible avec la nouvelle version de Nuked-Klan.',
    'FORCE'                 => 'Forcer l\'installation',
    #####################################
    # views/chooseSendStats.php
    #####################################
    'SELECT_STATS'          => 'Activation des statistiques anonymes',
    'STATS_INFO'            => '<p>Afin d\'améliorer au mieux le CMS Nuked Klan, en tenant compte de l\'utilisation des administrateurs de sites NK,<br/>nous avons mis en place sur cette nouvelle version un système d\'envoi de statistiques anonymes.</p><p>Vous avez le choix d\'activer ou non ce système, mais sachez qu\'en l\'activant vous permettrez à l\'équipe de Developpement/Marketing<br/>de mieux répondre à vos attentes.</p><p>Pour une totale transparence, lors de l\'envoi des statistiques, vous serez informé dans l\'administration, des données envoyées.<br/>Sachez qu\'à tout moment vous aurez la possibilité de désactiver l\'envoi des statistiques dans les préférences générales de votre administration.</p>',
    'CONFIRM_STATS'         => 'Oui, j\'autorise l\'envoi de statistiques anonymes à Nuked-Klan',
    'CONFIRM'               => 'Valider',
    #####################################
    # views/cleaningFiles.php
    #####################################
    'DEPRECATED_FILES'      => 'Fichiers obsolètes detectés',
    'CLEANING_FILES'        => 'Un ou plusieurs fichiers obsolètes n\'ont pas pu être effacer.<br />Veuillez supprimer manuellement les fichiers suivants :',
    'RETRY'                 => 'Réessayer',
    #####################################
    # views/confIncFailure.php
    #####################################
    'WEBSITE_DIRECTORY_CHMOD' => 'Impossible d\'écrire dans le dossier contenant Nuked-Klan<br/>Veuillez mettre manuellement le CHMOD <strong>0755</strong> sur ce dossier.',
    'CONF_INC_CHMOD_ERROR'  => 'Impossible de modifier les droits CHMOD du fichier conf.inc.php<br/>Veuillez mettre manuellement le CHMOD <strong>%s</strong> sur ce fichier.',
    'WRITE_CONF_INC_ERROR'  => 'Une erreur est survenue dans la génération du fichier conf.inc.php',
    'COPY_CONF_INC_ERROR'   => 'Impossible de créer la sauvegarde du fichier conf.inc.php<br/>Veuillez télécharger le fichier et le sauvegarder manuellement.',
    'DOWNLOAD'              => 'Télécharger',
    #####################################
    # views/fatalError.php
    #####################################
    'ERROR'                 => 'Une erreur est survenue !!!',
    'BACK'                  => 'Retour',
    'REFRESH'               => 'Rafraichir',
    #####################################
    # views/formError.php
    #####################################
    'ERROR_FIELDS'          => 'Vous avez mal rempli les champs du formulaire.',
    #####################################
    # views/fullPage.php
    #####################################
    'INSTALL_TITLE'         => 'Installation de Nuked-klan %s',
    'UPDATE_TITLE'          => 'Mise à jour de Nuked-klan %s',
    'SELECT_LANGUAGE'       => 'Sélection de la langue',
    'SELECT_TYPE'           => 'Type d\'installation',
    'INSTALL'               => 'Installation',
    'UPDATE'                => 'Mise à jour',
    'YES'                   => 'Oui',
    'NO'                    => 'Non',
    'QUICK'                 => 'Rapide',
    'ASSIST'                => 'Assistée',
    'SELECT_SAVE'           => 'Sauvegarde de la base de données',
    'IN_PROGRESS'           => 'En cours',
    'FINISH'                => 'Terminé',
    'RESET_SESSION'         => 'Réinitialiser',
    'DISCOVERY'             => 'Découvrer Nuked-Klan !',
    'DISCOVERY_DESCR'       => 'Vous êtes sur le point d\'installer votre site web sur base du CMS Nuked-Klan...</p><p>En quelques clics et en quelques minutes, offrez-vous la possibilité de gérer votre team, guilde ou clan, à l\'aide d\'outils spécialement conçus à cet effet !</p><p>Vous n\'êtes pas un joueur mais vous désirez toutefois utiliser Nuked-Klan pour réaliser votre site web ?</p><p>Aucun problème, une version généraliste (SP) a également été développée et vous est proposée, expressément dans cette optique.</p><p>Adopter un design plus adapté à l\'esprit de votre activité (palette de couleurs, logos,...) devient, grâce à Nuked-Klan, un véritable jeu d\'enfant. Avec une collection impressionante de graphismes et une modification (ainsi qu\'une création) de thèmes certainement une des plus aisée du marché des CMS, vous aboutirez inévitablement à un site web qui vous ressemble.</p><p>Nous vous remercions pour l\'intérêt et la confiance que vous nous apportez au quotidien... et depuis toutes ces années !',
    'NEW_VERSION_CONCEPT'   => 'La 1.8 : une version évolutive',
    'NEW_VERSION_CONCEPT_DESCR' => 'La nouvelle version 1.8 est enfin disponible !<p>Après un cahier des charges trop ambitieux, nous avons revu à la baisse les ambitions de cette nouvelle version afin de vous proposer une version stable qui saura vous satisfaire en attendant la version 2.0 de Nuked Klan.</p><p>Après le redesign de l\'administration sur la version 1.7.9, la version 1.8 restylera et transformera totalement les modules.</p><p>Cette nouvelle version 1.8 se veut modulable : des mises à jour régulières (1.8.1, 1.8.2 etc..) qui apporteront chacune leurs lots de modifications.</p>Voici les grandes lignes de cette nouvelle version : <ul><li>Compatibilité > PHP 5.4</li><li>Nouveau Captcha invisible</li><li>Intégration jquery et CSS pour chaque module</li><li>Nouveaux modules et redesign des anciens</li><li>Nouveaux éditeurs (Ckeditor + TinyMce)</li><li>Une version unique (Gamer et SP)</li><li>Correction de nombreux bugs</li><li>Nouveau thème innovant</li><li>Etc...</li></ul>Et tout un lot d\'améliorations sur l\'ensemble du CMS, n\'attendez plus et finalisez votre installation!',
    'GITHUB_NK'             => 'Participez au développement sur Github !',
    'GITHUB_NK_DESCR'       => 'Depuis la sortie de la version 1.7.9, le projet Nuked Klan est présent sur Github.<p>Le dépôt Github vous permet de suivre l\'avancement des différentes versions et même d\'y participer.</p><p>Si vous rencontrez un bug ou avez une idée d\'amélioration, vous pouvez utiliser le bug tracker pour nous transmettre vos remarques.<br />Notre équipe après vérification et/ou approbation prendra en charge votre demande et pourra la traiter dans les plus brefs délais en assurant un suivie de qualité.</p><p>Vous pouvez aussi consulter le Wiki du dépôt Github, vous y trouverez des réponses, des conseils en tous genres autour du CMS Nuked Klan.</p><p>Enfin si vous vous sentez l\'âme d\'un développeur vous avez même la possibilité de nous soumettre vos améliorations et directement contribuer au projet Nuked Klan.</p>',
    'COMMUNAUTY_NK'         => 'La communauté NK',
    'COMMUNAUTY_NK_DESCR'   => 'Une communauté sans cesse florissante, avec des membres d\'une grande serviabilité et possédant de nombreuses compétences.<br/>Voilà un des avantages non négligeable dont vous bénéficierez en adoptant Nuked-Klan et en rejoignant la dite communauté.<br/>Tout naturellement, vous intègrerez cette grande famille, toujours soucieuse du bien-être de ses membres.</p><p>De nombreux fan-sites gravitent autour du CMS. Preuve de l\'enthousiasme et de l\'engouement que procure l\'utilisation de Nuked-Klan, ils représentent la colonne vertébrale du CMS.</p><p>Pour cette raison (et pour bien d\'autres), ils apportent à notre équipe de développeurs et de communautaires l\'envie d\'avancer, main dans la main, dans la bonne humeur et avec un esprit assidu de communication.</p><p>C\'est ainsi que nous évoluerons, au fil des années, toujours à l\'écoute de vos attentes et de vos besoins.</p><p>Parce que Nuked-Klan est, avant tout, votre CMS !!',
    'NEW_MODULES'           => 'Mises à jour et ajout de nouveaux modules !',
    'NEW_MODULES_DESCR'     => 'Tout au long des mises à jour de cette nouvelle version les modules seront tous améliorés, de manière à combler les manques des précédentes versions.<p>Chaque module disposera désormais de leur propre feuille de style qui peut être modifiée par le design de votre thème.<br />Ainsi les possibilités de personnalisation de votre site seront décuplées à l\'infini.</p><p>Un nouveau design pour toutes les notifications côté client, rendra l\'utilisation de votre site plus agréable.</p><p>Pour la première release (1.8.0) qui fixe les bases de la branche 1.8, seuls les modules Forum et la Tribune libre ont été entièrement revud en ajoutant tout un lot de modifications présentes sur les patchs les plus utilisés par la communauté.<br />Les modules Teams, Wars, News & Articles ont bénéficié de légères modifications, afin de s\'adapter aux fonctionnalités du nouveau thème et de préparer leur refonte sur les versions suivantes...</p>Le module Page est désormais intégré par défaut à Nuked Klan, d\'autres modules suivront avec les mises à jour à venir.',
    'NEW_TEMPLATE'          => 'Un nouveau thème innovant !',
    'NEW_TEMPLATE_DESCR'    => 'Restless utilise au maximum toutes les fonctionnalités de votre nouvelle version 1.8.<p>Ce thème vous comblera par les innombrables options dont il dispose :</p><ul><li>4 thèmes personnalisés</li><li>6 couleurs différentes</li><li>Une automatisation avancée</li><li>Un système de template innovant</li><li>Un codage maîtrisé</li><li>Une administration poussée</li><li>Des blocs inédits</li></ul>En détails :<ul><li>Le menu principal utilise le bloc menu de NK</li><li>Le slider se remplit automatiquement au fur et à mesure de l\'ajout de news (un champ image de couverture vous permet désormais d\'illustrer avec une image vos news)</li><li>Le bloc Top match utilise les nouveaux champs images des modules teams et matchs</li><li>Le bloc article utilise lui aussi les images de couverture de vos articles</li><li>Le bloc matchs affichera vos dernières victoires avec un design épuré</li><li>Le bloc team mettra en valeur vos équipes</li><li>Le bloc galerie exposera vos 6 derniers clichés</li><li>Le bloc téléchargement comptabilisera la popularité de vos derniers fichiers</li><li>Un bloc inédit mettra en valeur les messages de votre livre d\'or</li><li>Un bloc réseaux sociaux pour fidéliser votre communauté</li><li>Un sub menu à deux niveaux dans le footer de votre site optimisera la navigation</li><li>Le slider sponsor du footer ravira vos partenaires</li><li>Etc...</li></ul>Une multitude d\'autres petits détails en feront le thème ultime que vous attendiez !',
    #####################################
    # views/getPartners.php
    #####################################
    'NO_PARTNERS'           => 'Une erreur est survenue lors de la r&eacute;cup&eacute;ration de la liste des partenaires...',
    #####################################
    # views/main.php
    #####################################
    'WELCOME_INSTALL'       => 'Bienvenue sur Nuked-Klan %s',
    'GUIDE_INSTALL'         => 'L\'assistant va vous guider à travers les étapes de l\'installation de votre portail...<br /><br /><b>Merci de laisser le copyleft sur votre site pour respecter la licence GNU.</b>',
    'START_INSTALL'         => 'Démarrer l\'installation',
    'DETECT_UPDATE'         => 'L\'assistant a détecté une installation de la version : %s de Nuked-Klan',
    'START_UPDATE'          => 'Démarrer la mise à jour',
    #####################################
    # views/maliciousScript.php
    #####################################
    'MALICIOUS_SCRIPT_DETECTED' => 'Script malveillant détecté',
    'DELETE_TURKISH_FILE'   => 'Impossible de supprimer le fichier. Veuillez le supprimer manuellement et vérifier encore si il est présent.<br/>&nbsp;Fichier : /modules/404/lang.turkish.lang.php',
    'CHECK_AGAIN'           => 'Vérifier encore',
    #####################################
    # views/processSuccess.php
    #####################################
    'INSTALL_SUCCESS'       => 'Installation terminée',
    'UPDATE_SUCCESS'        => 'Mise à jour  terminée',
    'INFO_PARTNERS'         => 'Retrouvez nos partenaires et leurs codes promotionnels,<br/>afin de profiter au mieux de leurs produits et/ou services.',
    'WAIT'                  => 'Veuillez patienter...',
    'ACCESS_SITE'           => 'Accéder à votre site',
    #####################################
    # views/runProcess.php
    #####################################
    'CREATE_DB'             => 'Création de la base de données',
    'UPDATE_DB'             => 'Mise à jour de la base de données',
    'WAITING'               => 'Veuillez cliquer sur démarrer pour commencer...',
    'START'                 => 'Démarrer',
    #####################################
    # views/selectLanguage.php
    #####################################
    'FRENCH'                => 'Français',
    'ENGLISH'               => 'Anglais',
    'SUBMIT'                => 'Valider',
    #####################################
    # views/selectProcessType.php
    #####################################
    'CHECK_TYPE_INSTALL'    => 'Choix du type d\'installation',
    'INSTALL_SPEED'         => 'Installation rapide',
    'INSTALL_ASSIST'        => 'Installation assistée',
    'UPDATE_SPEED'          => 'Mise à jour rapide',
    'UPDATE_ASSIST'         => 'Mise à jour assistée',
    #####################################
    # views/selectSaveDb.php
    #####################################
    'TO_SAVE'               => 'Sauvegarder',
    'SAVE_YOUR_DATABASE'    => 'Vous pouvez sauvegarder votre base de donnée en cliquant sur le lien ci-dessous.',
    #####################################
    # views/setDbConfiguration.php
    #####################################
    'CONFIG'                => 'Configuration',
    'DB_HOST'               => 'Serveur %s',
    'INSTALL_DB_HOST'       => 'Il s\'agit ici de l\'adresse du serveur %s de votre hébergement, celui-ci contient toutes vos données textes, membres, messages... En général, il s\'agit de localhost, mais dans tous les cas, l\'adresse est indiquée dans votre mail d\'inscription de votre hébergeur ou dans l\'administration de votre hébergement.',
    'DB_USER'                => 'Utilisateur',
    'INSTALL_DB_USER'       => 'Il s\'agit de votre identifiant qui vous permet de vous connecter à votre base %s.',
    'DB_PASSWORD'           => 'Mot de passe',
    'INSTALL_DB_PASSWORD'   => 'Il s\'agit du mot de passe de votre identifiant qui vous permet de vous connecter à votre base %s.',
    'DB_PREFIX'             => 'Prefix',
    'INSTALL_DB_PREFIX'     => 'Le prefix permet d\'installer plusieurs fois Nuked-Klan sur une seule base %s en utilisant un prefix différent à chaque fois, par défaut, il s\'agit de \'nuked\', mais vous pouvez le changer comme vous le voulez.',
    'DB_NAME'               => 'Nom de la Base',
    'INSTALL_DB_NAME'       => 'Il s\'agit du nom de votre base de données %s, souvent vous devez vous rendre dans l\'administration de votre hébergement pour créer une base de données, mais parfois celle-ci vous est déjà fournie dans le mail d\'inscription de votre hébergement.',
    'ADVANCED_PARAMETERS'   => 'Paramètre avancés',
    'DB_TYPE'               => 'Type de base de données',
    //'INSTALL_DB_TYPE'       => '',
    'DB_PORT'               => 'Port',
    //'INSTALL_DB_PORT'       => '',
    'DB_PERSISTENT'         => 'Connexion persistante',
    //'INSTALL_DB_PERSISTENT' => '',
    #####################################
    # views/setAdministrator.php
    #####################################
    'CREATE_ADMINISTRATOR'  => 'Création du compte Administrateur',
    'NICKNAME'              => 'Pseudo',
    'PASSWORD'              => 'Mot de passe',
    'PASSWORD_CONFIRM'      => 'Mot de passe (confirmez)',
    'EMAIL'                 => 'E-mail',
);

?>