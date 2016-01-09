<?php
/**
 * french.lang.php
 *
 * French translation for install / update process
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
    'QUOTE'                 => 'Citation',
    'HAS_WROTE'             => 'a écrit',
    #####################################
    # db->load()
    #####################################
    'UNKNOW_DATABASE_TYPE'  => 'Type de base de données `%s` inconnu',
    #####################################
    # dbMySQL->_getDbConnectError()
    #####################################
    'DB_HOST_ERROR'         => 'Veuillez contrôler le nom du serveur mysql.',
    'DB_LOGIN_ERROR'        => 'Veuillez contrôler le nom d\'utilisateur et le mot de passe.',
    'DB_NAME_ERROR'         => 'Veuillez contrôler le nom de la base de données.',
    'DB_CHARSET_ERROR'      => 'Votre base de données ne supporte pas l\'interclassement %s',
    'DB_UNKNOW_ERROR'       => 'Erreur MySQL inconnue',
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
    'DROP_TABLE'            => 'Suppression de la table `%s` si existante',
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
    # dbTable->applyUpdateFieldListToData()
    #####################################
    'CALLBACK_UPDATE_FUNCTION_DONT_EXIST' => 'La fonction de rappel `%s` n\'existe pas',
    #####################################
    # install->main()
    #####################################
    'CORRUPTED_CONF_INC'    => 'Fichier conf.inc.php corrompu, veuillez éditer le fichier conf.inc.php.',
    'DB_PREFIX_ERROR'       => 'Le prefix est erroné, veuillez éditer le fichier conf.inc.php.',// TODO : ou la table est manquante...
    'LAST_VERSION_SET'      => 'Vous avez déjà la dernière version %s de Nuked-Klan',
    'BAD_VERSION'           => 'Votre version de Nuked-Klan ne peut pas être mise à jour directement.<br/>Veuillez d\'abord mettre à jour vers la version %s',
    #####################################
    # install->runTableProcessAction()
    #####################################
    'MISSING_FILE'          => 'Fichier introuvable : ',
    #####################################
    # install->_formatSqlError()
    #####################################
    'DB_CONNECT_FAIL'       => 'Connexion à la base de données impossible !',
    'FATAL_SQL_ERROR'       => 'Une erreur SQL est survenue<br />Erreur : %s',
    #####################################
    # install->_writeDefaultContent()
    #####################################
    'FIRST_NEWS_TITLE'      => 'Bienvenue sur votre site NuKed-KlaN %s',
    'FIRST_NEWS_CONTENT'    => 'Bienvenue sur votre site NuKed-KlaN, votre installation s\'est, à priori, bien déroulée, rendez-vous dans la partie administration pour commencer à utiliser votre site tout simplement en vous loguant avec le pseudo indiqué lors de l\'install. En cas de problèmes, veuillez le signaler sur <a href="http://www.nuked-klan.org">http://www.nuked-klan.org</a> dans le forum prévu à cet effet.',
    #####################################
    # processConfiguration->_check()
    #####################################
    'MISSING_CONFIG_KEY'    => 'Clé `%s` manquante dans le fichier INSTALL/config.php',
    'CONFIG_KEY_MUST_BE_STRING' => 'La clé `%s` doit être une chaîne de caractères',
    'CONFIG_KEY_MUST_BE_ARRAY' => 'La clé `%s` doit être un tableau',
    #####################################
    # view::__construct()
    #####################################
    'VIEW_NO_FOUND'         => 'Le fichier de la vue `%s` est manquant',
    #####################################
    # media/js/runProcess.js
    #####################################
    'CHECK_TABLE_INTEGRITY' => 'Vérification de l\'integrité de la table <b>%s</b>',
    'SUCCESS'               => 'Réussi',
    'FAILURE'               => 'Echec',
    'CONVERTED_TABLE_SUCCESS' => 'Table <b>%s</b> convertie avec succès.',
    'CREATED_TABLE_SUCCESS' => 'Table <b>%s</b> crée avec <span style="color:green;">succès</span>.',
    'UPDATE_TABLE_SUCCESS'  => 'Table <b>%s</b> mise à jour avec <span style="color:green;">succès</span>.',
    'REMOVE_TABLE_SUCCESS'  => 'Table <b>%s</b> supprimée avec <span style="color:green;">succès</span>.',
    'NOTHING_TO_CHECK'      => 'Rien à vérifier pour la table <b>%s</b>',
    'NO_CONVERT_TABLE'      => 'Aucune conversion pour la table <b>%s</b>',
    'NOTHING_TO_DO'         => 'Aucune modification à effectuée pour la table <b>%s</b>',
    'CHECK_ALL_TABLE_INTEGRITY' => 'Vérification de l\'integrité des tables',
    'CHECK_TABLE_CHARSET'   => 'Vérification de l\'encodage des tables',
    'CHECK_INTEGRITY_FAILED' => 'Il y a %d tables corrompues',
    'TABLE_CONVERTION'      => 'Conversion des tables',
    'CONVERTED_TABLE_FAILED' => 'Il y a %d tables non converties',
    'INSTALL_SUCCESS'       => 'L\'installation est terminée ! Toutes les tables ont bien été créées.',
    'UPDATE_SUCCESS'        => 'La mise à jour est terminée ! Toutes les tables ont bien été modifiées.',
    'INSTALL_FAILED'        => 'L\'installation est terminée ! Mais des erreurs sont survenues, %d tables n\'ont pas été créées.',
    'UPDATE_FAILED'         => 'La mise à jour est terminée ! Mais des erreurs sont survenues, %d tables n\'ont pas été modifiées.',
    'PRINT_ERROR'           => ' - Erreur :',
    'UPDATE_TABLE_STEP'     => 'Mise à jour de la table <b>%1$s</b> : Etape <b>%2$s</b>',
    'CHECK_TABLE_INTEGRITY_ERROR' => 'Une erreur est survenue lors de la vérification de la table',
    'CREATED_TABLE_ERROR'   => 'Une erreur est survenue lors de la création de la table',
    'UPDATE_TABLE_ERROR'    => 'Une erreur est survenue lors de la modification de la table',
    'STARTING_INSTALL'      => 'Démarrage de l\'installation.',
    'STARTING_UPDATE'       => 'Démarrage de la mise à jour.',
    #####################################
    # media/js/setUserAdmin.js
    #####################################
    'ERROR_NICKNAME'        => 'Le pseudo doit faire minimum 3 caractères et ne peut contenir les caractères suivants : $^()\'?%#\<>,;:',
    'ERROR_PASSWORD'        => 'Veuillez saisir un mot de passe.',
    'ERROR_PASSWORD_CONFIRM' => 'Les mots de passe ne correspondent pas.',
    'ERROR_EMAIL'           => 'Veuillez saisir un e-mail valide',
    #####################################
    # tables/table.block.c.i.u.php
    #####################################
    'INSERT_DEFAULT_DATA'   => 'Insertion des données par défaut de la table `%s`',
    'APPLY_BBCODE'          => 'Application du BBcode sur le champ `%s` de la table `%s`',
    'BLOCK_LOGIN'           => 'Login',
    'NAV'                   => 'Menu',
    'NAV_HOME'              => 'Accueil',
    'NAV_NEWS'              => 'News',
    'NAV_FORUM'             => 'Forum',
    'NAV_DOWNLOAD'          => 'Téléchargements',
    'NAV_TEAM'              => 'Team',
    'NAV_MEMBERS'           => 'Membres',
    'NAV_DEFY'              => 'Nous Défier',
    'NAV_RECRUIT'           => 'Recrutement',
    'NAV_ART'               => 'Articles',
    'NAV_SERVER'            => 'Serveurs',
    'NAV_LINKS'             => 'Liens Web',
    'NAV_CALENDAR'          => 'Calendrier',
    'NAV_GALLERY'           => 'Galerie',
    'NAV_MATCHS'            => 'Matchs',
    'NAV_ARCHIV'            => 'Archives',
    'NAV_IRC'               => 'IrC',
    'NAV_GUESTBOOK'         => 'Livre d\'Or',
    'NAV_SEARCH'            => 'Recherche',
    'NAV_STRATS'            => 'Stratégies',
    'MEMBER'                => 'Membre',
    'NAV_ACCOUNT'           => 'Compte',
    'ADMIN'                 => 'Admin',
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
    #####################################
    # table.forums_read.c.i.u.php
    #####################################
    // TODO ADD_FORUM_READ_DATA
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
    'DELETE_MODULE'         => 'Suppression du module %s',
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
    'UPDATE_PASSWORD'       => 'Mot de passe du champ `%s` mise à jour dans la table `%s`',
    #####################################
    # views/changelog.php
    #####################################
    'NEW_FEATURES_NK'       => 'Nouveautés Nuked Klan %s',
    'SECURITY'              => 'Sécurité',
    'SECURITY_DETAIL'       => 'La sécurité a été entièrement revue.<br />Nous pouvons aussi vous envoyer des messages depuis le site officiel, afin de vous avertir, informer ou autre...',
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
    'COMPOSANT'             => 'Composant',
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
    'NEWSADMIN'             => 'Une nouvelle administration',
    'NEWSADMIN_DESCR'       => 'Plus ergonomique et plus complète, la nouvelle administration présente dans cette version comblera les plus pointilleux d\'entre vous.</p><p>Des options indispensables comme le listage des erreurs SQL et des actions opérées sur le site, la possibilité de laisser des notifications entre administrateurs,... sont, dorénavant, directement intégrées dans le panneau d\'administration.</p><p>Nous avons également pensé aux graphistes et développeurs de thèmes, en leur offrant la possibilité de définir une gestion précise des différents éléments du design, directement via l\'administration interne du site.</p><p>Avec une sécurisation entièrement revue et corrigée, cette dernière version devrait assurer la pérénnité et la fiabilité de votre site web.</p><p>Toujours attentifs à vos attentes et vos besoins, quelques options très attendues voient le jour dans cette version. Ainsi, la possibilité de régler le fuseau horaire de votre site, ... (citer quelques améliorations).',
    'INSTALL_AND_UPDATE'    => 'Installation et mise à jour',
    'INSTALL_AND_UPDATE_DESCR' => 'Les procédures d\'installation et de mise à jour ont été complètement revisitées et simplifiées.</p><p>Etape par étape, tout est maintenant commenté et dissocié afin de parer au moindre problème que vous pourriez rencontrer.</p><p>Plus de perte de données lors d\'une mise à jour, une sauvegarde de votre base de donnée existante est automatiquement exécutée.</p><p>Durant l\'installation et la mise à jour, toutes les étapes sont maintenant archivées dans un journal. En cas de souci, ce journal permettra à notre équipe de vous assister durant les procédures d\'installation (ou de mise à jour) de façon optimale.</p><p>Nous vous proposons, dorénavant, de participer à l\'évolution du CMS via l\'envoi (anonyme) de statistiques. Grâce à cela, nous aurons la possibilité de répondre de façon précise et idéale à vos attentes, dès les prochaines versions.',
    'COMMUNAUTY_NK'         => 'La communauté NK',
    'COMMUNAUTY_NK_DESCR'   => 'Une communauté sans cesse florissante, avec des membres d\'une grande serviabilité et possédant de nombreuses compétences.<br/>Voilà un des avantages non négligeable dont vous bénéficierez en adoptant Nuked-Klan et en rejoignant la dite communauté.<br/>Tout naturellement, vous intègrerez cette grande famille, toujours soucieuse du bien-être de ses membres.</p><p>De nombreux fan-sites gravitent autour du CMS. Preuve de l\'enthousiasme et de l\'engouement que procure l\'utilisation de Nuked-Klan, ils représentent la colonne vertébrale du CMS.</p><p>Pour cette raison (et pour bien d\'autres), ils apportent à notre équipe de développeurs et de communautaires l\'envie d\'avancer, main dans la main, dans la bonne humeur et avec un esprit assidu de communication.</p><p>C\'est ainsi que nous évoluerons, au fil des années, toujours à l\'écoute de vos attentes et de vos besoins.</p><p>Parce que Nuked-Klan est, avant tout, votre CMS !!',
    #####################################
    # views/getPartners.php
    #####################################
    'NO_PARTNERS'           => 'Une erreur est survenue lors de la r&eacute;cup&eacute;ration de la liste des partenaires...',
    #####################################
    # views/installSuccess.php
    #####################################
    'INSTALL_SUCCESS'       => 'Installation terminée',
    'INFO_PARTNERS'         => 'Retrouvez nos partenaires et leurs codes promotionnels,<br/>afin de profiter au mieux de leurs produits et/ou services.',
    'WAIT'                  => 'Veuillez patienter...',
    'ACCESS_SITE'           => 'Accéder à votre site',
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
    # views/selectSaveBdd.php
    #####################################
    'TO_SAVE'               => 'Sauvegarder',
    'SAVE_YOUR_DATABASE'    => 'Vous pouvez sauvegarder votre base de données en cliquant sur le lien ci-dessous.',
    #####################################
    # views/setConfig.php
    #####################################
    'CONFIG'                => 'Configuration',
    'DB_HOST'               => 'Serveur Mysql',
    'INSTALL_DB_HOST'       => 'Il s\'agit ici de l\'adresse du serveur MySQL de votre hébergement, celui-ci contient toutes vos données textes, membres, messages... En général, il s\'agit de localhost, mais dans tous les cas, l\'adresse est indiquée dans votre mail d\'inscription de votre hébergeur ou dans l\'administration de votre hébergement.',
    'DB_USER'                => 'Utilisateur',
    'INSTALL_DB_USER'       => 'Il s\'agit de votre identifiant qui vous permet de vous connecter à votre base MySQL.',
    'DB_PASSWORD'           => 'Mot de passe',
    'INSTALL_DB_PASSWORD'   => 'Il s\'agit du mot de passe de votre identifiant qui vous permet de vous connecter à votre base MySQL.',
    'DB_PREFIX'             => 'Prefix',
    'INSTALL_DB_PREFIX'     => 'Le prefix permet d\'installer plusieurs fois Nuked-Klan sur une seule base MySQL en utilisant un prefix différent à chaque fois, par défaut, il s\'agit de \'nuked\', mais vous pouvez le changer comme vous le voulez.',
    'DB_NAME'               => 'Nom de la Base',
    'INSTALL_DB_NAME'       => 'Il s\'agit du nom de votre base de données MySQL, souvent vous devez vous rendre dans l\'administration de votre hébergement pour créer une base de données, mais parfois celle-ci vous est déjà fournie dans le mail d\'inscription de votre hébergement.',
    #####################################
    # views/setUserAdmin.php
    #####################################
    'CREATE_USER_ADMIN'     => 'Création du compte Administrateur',
    'NICKNAME'              => 'Pseudo',
    'PASSWORD'              => 'Mot de passe',
    'PASSWORD_CONFIRM'      => 'Mot de passe (confirmez)',
    'EMAIL'                 => 'E-mail',
    #####################################
    # views/setAdminError.php
    #####################################
    'ERROR_FIELDS'          => 'Vous avez mal rempli les champs du formulaire.',
);

?>