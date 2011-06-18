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

global $user, $nuked, $language;


function admintop()
{
global $user, $nuked, $language;
translate("modules/Admin/lang/" . $language . ".lang.php");

$visiteur = $user ? $user[1] : 0;

$condition_js = ($nuked['screen']) == 'off' ? 'screenoff();' : 'document.getElementById("screen").style.display="block";';
if($visiteur < 2) redirect('index.php?file=404', 0);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
    <head>
        <meta name="keywords" content="<?php echo $nuked['keyword'] ?>" />
        <meta name="Description" content="<?php echo $nuked['description'] ?>" />
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

        <title><?php echo $nuked['name'] ?> - <?php echo $nuked['slogan'] ?></title>

        <link rel="shortcut icon"  href="images/favicon.ico" />
        <link rel="stylesheet" href="modules/Admin/css/reset.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="modules/Admin/css/style.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="modules/Admin/css/invalid.css" type="text/css" media="screen" />    

        <script type="text/javascript" src="modules/Admin/scripts/jquery-1.6.1.min.js"></script>
        <script type="text/javascript" src="modules/Admin/scripts/simpla.jquery.configuration.js"></script>
        <script type="text/javascript" src="modules/Admin/scripts/facebox.js"></script>

        <script type="text/javascript">
            function maFonctionAjax(texte)
            {
                var OAjax;
                if (window.XMLHttpRequest) OAjax = new XMLHttpRequest();
                else if (window.ActiveXObject) OAjax = new ActiveXObject('Microsoft.XMLHTTP'); 
                OAjax.open('POST','index.php?file=Admin&page=discussion',true);
                OAjax.onreadystatechange = function()
                {
                    if (OAjax.readyState == 4 && OAjax.status==200)
                    {
                        if (document.getElementById) 
                        {
                            document.getElementById('affichefichier').innerHTML = OAjax.responseText;
                            document.getElementById('texte').value = '';
                        }
                    }
                }

                OAjax.setRequestHeader('Content-type','application/x-www-form-urlencoded');
                OAjax.send('texte='+texte+'');
                $(document).trigger('close.facebox')
            }

            var xtralink = 'non';

            function screenon(lien,lien2)
            {
                xtralink = lien2;
                document.getElementById('iframe').innerHTML = '<iframe style="border: 0" width="100%" height="80%" src="'+lien+'"></iframe>';
                <?php echo $condition_js; ?>
            }
            function screenoff()
            {
                document.getElementById('screen').style.display='none';
                if (xtralink != 'non') window.location = xtralink;
            }
            function maFonctionAjax2(texte,type)
            {
                var OAjax;
                if (window.XMLHttpRequest) OAjax = new XMLHttpRequest();
                else if (window.ActiveXObject) OAjax = new ActiveXObject('Microsoft.XMLHTTP');
                OAjax.open('POST','index.php?file=Admin&page=notification',true);
                OAjax.onreadystatechange = function()
                {
                    if (OAjax.readyState == 4 && OAjax.status==200)
                    {
                        if (document.getElementById) 
                        {
                            document.getElementById('texte').value = '';
                            document.getElementById('type').value = '';
                        }
                    }
                }

                OAjax.setRequestHeader('Content-type','application/x-www-form-urlencoded');
                OAjax.send('texte='+texte+'&type='+type+'');
                $(document).trigger('close.facebox')
            }
            function maFonctionAjax3(texte)
            {
                var OAjax;
                if (window.XMLHttpRequest) OAjax = new XMLHttpRequest();
                else if (window.ActiveXObject) OAjax = new ActiveXObject('Microsoft.XMLHTTP');
                OAjax.open('POST','modules/'+texte+'/menu/<?php echo $language; ?>/menu.php',true);
                OAjax.onreadystatechange = function()
                {
                    if (OAjax.readyState == 4 && OAjax.status==200)
                    {
                        if (document.getElementById) document.getElementById('1').innerHTML = OAjax.responseText;
                    }
                }

                OAjax.setRequestHeader('Content-type','application/x-www-form-urlencoded');
                OAjax.send();
            }
            function del(id)
            {
                var OAjax;
                if (window.XMLHttpRequest) OAjax = new XMLHttpRequest();
                else if (window.ActiveXObject) OAjax = new ActiveXObject('Microsoft.XMLHTTP');
                OAjax.open('POST','index.php?file=Admin&page=notification&op=delete',true);
                OAjax.onreadystatechange = function()
                {
                    if (OAjax.readyState == 4 && OAjax.status==200)
                    {
                        if (document.getElementById) {}
                    }
                }

                OAjax.setRequestHeader('Content-type','application/x-www-form-urlencoded');
                OAjax.send('id='+id+'');
            }
        </script>
    </head>
    <body>
        <div id="screen" onclick="screenoff()">
            <div id="iframe"></div>
            <div id="iframe_close">&nbsp;</div>
        </div>

        <div id="body-wrapper"><!-- Wrapper for the radial gradient background -->
            <div id="sidebar">
                <div id="sidebar-wrapper"><!-- Sidebar with logo and menu -->
                    <a href="http://www.nuked-klan.org" target="_blanck"><img id="logo" src="modules/Admin/images/logo.png" alt="Simpla Admin logo" /></a><!-- Logo NK -->
                    <!-- Sidebar Profile links -->
                    <div id="profile-links">
                        <?php echo _BONJOUR; ?> <a href="index.php?file=User" title="<?php echo _EDIT; ?>"><?php echo $user[2];?></a>, <?php echo _VOIR; ?> <a href="#messages" rel="modal"><?php echo _MESSAGES; ?></a><br /><br />
                        <a onclick="javascript:screenon('index.php', 'non');return false" href="#"><?php echo _VOIRSITE; ?></a> | <a href="index.php?file=Admin&amp;page=deconnexion"><?php echo _DECONNEXION; ?></a><br />
                        <a href="index.php"><?php echo _RETOURNER; ?></a>
                    </div>

                <ul id="main-nav"><!-- Accordion Menu -->
                    <li>
                        <?php if($_REQUEST['file'] == 'Admin' && $_REQUEST['page'] == 'index')
                        echo '<a href="index.php?file=Admin" class="nav-top-item no-submenu current">';
                        else echo '<a href="index.php?file=Admin" class="nav-top-item no-submenu">';
                        echo _PANNEAU . '</a>',"\n"; ?>
                    </li>
                    <li><!-- SUB MENU : PARAMETERS -->
                        <?php if($_REQUEST['file'] == 'Admin' && ($_REQUEST['page'] == 'setting' || $_REQUEST['page'] == 'maj' || $_REQUEST['page'] == 'phpinfo' || 
                        $_REQUEST['page'] == 'mysql' || $_REQUEST['page'] == 'action' || $_REQUEST['page'] == 'erreursql')) echo '<a href="#" class="nav-top-item current">';
                        else echo '<a href="#" class="nav-top-item">';
                        echo _PARAMETRE . '</a>',"\n"; ?>
                        <ul>
                            <li>
                                <?php if($_REQUEST['file'] == 'Admin' && $_REQUEST['page'] == 'setting') echo '<a class="current" href="index.php?file=Admin&amp;page=setting">';
                                else echo '<a href="index.php?file=Admin&amp;page=setting">';
                                echo _PREFGEN . '</a>',"\n"; ?>
                            </li>
                            <li>
                                <?php if($_REQUEST['file'] == 'Admin' && $_REQUEST['page'] == 'mysql') echo '<a class="current" href="index.php?file=Admin&amp;page=mysql">';
                                else echo '<a href="index.php?file=Admin&amp;page=mysql">';
                                echo _GMYSQL . '</a>',"\n"; ?>
                            </li>
                            <li>
                                <?php if($_REQUEST['file'] == 'Admin' && $_REQUEST['page'] == 'phpinfo') echo '<a class="current" href="index.php?file=Admin&amp;page=phpinfo">';
                                else echo '<a href="index.php?file=Admin&amp;page=phpinfo">';
                                echo _ADMINPHPINFO . '</a>',"\n"; ?>
                            </li>
                            <li>
                                <?php if($_REQUEST['file'] == 'Admin' && $_REQUEST['page'] == 'maj') echo '<a class="current" href="index.php?file=Admin&amp;page=maj">';
                                else echo '<a href="index.php?file=Admin&amp;page=maj">';
                                echo _CHECKUPDATE . '</a>',"\n"; ?>
                            </li>
                            <li>
                                <?php if($_REQUEST['file'] == 'Admin' && $_REQUEST['page'] == 'action') echo '<a class="current" href="index.php?file=Admin&amp;page=action">';
                                else echo '<a href="index.php?file=Admin&amp;page=action">';
                                echo _ACTIONM . '</a>',"\n"; ?>
                            </li>
                            <li>
                                <?php
                                if($_REQUEST['file'] == 'Admin' && $_REQUEST['page'] == 'erreursql') echo '<a class="current" href="index.php?file=Admin&amp;page=erreursql">';
                                else echo '<a href="index.php?file=Admin&amp;page=erreursql">';
                                echo _ERRORBDD . '</a>',"\n"; ?>
                            </li>
                        </ul>
                </li>
                <li><!-- SUB MENU : GESTION -->
                    <?php if($_REQUEST['file'] == 'Admin' && ($_REQUEST['page'] == 'user' || $_REQUEST['page'] == 'theme' || $_REQUEST['page'] == 'modules' || 
                    $_REQUEST['page'] == 'block' || $_REQUEST['page'] == 'menu' || $_REQUEST['page'] == 'smilies' || $_REQUEST['page'] == 'games')) echo '<a href="#" class="nav-top-item current">';
                    else echo '<a href="#" class="nav-top-item">';
                    echo _GESTIONS . '</a>',"\n"; ?>
                    <ul>
                        <li>
                            <?php if($_REQUEST['file'] == 'Admin' && $_REQUEST['page'] == 'user') echo '<a class="current" href="index.php?file=Admin&amp;page=user">';
                            else echo '<a href="index.php?file=Admin&amp;page=user">';
                            echo _UTILISATEURS . '</a>',"\n"; ?>
                        </li>
                        <?php if(file_exists('themes/' . $nuked['theme'] . '/admin.php')) {
                        echo '<li>',"\n";
                            if($_REQUEST['file'] == 'Admin' && $_REQUEST['page'] == 'theme') echo '<a class="current" href="index.php?file=Admin&amp;page=theme">';
                            else echo '<a href="index.php?file=Admin&amp;page=theme">';
                        echo _THEMIS . '</a></li>',"\n";
                        }
                        ?>
                        <li>
                            <?php if($_REQUEST['file'] == 'Admin' && $_REQUEST['page'] == 'modules') echo '<a class="current" href="index.php?file=Admin&amp;page=modules">';
                            else echo '<a href="index.php?file=Admin&amp;page=modules">';
                            echo _INFOMODULES . '</a>',"\n"; ?>
                        </li>
                        <li>
                            <?php if($_REQUEST['file'] == 'Admin' && $_REQUEST['page'] == 'block') echo '<a class="current" href="index.php?file=Admin&amp;page=block">';
                            else echo '<a href="index.php?file=Admin&amp;page=block">';
                            echo _BLOCK . '</a>',"\n"; ?>
                        </li>
                        <li>
                            <?php if($_REQUEST['file'] == 'Admin' && $_REQUEST['page'] == 'menu') echo '<a class="current" href="index.php?file=Admin&amp;page=menu">';
                            else echo '<a href="index.php?file=Admin&amp;page=menu">';
                            echo _MENU . '</a>',"\n"; ?>
                        </li>
                        <li>
                            <?php if($_REQUEST['file'] == 'Admin' && $_REQUEST['page'] == 'smilies') echo '<a class="current" href="index.php?file=Admin&amp;page=smilies">';
                            else echo '<a href="index.php?file=Admin&amp;page=smilies">'; 
                            echo _SMILEY . '</a>',"\n"; ?>
                        </li>
                        <li>
                            <?php if($_REQUEST['file'] == 'Admin' && $_REQUEST['page'] == 'games') echo '<a class="current" href="index.php?file=Admin&amp;page=games">';
                            else echo '<a href="index.php?file=Admin&amp;page=games">';
                            echo _JEUX . '</a>',"\n"; ?>
                        </li>
                    </ul>
                </li>
                <li>
                    <?php
                        $modules = array();
                        $sql = mysql_query("SELECT nom FROM " . MODULES_TABLE . " WHERE '" . $visiteur . "' >= admin AND niveau > -1 AND admin > -1 ORDER BY nom");
                        while (list($mod) = mysql_fetch_array($sql))
                        {
                            if ($mod == 'Gallery') $modname = _NAMEGALLERY;
                            else if ($mod == 'Calendar') $modname = _NAMECALANDAR;
                            else if ($mod == 'Defy') $modname = _NAMEDEFY;
                            else if ($mod == 'Download') $modname = _NAMEDOWNLOAD;
                            else if ($mod == 'Guestbook') $modname = _NAMEGUESTBOOK;
                            else if ($mod == 'Irc') $modname = _NAMEIRC;
                            else if ($mod == 'Links') $modname = _NAMELINKS;
                            else if ($mod == 'Wars') $modname = _NAMEMATCHES;
                            else if ($mod == 'News') $modname = _NAMENEWS;
                            else if ($mod == 'Recruit') $modname = _NAMERECRUIT;
                            else if ($mod == 'Sections') $modname = _NAMESECTIONS;
                            else if ($mod == 'Server') $modname = _NAMESERVER;
                            else if ($mod == 'Suggest') $modname = _NAMESUGGEST;
                            else if ($mod == 'Survey') $modname = _NAMESURVEY;
                            else if ($mod == 'Forum') $modname = _NAMEFORUM;
                            else if ($mod == 'Textbox') $modname = _NAMESHOUTBOX;
                            else if ($mod == 'Comment') $modname = _NAMECOMMENT;
                            else $modname = $mod;

                            array_push($modules, $modname . '|' . $mod);
                        }

                        natcasesort($modules);
                        foreach($modules as $value)
                        {
                            $temp = explode('|', $value);

                            if (is_file('modules/' . $temp[1] . '/admin.php'))
                            {
                                if ($_REQUEST['file'] == $temp[1] && $_REQUEST['page'] == 'admin') $modulecur = true;
                            }
                        }

                        if($modulecur == true) echo '<a href="#" class="nav-top-item current">';
                        else echo '<a href="#" class="nav-top-item">';

                        echo _CONTENU . '</a><ul>',"\n";
                        foreach($modules as $value)
                        {
                            $temp = explode('|', $value);

                            if (is_file('modules/' . $temp[1] . '/admin.php'))
                            {
                                if ($_REQUEST['file'] == $temp[1] && $_REQUEST['page'] == 'admin')
                                {
                                    echo '<li><a class="current" href="index.php?file=' . $temp[1] . '&amp;page=admin">' . $temp[0] . '</a><li>',"\n";
                                    $modulecur = true;
                                }
                                else echo '<li><a href="index.php?file=' . $temp[1] . '&amp;page=admin">' . $temp[0] . '</a><li>',"\n";
                            }
                        }
                    ?>
                </ul>
            </li>
            <li>
                <?php if($_REQUEST['file'] == 'Admin' && ($_REQUEST['page'] == 'propos' || $_REQUEST['page'] == 'licence')) echo '<a href="#" class="nav-top-item current">';
                else echo '<a href="#" class="nav-top-item">';
                echo _DIVERS . '</a>',"\n"; ?>
                <ul>
                    <li>
                        <a href="http://www.nuked-klan.org/index.php?file=Forum" target="_blanck"><?php echo _OFFICIEL; ?></a>
                    </li>
                    <li>
                        <?php if($_REQUEST['file'] == 'Admin' && $_REQUEST['page'] == 'licence') echo '<a class="current" href="index.php?file=Admin&amp;page=licence">';
                        else echo '<a href="index.php?file=Admin&amp;page=licence">';
                        echo _LICENCE . '</a>',"\n"; ?>
                    </li>
                    <li>
                        <?php if($_REQUEST['file'] == 'Admin' && $_REQUEST['page'] == 'propos') echo '<a class="current" href="index.php?file=Admin&amp;page=propos">';
                        else echo '<a href="index.php?file=Admin&amp;page=propos">';
                        echo _PROPOS . '</a>',"\n"; ?>
                    </li>
                </ul>
            </li>
        </ul><!-- End #main-nav -->
        <div id="messages"><!-- Messages are shown when a link with these attributes are clicked: href="#messages" rel="modal"  -->
            <h3><?php echo _DISCUADMIN; ?>:</h3>            
            <div id="content_messages">    
                <?php
                $sql = mysql_query("SELECT date, pseudo, texte  FROM " . $nuked['prefix'] . "_discussion ORDER BY date DESC LIMIT 0, 16");
                while (list($date, $users, $texte) = mysql_fetch_array($sql))
                {
                    $sql2 = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '" . mysql_real_escape_string($users) . "'");
                    list($pseudo) = mysql_fetch_array($sql2);
                ?>
                <p>
                    <strong><?php echo nkDate($date); ?></strong> <?php echo _BY; ?> <?php echo $pseudo; ?><br />
                    <?php echo $texte; ?>
                </p>
                <?php } ?>
            </div>    
            <form method="post" onsubmit="maFonctionAjax(this.texte.value);return false" action="">
                <h4><?php echo _NEWMSG; ?>:</h4>
                <fieldset>
                    <textarea name="texte" cols="79" rows="5"></textarea>
                </fieldset>
                <fieldset>
                    <input class="button" type="submit" value="Send" />
                </fieldset>
            </form> 
            <div id="affichefichier"></div>
        </div> <!-- End #messages -->
    </div>
</div> <!-- End #sidebar -->
<div id="main-content"> <!-- Main Content Section with everything -->
    <div style="width:100%;">
        <noscript> <!-- Show a notification if the user has disabled javascript -->
            <div class="notification error png_bg">
                <div>
                    <?php echo _JAVA; ?>
                </div>
            </div>
        </noscript>
<?php
}
function adminfoot()
{
global $language;
?>
                <script type="text/javascript" src="media/ckeditor/ckeditor_basic.js"></script>
                <script type="text/javascript">
                //<![CDATA[
                    CKEDITOR.replaceAll( 'editor' );
                    CKEDITOR.config.scayt_sLang = "<?php echo ($language == 'french') ? 'fr_FR' : 'en_US'; ?>";
                //]]>
                </script>
            </div>
        </div>
<?php
}
?>