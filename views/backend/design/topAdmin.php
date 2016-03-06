<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
    <head>
        <meta name="keywords" content="<?php echo $GLOBALS['nuked']['keyword'] ?>" />
        <meta name="Description" content="<?php echo $GLOBALS['nuked']['description'] ?>" />
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title><?php echo $GLOBALS['nkTemplate']['title'] ?></title>
        <link rel="shortcut icon"  href="images/favicon.ico" />
    </head>
    <body>
        <div id="screen" onclick="screenoff()">
            <div id="iframe"></div>
            <div id="iframe_close">&nbsp;</div>
        </div>

        <div id="Frame">
        <!-- Wrapper for the radial gradient background -->
        <div id="body-wrapper">

            <div id="sidebar">

                <!-- Sidebar with logo and menu -->
                <div id="sidebar-wrapper">

                    <!-- Logo NK -->
                    <a href="http://www.nuked-klan.org" target="_blank"><img id="logo" src="modules/Admin/images/logo.png" alt="Simpla Admin logo" /></a>

                    <!-- Sidebar Profile links -->
                    <div id="profile-links">
                        <a href="index.php?file=User" title="<?php echo _EDIT ?>"><img src="<?php echo getUserAvatar() ?>" class="nkAdminUserAvatar" alt="" /></a>
                        <?php echo _BONJOUR ?>
                        <a href="index.php?file=User" title="<?php echo _EDIT ?>"><?php echo $GLOBALS['user']['name'] ?></a><br />
                        <?php echo _VOIR ?>
                        <a href="#messages" rel="modal"><?php echo _MESSAGES ?></a><br /><br />
<?php
    if ($GLOBALS['nuked']['screen'] == 'on') :
?>
                        <a onclick="javascript:screenon('index.php', 'non');return false" href="#"><?php echo _VOIRSITE ?></a> |
<?php
    endif
?>
                        <a href="index.php?file=Admin&amp;page=deconnexion"><?php echo _DECONNEXION ?></a><br />
                        <a href="index.php"><?php echo _RETOURNER ?></a>
                    </div>

                    <!-- Accordion Menu -->
                    <ul id="main-nav">

                        <li><a href="index.php?file=Admin" class="nav-top-item no-submenu<?php printAdminMenuCurrentClass('pannel') ?>"><?php echo _PANNEAU ?></a></li>

                        <li>
                            <!-- SUB MENU : PARAMETERS -->
                            <a href="#" class="nav-top-item<?php printAdminMenuCurrentClass('parameter') ?>"><?php echo _PARAMETRE; ?></a>

                            <ul>
                                <li><a <?php printAdminSubMenuCurrentClass('setting') ?> href="index.php?file=Admin&amp;page=setting"><?php echo _PREFGEN ?></a></li>
                                <li><a <?php printAdminSubMenuCurrentClass('mysql') ?> href="index.php?file=Admin&amp;page=mysql"><?php echo _GMYSQL ?></a></li>
                                <li><a <?php printAdminSubMenuCurrentClass('phpinfo') ?> href="index.php?file=Admin&amp;page=phpinfo"><?php echo _ADMINPHPINFO ?></a></li>
                                <li><a <?php printAdminSubMenuCurrentClass('action') ?> href="index.php?file=Admin&amp;page=action"><?php echo _ACTIONM ?></a></li>
                                <li><a <?php printAdminSubMenuCurrentClass('erreursql') ?> href="index.php?file=Admin&amp;page=erreursql"><?php echo _ERRORBDD ?></a></li>
                            </ul>
                        </li>

                        <li>
                            <!-- SUB MENU : GESTION -->
                            <a href="#" class="nav-top-item<?php printAdminMenuCurrentClass('management') ?>"><?php echo _GESTIONS ?></a>

                            <ul>
                                <li><a <?php printAdminSubMenuCurrentClass('user') ?> href="index.php?file=Admin&amp;page=user"><?php echo _UTILISATEURS ?></a></li>

<?php
    if (file_exists('themes/'. $GLOBALS['nuked']['theme'] .'/admin.php')) :
?>
                                <li><a <?php printAdminSubMenuCurrentClass('theme') ?> href="index.php?file=Admin&amp;page=theme"><?php echo _THEMIS ?></a></li>
<?php
    endif
?>

                                <li><a <?php printAdminSubMenuCurrentClass('modules') ?> href="index.php?file=Admin&amp;page=modules"><?php echo _INFOMODULES ?></a></li>
                                <li><a <?php printAdminSubMenuCurrentClass('block') ?> href="index.php?file=Admin&amp;page=block"><?php echo _BLOCK ?></a></li>
                                <li><a <?php printAdminSubMenuCurrentClass('menu') ?> href="index.php?file=Admin&amp;page=menu"><?php echo _MENU ?></a></li>
                                <li><a <?php printAdminSubMenuCurrentClass('smilies') ?> href="index.php?file=Admin&amp;page=smilies"><?php echo _SMILEY ?></a></li>
                            </ul>
                        </li>
                        <!-- SUB MENU : CONTENU -->
                        <li>
<?php
    list($moduleCurrentClass, $modulesList) = getAdminModulesMenuData();
?>
                            <a href="#" class="nav-top-item<?php echo $moduleCurrentClass ?>"><?php echo _CONTENU ?></a>
                            <ul>
<?php
    foreach ($modulesList as $module => $moduleName) :
        if (is_dir('modules/'. $module .'/backend')) :
            if ($GLOBALS['file'] == $module) :
?>
                                <li><a class="current" href="index.php?admin=<?php echo $module ?>"><?php echo $moduleName ?></a></li>
<?php
            else :
?>
                                <li><a href="index.php?admin=<?php echo $module ?>"><?php echo $moduleName ?></a></li>
<?php
            endif;
        else :
            if ($GLOBALS['file'] == $module && $GLOBALS['page'] == 'admin') :
?>
                                <li><a class="current" href="index.php?file=<?php echo $module ?>&amp;page=admin"><?php echo $moduleName ?></a></li>
<?php
            else :
?>
                                <li><a href="index.php?file=<?php echo $module ?>&amp;page=admin"><?php echo $moduleName ?></a></li>
<?php
            endif;
        endif;
    endforeach;
?>

                            </ul>
                        </li>
<?php
    list($moduleCurrentClass, $modulesList) = getAdminModulesMenuData('gaming');

    if ($modulesList) :
?>
                        <!-- SUB MENU : GAMING -->
                        <li>

                            <a href="#" class="nav-top-item<?php echo $moduleCurrentClass ?>"><?php echo __('GAMING') ?></a>
                            <ul>
<?php
        foreach ($modulesList as $module => $moduleName) :
            if (is_dir('modules/'. $module .'/backend')) :
                if ($GLOBALS['file'] == $module) :
?>
                                <li><a class="current" href="index.php?admin=<?php echo $module ?>"><?php echo $moduleName ?></a></li>
<?php
                else :
?>
                                <li><a href="index.php?admin=<?php echo $module ?>"><?php echo $moduleName ?></a></li>
<?php
                endif;
            else :
                if ($GLOBALS['file'] == $module && $GLOBALS['page'] == 'admin') :
?>
                                <li><a class="current" href="index.php?file=<?php echo $module ?>&amp;page=admin"><?php echo $moduleName ?></a></li>
<?php
                else :
?>
                                <li><a href="index.php?file=<?php echo $module ?>&amp;page=admin"><?php echo $moduleName ?></a></li>
<?php
                endif;
            endif;
        endforeach;
?>

                            </ul>
                        </li>
<?php
    endif;
?>
                        <li>
                            <!-- SUB MENU : DIVERS -->
                            <a href="#" class="nav-top-item<?php printAdminMenuCurrentClass('miscellaneous') ?>"><?php echo _DIVERS ?></a>
                            <ul>
                                <li><a href="http://www.nuked-klan.org/index.php?file=Forum" target="_blank"><?php echo _OFFICIEL ?></a></li>
                                <li><a <?php printAdminSubMenuCurrentClass('licence') ?> href="index.php?file=Admin&amp;page=licence"><?php echo _LICENCE ?></a></li>
                                <li><a <?php printAdminSubMenuCurrentClass('propos') ?> href="index.php?file=Admin&amp;page=propos"><?php echo _PROPOS ?></a></li>
                            </ul>
                        </li>
                    </ul>
                    <!-- End Accordion Menu -->
                    <!-- Messages are shown when a link with these attributes are clicked: href="#messages" rel="modal"  -->
                    <div id="messages">
                        <h3><?php echo _DISCUADMIN ?>:</h3>
                        <div id="content_messages">

<?php
    foreach (getDiscussionData() as $discussion) :
?>
                            <p><strong><?php echo nkDate($discussion['date']) ?></strong> <?php echo _BY ?> <?php echo $discussion['author'] ?><br /><?php echo $discussion['texte'] ?></p>
<?php
    endforeach
?>

                        </div>
                        <form method="post" onsubmit="maFonctionAjax(this.texte.value);return false" action="">
                            <h4><?php echo _NEWMSG ?>:</h4>
                            <fieldset>
                                <textarea name="texte" cols="79" rows="5"></textarea>
                            </fieldset>
                            <fieldset>
                                <input class="button" type="submit" value="Send" />
                            </fieldset>
                        </form>
                        <div id="affichefichier"></div>
                    </div>
                    <!-- End #messages -->

                </div>
                <!-- End #sidebar-wrapper -->

            </div>
            <!-- End #sidebar -->

            <!-- Main Content Section with everything -->
            <div id="main-content">
                <div style="width:100%;height:100%;display:block">
                    <!-- Show a notification if the user has disabled javascript -->
                    <noscript>
                        <div class="notification error png_bg">
                            <div><?php echo _JAVA ?></div>
                        </div>
                    </noscript>