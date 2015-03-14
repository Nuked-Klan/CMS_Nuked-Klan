<?php
/**
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
if (!defined("INDEX_CHECK"))
{
    die ("<div style=\"text-align: center;\">You cannot open this page directly</div>");
}

global $user, $language, $nuked, $cookie_captcha, $random_code, $bgcolor3;

translate("modules/Forum/lang/" . $language . ".lang.php");
include("modules/Forum/template.php");

// Inclusion système Captcha
include_once("Includes/nkCaptcha.php");

// On initialise le captcha
$captcha = initCaptcha();

opentable();

if (!$user) {
    $visiteur = 0;
}
else {
    $visiteur = $user[1];
}

$ModName = basename(dirname(__FILE__));
$level_access = nivo_mod($ModName);

if ($visiteur >= $level_access && $level_access > -1) {

    define('EDITOR_CHECK', 1);

    $sql = mysql_query("SELECT nom, cat, level_poll FROM " . FORUM_TABLE . " WHERE '" . $visiteur . "' >= niveau AND id = '" . $_REQUEST['forum_id'] . "'");
    $level_ok = mysql_num_rows($sql);

        // No user access
        if ($level_ok == 0) {
          echo '<div id="nkAlertError" class="nkAlert">' . _NOACCESSFORUM . '</div>';
        }
        // User access
        else {
            list($nom, $cat, $level_poll) = mysql_fetch_array($sql);

            $result = mysql_query("SELECT moderateurs FROM " . FORUM_TABLE . " WHERE '" . $visiteur . "' >= niveau AND id = '" . $_REQUEST['forum_id'] . "'");
            list($modos) = mysql_fetch_array($result);

            $select_cat = mysql_query('SELECT nom FROM ' . FORUM_CAT_TABLE . ' WHERE id = ' . $cat);
            list($cat_name) = mysql_fetch_array($select_cat);

            if ($user && $modos != "" && strpos($user[0], $modos)) {
                $administrator = 1;
            }
            else {
                $administrator = 0;
            }

            if ($_REQUEST['do'] == "edit" || $_REQUEST['do'] == "quote") {
                $result = mysql_query("SELECT txt, titre, auteur, usersig, emailnotify FROM " . FORUM_MESSAGES_TABLE . " WHERE id = '" . $_REQUEST['mess_id'] . "' AND forum_id = '" . $_REQUEST['forum_id'] . "'");
                list($e_txt, $e_titre, $author, $usersig, $emailnotify) = mysql_fetch_array($result);

                $e_titre = printSecuTags($e_titre);
            }

            if ($_REQUEST['thread_id'] != "") {
                $action = "index.php?file=Forum&amp;op=reply";
                $action_name = _POSTREPLY;
            }
            else if ($_REQUEST['do'] == "edit") {
                $action = "index.php?file=Forum&amp;op=edit";
                $action_name = _POSTEDIT;
            }
            else {
                $action = "index.php?file=Forum&amp;op=post";
                $action_name = _POSTNEWTOPIC;
            }

            //Construction du Breadcrump
            $category = '-> <a href="index.php?file=Forum&amp;cat='.$cat.'"><strong>'.$cat_name.'</strong></a>&nbsp;';
            $topic = '-> <a href="index.php?file=Forum&amp;page=viewforum&amp;forum_id=' . $_REQUEST['forum_id'] . '"><strong>'.$nom.'</strong></a>&nbsp;';
            $nav = $category.$topic;

    //Initialisation de la couleur des catégories en fonction du bgcolor
    if(isset($GLOBALS['bgcolor1']) && isset($GLOBALS['bgcolor2']) && isset($GLOBALS['bgcolor3']) && isset($GLOBALS['bgcolor4'])){
?>
        <style type="text/css">
            .nkForumPostHead{
                background: <?php echo $GLOBALS['bgcolor3']; ?>
            }
        </style>
<?php
    }
?>
    <div id="nkForumWrapper">
        <form method="post" action="<?php echo $action; ?>" enctype="multipart/form-data">
            <div id="nkForumBreadcrumb">
                <a href="index.php?file=Forum"><strong><?php echo _INDEXFORUM; ?></strong></a>&nbsp;<?php echo $nav; ?>
            </div>
            <div class ="nkForumPostHead">
                <h3><?php echo $action_name; ?></h3>
            </div>
            <div class="nkForumPost">
                <div class="nkForumCatWrapper">
                    <div class="nkForumPostContent">
                        
                        <div><!--Pseudo -->
                            <div class="nkForumPostCat nkBgColor2 nkBorderColor1">
                                <strong><?php echo _PSEUDO; ?></strong>
                            </div>
                            <div class="nkForumPostCatContent nkBgColor2 nkBorderColor1">
    <?php
                                if ($_REQUEST['do'] == "edit") {
                                    echo $author;
                                }
                                else if ($user[2] != "") {
    ?>
                                <?php echo $user[2]; ?>&nbsp;<a href="index.php?file=User&amp;nuked_nude=index&amp;op=logout" class="nkButton icon remove danger"><?php echo _FLOGOUT; ?></a>
                                <input type="hidden" name="auteur" value="<?php echo $user[2]; ?>" />
    <?php
                                }
                                else {
    ?>
                                <input type="text" name="auteur" size="35"  maxlength="35" />
                                <a href="index.php?file=User&amp;op=login_screen" class="nkButton icon user"><?php echo _FLOGIN; ?></a>
    <?php
                                }
    ?>
                            </div>
                        </div>

                        <div><!--Title -->
                            <div class="nkForumPostCat nkBgColor2 nkBorderColor1">
                                <strong><?php echo _TITLE; ?></strong>
                            </div>
                            <div class="nkForumPostCatContent nkBgColor2 nkBorderColor1">                            
<?php
                                if ($_REQUEST['thread_id'] != "") {
                                    $sql1 = mysql_query("SELECT titre, annonce FROM " . FORUM_THREADS_TABLE . " WHERE id = '" . $_REQUEST['thread_id'] . "' AND forum_id = '" . $_REQUEST['forum_id'] . "'");
                                    list($titre, $annonce) = mysql_fetch_array($sql1);
                                    $titre = nkHtmlEntities($titre);
                                    $titre = preg_replace("`&amp;lt;`i", "&lt;", $titre);
                                    $titre = preg_replace("`&amp;gt;`i", "&gt;", $titre);
                                    $re_titre = "RE : " . $titre;
?>
                                    <input id="forum_titre" type="text" size="70"  maxlength="70" name="titre" value="<?php echo $re_titre; ?>" />
<?php
                                }
                                else if ($_REQUEST['do'] == "edit") {
?>
                                    <input id="forum_titre" type="text" size="70"  maxlength="70" name="titre" value="<?php echo $e_titre; ?>" />
<?php
                                }
                                else {
?>
                                    <input id="forum_titre" size="70"  maxlength="70" type="text" name="titre" />
<?php
                                }
                                if ($_REQUEST['do'] == "edit") {
?>
                                    <input type="hidden" name="author" value="<?php echo $author; ?>" />
<?php
                                }
?>
                            </div>
                        </div>

                        <div><!--Message -->
                            <div class="nkForumPostCat nkBgColor2 nkBorderColor1">
                                <strong><?php echo _MESSAGE; ?></strong>
                            </div>
                            <div class="nkForumPostCatContent nkBgColor2 nkBorderColor1">                            
<?php

                                if ($_REQUEST['do'] == "edit") {
                                    $ftexte = $e_txt;
                                }
                                else if ($_REQUEST['do'] == "quote") {
                                    //$ftexte = '<blockquote style="border: 1px dashed ' . $bgcolor3 . '; background: #FFF; color: #000; padding: 5px"><strong>' . _QUOTE . ' ' . _BY . ' ' . $author . ' :</strong><br />' . $e_txt . '</blockquote>';
                                    $ftexte = '<blockquote class="nkForumBlockQuote"><cite>' . _QUOTE . ' ' . _BY . ' ' . $author . ' :</cite><br />' . $e_txt . '</blockquote>';
                                }

                                $ftexte = editPhpCkeditor($ftexte);

                                if ($_REQUEST['do'] == "quote") {
?>
                                    <textarea id="e_advanced" name="texte" cols="70" rows="15"><?php echo $ftexte; ?><p></p></textarea>
<?php
                                }
                                else {
?>
                                    <textarea id="e_advanced" name="texte" cols="70" rows="15"><?php echo $ftexte; ?></textarea>
<?php
                                }
?>
                            </div>
                        </div>
<?php

                        //Checked params
                        if ($_REQUEST['do'] == "edit" && $usersig == 1) {
                            $checked1 = "checked=\"checked\"";
                        }
                        else if ($_REQUEST['do'] == "edit" && $usersig == 0) {
                            $checked1 = "";
                        }
                        else {
                            $checked1 = 'checked=checked';
                        }

                        if ($emailnotify == 1) {
                            $checked2 = 'checked=checked';
                        }
                        else {
                                $checked2 = "";
                        }

                        if ($annonce == 1) {
                            $checked3 = 'checked=checked';
                        }
                        else {
                                $checked3 = "";
                        }
?>
                        <div><!--Options -->
                            <div class="nkForumPostCat nkBgColor2 nkBorderColor1">
                                <strong><?php echo _OPTIONS; ?></strong>
                            </div>
                            <div class="nkForumPostCatContent nkBgColor2 nkBorderColor1">  
<?php

                                if ($visiteur > 0) {
?>
                                    <input id="forum_sign" type="checkbox" class="checkbox" name="usersig" value="1" <?php echo $checked1; ?> />&nbsp;<?php echo _USERSIGN; ?><br />
                                    <input type="checkbox" class="checkbox" name="emailnotify" value="1" <?php echo $checked2; ?> />&nbsp;<?php echo _EMAILNOTIFY; ?><br />
<?php
                                }

                                if ($_REQUEST['do'] == "edit") {
                                    if($force_edit_message == 'on' && $administrator != 1) {
?>
                                        <input type="hidden" name="edit_text" value="1" />
<?php
                                    }
                                    else {
?>
                                        <input type="checkbox" name="edit_text" value="1" checked="checked" />&nbsp;<?php echo _EDITTEXT; ?>
<?php
                                    }
                                }

                                if ($_REQUEST['thread_id'] != "" || $_REQUEST['do'] == "edit") {
                                    echo "<br />";
                                }
                                else {

                                    if ($user[1] >= admin_mod("Forum") || $administrator == 1) {
?>
                                        <input type="checkbox" class="checkbox" name="annonce" value="1"<?php echo $checked3; ?> />&nbsp;<?php echo _ANNONCE; ?><br />
<?php
                                    }
                                }
?>
                            </div>
                        </div>
<?php

                        if ($visiteur < $level_poll || $_REQUEST['thread_id'] != "" || $_REQUEST['do'] == "edit") {
                            echo "";
                        }
                        else {
?>
                        <div><!--Sondage -->
                            <div class="nkForumPostCat nkBgColor2 nkBorderColor1">
                                <strong><?php echo _SURVEY; ?></strong>
                            </div>
                            <div class="nkForumPostCatContent nkBgColor2 nkBorderColor1">  
                                <input type="checkbox" class="checkbox" name="survey" value="1" />&nbsp;<?php echo _POSTSURVEY; ?><br />
                                <input type="text" name="survey_field" size="2" value="4" />&nbsp;<?php echo _SURVEYFIELD; ?>&nbsp;(<?php echo _MAX; ?> : <?php echo $nuked['forum_field_max']; ?>)
                            </div>
                        </div>
<?php
                        }

                        if ($visiteur >= $nuked['forum_file_level'] && $nuked['forum_file'] == "on" && $nuked['forum_file_maxsize'] > 0 && $_REQUEST['do'] != "edit") {
                            if ($nuked['forum_file_maxsize'] >= 1000) {
                                $max_size = $nuked['forum_file_maxsize'] * 1000;
                                $maxfile = $nuked['forum_file_maxsize'] / 1000;
                                $maxfilesize = $maxfile . "&nbsp;" . _MO;
                            }
                            else {
                                $maxfilesize = $nuked['forum_file_maxsize'] . "&nbsp;" . _KO;
                            }

?>
                        <div><!--attachedFile -->
                            <div class="nkForumPostCat nkBgColor2 nkBorderColor1">
                                <strong><?php echo _ATTACHFILE; ?></strong>
                            </div>
                            <div class="nkForumPostCatContent nkBgColor2 nkBorderColor1">
                                <input type="file" name="fichiernom" size="30" />&nbsp;(<?php echo _MAXFILESIZE; ?> : <?php echo $maxfilesize; ?>)
                                <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_size; ?>" />
                            </div>
                        </div>
<?php
                        }
?>
                    </div>
                </div>
            </div>
            <div class ="nkForumPostbutton">
                <input type="submit" value="<?php echo _SEND; ?>" class="nkButton" />
                <input type="hidden" name="forum_id" value="<?php echo $_REQUEST['forum_id']; ?>" />
                <input type="hidden" name="thread_id" value="<?php echo $_REQUEST['thread_id']; ?>" />
                <input type="hidden" name="mess_id" value="<?php echo $_REQUEST['mess_id']; ?>" />
            </div>
<?php
                if ($GLOBALS['captcha'] === true) {
                    echo create_captcha();
                }
?>
        </form>
<?php

            if ($_REQUEST['thread_id'] != "")
            {
?>
            <div class ="nkForumPostHead">
                <h3><?php echo _PREVIOUSMESSAGES; ?></h3>
            </div>
            <div class="nkForumPostReview nkBgColor2 nkBorderColor1">
                <div class="nkForumCatWrapper">
                    <div class="nkForumPostReviewContent">            
<?php
                $sql2 = mysql_query("SELECT txt, auteur, date FROM " . FORUM_MESSAGES_TABLE . " WHERE thread_id = '" . $_REQUEST['thread_id'] . "' AND forum_id = '" . $_REQUEST['forum_id'] . "' ORDER BY date DESC LIMIT 0, 20");
                while (list($txt, $auteur, $date) = mysql_fetch_row($sql2)) {
                    
                    $date = nkDate($date);
                    $tmpcnt++ % 2 == 1 ? $color = $color1 : $color = $color2;
                    $auteur = nk_CSS($auteur);
?>
                        <div>
                            <div class="nkForumPostReviewAuthor">
                                <strong><?php echo $auteur; ?></strong>
                            </div>
                            <div class="nkForumPostReviewMessage">
                                <div>
                                    <img src="images/posticon.gif" alt="" />
                                    <?php echo _POSTEDON; ?>&nbsp;<?php echo $date ; ?>
                                </div>
                                <p><?php echo $txt; ?></p>
                            </div>
                        </div>
<?php
                }
?>
                    </div>
                </div>
            </div>
<?php 
            }
?>
    </div>
<?php        
        }

}
else if ($level_access == -1) {
    // On affiche le message qui previent l'utilisateur que le module est désactivé
    echo '<div id="nkAlertError" class="nkAlert">
            <strong>'._MODULEOFF.'</strong>
            <a href="javascript:history.back()"><span>'._BACK.'</span></a>
        </div>';
}
else if ($level_access == 1 && $visiteur == 0) {
    // On affiche le message qui previent l'utilisateur qu'il n'as pas accès à ce module
    echo '<div id="nkAlertError" class="nkAlert">
            <strong>'._USERENTRANCE.'</strong>
            <a href="index.php?file=User&amp;op=login_screen"><span>'._LOGINUSER.'</span></a>
            &nbsp;|&nbsp;
            <a href="index.php?file=User&amp;op=reg_screen"><span>'._REGISTERUSER.'</span></a>
        </div>';
}
else {
    // On affiche le message qui previent l'utilisateur que le module est désactivé
    echo '<div id="nkAlertError" class="nkAlert">
            <strong>'._NOENTRANCE.'</strong>
            <a href="javascript:history.back()"><span>'._BACK.'</span></a>
        </div>';
}

// Fermeture du conteneur de module
closetable();

?>
