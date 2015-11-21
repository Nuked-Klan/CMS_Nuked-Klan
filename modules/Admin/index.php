<?php
/**
 * games.php
 *
 * Backend of Admin module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Admin', ADMINISTRATOR_ACCESS))
    return;


    ?>
    <!-- Page Head -->
    <h2><?php echo _BONJOUR . '&nbsp;' . $user[2]; ?></h2>
    <p id="page-intro"><?php echo _MESSAGEDEBIENVENUE; ?></p>
        <div style="text-align: right">
        <form method="post" onsubmit="maFonctionAjax3(this.module.value);return false" action="">
            <fieldset>
            <select id="module" name="module">
                <option value="Admin"><?php echo _PANNEAU; ?></option>
                <?php
                $modules = array();
                $sql = mysql_query('SELECT nom FROM ' . MODULES_TABLE . ' WHERE "' . $visiteur . '" >= admin AND niveau > -1 AND admin > -1 ORDER BY nom');
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

                    if (is_file('modules/' . $temp[1] . '/admin.php') AND is_file('modules/' . $temp[1] . '/menu/'.$language.'/menu.php'))
                    {
                        echo '<option value="' . $temp[1] . '">' . $temp[0] . '</option>';
                    }
                }
                ?>
            </select>
            <input class="button" type="submit" value="Send" />
            </fieldset>
        </form>
        </div>
        <ul class="shortcut-buttons-set" id="1">
            <li>
                <a class="shortcut-button" href="modules/Admin/menu/<?php echo $language; ?>/aide.php" rel="modal">
                    <img src="modules/Admin/images/icons/aide.png" alt="icon" />
                    <span><?php echo _AIDE; ?></span>
                </a>
            </li>

            <li>
                <a class="shortcut-button" rel="modal" href="index.php?file=Stats&amp;page=admin&amp;op=statsPopup">
                    <img src="modules/Admin/images/icons/statistiques.png" alt="icon" />
                    <span><?php echo _STATS; ?></span>
                </a>
            </li>

            <li>
                <a class="shortcut-button" href="index.php?file=Admin&amp;page=erreursql">
                    <img src="modules/Admin/images/icons/erreur.png" alt="icon" />
                    <span><?php if($language=='english'){echo '<br/>';} echo _SQL; ?></span>
                </a>
            </li>

            <li>
                <a class="shortcut-button" href="#notification" rel="modal">
                    <img src="modules/Admin/images/icons/megaphone.png" alt="icon" />
                    <span><?php echo _NOTIFICATION; ?></span>
                </a>
            </li>

            <li>
                <a class="shortcut-button" href="#messages" rel="modal">
                    <img src="modules/Admin/images/icons/comment_48.png" alt="icon" />
                    <span><?php echo _DISCUSSION; ?></span>
                </a>
            </li>
            <?php
                if(file_exists('themes/' . $nuked['theme'] . '/admin.php'))
                {
                    if (file_exists('themes/' . $nuked['theme'] . '/images/adminpanel.png'))
                        $imagepanel = 'themes/' . $nuked['theme'] . '/images/adminpanel.png';
                    else $imagepanel = 'modules/Admin/images/icons/logo.png';
                ?>
                <li>
                    <a class="shortcut-button" href="index.php?file=Admin&amp;page=theme">
                        <img src="<?php echo $imagepanel; ?>" alt="icon" />
                        <span><?php echo _THEMEPANEL; ?></span>
                    </a>
                </li>
            <?php
                }
            ?>
        </ul><!-- End .shortcut-buttons-set -->

        <div class="clear"></div><!-- End .clear -->
            <div id="notification" style="display: none">
                <h3><?php echo _NOTIFICATION; ?>:</h3>
                <form method="post" onsubmit="maFonctionAjax2(this.texte.value,this.type.value);return false" action="">
                    <h4><?php echo _MESSAGE; ?>:</h4>
                    <fieldset>
                        <textarea name="texte" cols="79" rows="5"></textarea>
                    </fieldset>

                    <fieldset>
                        <select id="type" name="type" class="small-input">
                            <option value="0"><?php echo _TYPE; ?>...</option>
                            <option value="1"><?php echo _INFO; ?></option>
                            <option value="2"><?php echo _ECHEC; ?></option>
                            <option value="3"><?php echo _REUSSITE; ?></option>
                            <option value="4"><?php echo _ALERTE; ?></option>
                        </select>

                        <input class="button" type="submit" value="Send" />
                    </fieldset>
                </form>
            </div>

            <div style="width: 100%">
                <div class="content-box column-center">
                    <div class="content-box-header" style="margin-bottom: 0">
                        <h3><?php echo _WEBSITEACTIVITY; ?></h3>
                    </div><!-- End .content-box-header -->

                    <div class="content-box-content">
                        <div class="tab-content default-tab">
                            <div id="nkLastMembers">
                                <h5><?php echo _LASTMEMBERS; ?></h5>
                                <div>
                                <?php
                                    $sqlUser=mysql_query("SELECT pseudo, date, id, country FROM " . USER_TABLE . " ORDER BY date DESC LIMIT 0, 6 ");
                                    while (list($userPseudo, $regiterDate, $userId, $userCountry)=mysql_fetch_row($sqlUser)) {
                                    
                                    $userPseudo = stripslashes($userPseudo);
                                    $regiterDate = nkDate($regiterDate);
                                    $titleCountry = str_replace(".gif", "", $userCountry);
                                    
                                    if ( strlen($userPseudo) > 30 ) { $userPseudo = substr($userPseudo, 0, 30)."..."; }
                                ?> 
                                    <div>
                                        <img src="images/flags/<?php echo $userCountry;?>" alt="<?php echo $userCountry;?>" title="<?php echo $titleCountry;?>" />
                                        <a href="index.php?file=Admin&amp;page=user&amp;op=edit_user&amp;id_user=<?php echo $userId; ?>" title="<?php echo _DATEREGISTRATION;?><?php echo $regiterDate;?>"><?php echo $userPseudo; ?></a>
                                    </div>                      
                                <?php
                                }
                                ?>
                                    <p>
                                       <a href="index.php?file=Admin&amp;page=user&amp;orderby=date"><b><?php echo _SEEMORE;?></b></a>
                                    </p>
                                </div>
                            </div><!--
                         --><div id="nkLastVisit">
                                <h5><?php echo _LASTVISITS; ?></h5>
                                <div>
                                <?php
                                    $sqlLastVisit = mysql_query("SELECT UT.id, UT.pseudo, UT.niveau, ST.last_used FROM " . USER_TABLE . " as UT LEFT OUTER JOIN " . SESSIONS_TABLE . " as ST ON UT.id=ST.user_id WHERE UT.niveau > 0 ORDER BY ST.last_used DESC LIMIT 0, 6 ");
                                    while (list($idLastUsed, $pseudoLastused, $niveauLastUsed, $lastUsed) = mysql_fetch_array($sqlLastVisit))
                                    {
                                        $lastUsed == '' ? $lastUsed = '-' : $lastUsed = nkDate($lastUsed);
                                    
                                    if ( strlen($pseudoLastused) > 30 ) { $pseudoLastused = substr($pseudoLastused, 0, 30)."..."; }
                                ?> 
                                    <div>
                                        <span><strong><?php echo $pseudoLastused;?></strong>&nbsp;:&nbsp;<?php echo $lastUsed;?></span>
                                    </div>                      
                                <?php
                                }
                                ?>
                                    <p>
                                       <a href="index.php?file=Admin&amp;page=user&amp;orderby=last_date"><b><?php echo _SEEMORE;?></b></a>
                                    </p>
                                </div>
                            </div><!--
                         --><div id="nkLastComments">
                                <h5><?php echo _LASTCOMMENTS; ?></h5>
                                <div>
                                <?php
                                    $sqlLastComment = mysql_query("SELECT module, im_id, autor, date FROM " . COMMENT_TABLE . " ORDER BY date DESC LIMIT 0, 6 ");
                                    $countComment = mysql_num_rows($sqlLastComment);
                                
                                if($countComment != 0){
                                    while (list($lastModuleComment, $modIdComment, $lastCommentAuthor, $lastCommentDate) = mysql_fetch_array($sqlLastComment))
                                    {
                                        $lastCommentDate = nkDate($lastCommentDate);
                                    
                                    if ( strlen($lastCommentAuthor) > 30 ) { $lastCommentAuthor = substr($lastCommentAuthor, 0, 30)."..."; }
                                    if ($lastModuleComment == "Links")    $commentLink = "index.php?file=Links&amp;op=description&amp;link_id=" . $modIdComment ."";    
                                    if ($lastModuleComment == "Gallery")  $commentLink = "index.php?file=Gallery&amp;op=description&amp;sid=" . $modIdComment ."";    
                                    if ($lastModuleComment == "news")     $commentLink = "index.php?file=News&amp;op=index_comment&amp;news_id=" . $modIdComment ."";
                                    if ($lastModuleComment == "Sections") $commentLink = "index.php?file=Sections&amp;op=article&amp;artid=" . $modIdComment ."";    
                                    if ($lastModuleComment == "Download") $commentLink = "index.php?file=Download&amp;op=description&amp;dl_id=" . $modIdComment ."";    
                                    if ($lastModuleComment == "Survey")   $commentLink = "index.php?file=Survey&amp;op=affich_res&amp;poll_id=" . $modIdComment ."";
                                    if ($lastModuleComment == "match")    $commentLink = "index.php?file=Wars&amp;op=detail&amp;war_id=" . $modIdComment ."";
                                ?> 
                                    <div>
                                        <span><strong><?php echo $lastCommentAuthor;?></strong>&nbsp;<?php echo _HAS_COMMENTED_MOD;?>&nbsp;<a href="<?php echo $commentLink;?>" title="<?php echo _POSTED;?>&nbsp;<?php echo $lastCommentDate;?>"><?php echo $lastModuleComment; ?></a>
                                        </span>
                                    </div>                      
                                <?php
                                    }
                                ?>
                                    <p>
                                       <a href="index.php?file=Comment&amp;page=admin"><b><?php echo _SEEMORE;?></b></a>
                                    </p>
                                <?php
                                }
                                else {
                                ?> 
                                    <p><?php echo _NOCOMMENT;?></p>                     
                                <?php                                    
                                }
                                ?>
                                </div>
                            </div><!--
                         --><div id="nkStats">
                                <h5><?php echo _STATS; ?></h5>
                                <div>
                                <?php
                                    $sqlStats = mysql_query('SELECT
                                        (SELECT COUNT(id) FROM ' . USER_TABLE . ') AS nb_us,
                                        (SELECT COUNT(id) FROM ' . FORUM_MESSAGES_TABLE . ') AS nb_mess,
                                        (SELECT SUM(count) FROM ' . STATS_TABLE . ') AS count');
                                    list($nbUser, $nbPost, $visitCounter) = mysql_fetch_array($sqlStats);

                                    $nb = nbvisiteur();
                                ?> 
                                    <div>
                                        <span><?php echo _TOTALMEMBERS;?>&nbsp;:&nbsp;<?php echo $nbUser;?></span>
                                    </div>
                                    <div>
                                        <span><?php echo _TOTALVISITS;?>&nbsp;:&nbsp;<?php echo $visitCounter;?></span>
                                    </div>
                                    <div>
                                        <span><?php echo _TOTALFORUMMESSAGES;?>&nbsp;:&nbsp;<?php echo $nbPost;?></span>
                                    </div>
                                    <div>
                                        <span><?php echo _ONLINEVISITORS;?>&nbsp;:&nbsp;<?php echo $nb[0];?></span>
                                    </div>
                                    <div>
                                        <span><?php echo _ONLINEMEMBERS;?>&nbsp;:&nbsp;<?php echo $nb[1];?></span>
                                    </div>
                                    <div>
                                        <span><?php echo _ONLINEADMINS;?>&nbsp;:&nbsp;<?php echo $nb[2];?></span>
                                    </div>
                                    <p>
                                       <a href="index.php?file=Stats&amp;page=admin"><b><?php echo _SEEMORE;?></b></a>
                                    </p>
                                </div>
                            </div>
                        </div><!-- End #tab3 -->
                    </div><!-- End .content-box-content -->
                </div><!-- End .content-box -->

                <div class="content-box column-left">
                    <div class="content-box-header" style="margin-bottom: 0">
                        <h3><?php echo _ANNONCES; ?></h3>
                    </div><!-- End .content-box-header -->

                    <div class="content-box-content">
                        <div class="tab-content default-tab" id="NKmess">
                            <p>
                                <?php echo _CONNECTNK; ?>
                            </p>
                        </div><!-- End #tab3 -->
                        <div class="tab-content default-tab" id="NKUpdate">
                            <!-- NK.UPDATE CONTENT -->
                        </div><!-- End #tab3 -->
                        <?php echo '<script type="text/javascript" src="http://www.nuked-klan.org/extra/message.php?version=' . $nuked['version'] . '&lang=' . $language . '"></script>'; ?>
                    </div><!-- End .content-box-content -->
                </div><!-- End .content-box -->

            <div class="content-box column-right">
                <div class="content-box-header" style="margin-bottom: 0"><!-- Add the class "closed" to the Content box header to have it closed by default -->
                    <h3><?php echo _ACTIONS; ?></h3>
                </div><!-- End .content-box-header -->

                <div class="content-box-content">
                    <div class="tab-content default-tab">
                        <h4><a href="index.php?file=Admin&amp;page=action"><?php echo _VIEWACTIONS; ?></a></h4>
                        <p>
                        <?php
                        $sql_act = mysql_query("SELECT date, pseudo, action  FROM " . $nuked['prefix'] . "_action ORDER BY date DESC LIMIT 0, 4");
                        while ($action = mysql_fetch_array($sql_act))
                        {
                            $sql = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '" . $action['pseudo'] . "'");
                            list($pseudo) = mysql_fetch_array($sql);

                            $action['action'] = $pseudo . ' ' . $action['action'];
							$action['date'] = nkDate($action['date']);

                            echo '<div style="font-size: 12px; margin-bottom:5px;"><em>' . $action['date'] . '</em>&nbsp:&nbsp' . $action['action'] . '</div>';
                        }
                        ?>
                        </p>
                    </div><!-- End #tab3 -->
                </div><!-- End .content-box-content -->
            </div><!-- End .content-box -->
            <div class="clear"></div>
        </div>

        <!-- Start Notifications -->
        <?php
            $sql2 = mysql_query('SELECT id, type, texte  FROM ' . $nuked['prefix'] . '_notification ORDER BY date DESC LIMIT 0, 4');
            while (list($id, $type, $texte) = mysql_fetch_array($sql2))
            {
                if($type == 4)
                {
                ?>

                <div class="notification attention png_bg">
                    <?php if ($visiteur == 9) ?>
                    <a onclick="del('<?php echo $id; ?>');return false" href="#"  class="close"><img src="modules/Admin/images/icons/cross_grey_small.png" title="Close this notification" alt="close" /></a>
                    <div>
                        <?php echo _ALERTENOT; ?>. <?php echo $texte; ?>
                    </div>
                </div>
                <?php
                }
                else if($type == 1)
                {
                ?>
                <div class="notification information png_bg">
                    <?php if ($visiteur == 9) ?>
                    <a onclick="del('<?php echo $id; ?>');return false" href="#" class="close"><img src="modules/Admin/images/icons/cross_grey_small.png" title="Close this notification" alt="close" /></a>
                    <div>
                        <?php echo _INFONOT; ?>. <?php echo $texte; ?>
                    </div>
                </div>
                <?php
                }
                else if($type == 3)
                {
                ?>
                <div class="notification success png_bg">
                    <?php if ($visiteur == 9) ?>
                    <a onclick="del('<?php echo $id; ?>');return false" href="#" class="close"><img src="modules/Admin/images/icons/cross_grey_small.png" title="Close this notification" alt="close" /></a>
                    <div>
                        <?php echo _REUSSITENOT; ?>. <?php echo $texte; ?>
                    </div>
                </div>
                <?php
                }
                else if($type == 2)
                {
                ?>
                <div class="notification error png_bg">
                    <?php if ($visiteur == 9) ?>
                    <a onclick="del('<?php echo $id; ?>');return false" href="#" class="close"><img src="modules/Admin/images/icons/cross_grey_small.png" title="Close this notification" alt="close" /></a>
                    <div>
                        <?php echo _ECHOUENOT; ?>. <?php echo $texte; ?>
                    </div>
                </div>
                <?php
                }
            }

?>