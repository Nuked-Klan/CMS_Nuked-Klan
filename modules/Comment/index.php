<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
defined('INDEX_CHECK') or die;

global $language, $user, $cookie_captcha;
translate("modules/Comment/lang/$language.lang.php");
include_once('Includes/nkCaptcha.php');
if (_NKCAPTCHA == "off") $captcha = 0;
else if ((_NKCAPTCHA == 'auto' OR _NKCAPTCHA == 'on') && $user[1] > 0)  $captcha = 0;
else $captcha = 1;
$visiteur = ($user) ? $user[1] : 0;

function verification($module, $im_id){
    global $nuked;

    if(empty($module)) $module = $_REQUEST['file'];

    if($module == "News" || $module == "news"):
    
        $WhereModule = 'news';
        $sqlverif = 'news';
        $specification = 'id';

    elseif($module == "Download" || $module == "download"):
    
        $WhereModule = 'download';
        $sqlverif = "downloads";
        $specification = "id";

    elseif($module == "Sections" || $module == "sections"):
        
        $WhereModule = 'sections';
        $sqlverif = "sections";
        $specification = "artid";

    elseif($module == "Links" || $module == "links"):
        
        $WhereModule = 'links';
        $sqlverif = "liens";
        $specification = "id";

    elseif($module == "Wars" || $module == "match"):
        
        $WhereModule = 'wars';
        $sqlverif = "match";
        $specification = "warid";

    elseif($module == "Gallery" || $module == "gallery"):
        
        $WhereModule = 'gallery';
        $sqlverif = "gallery";
        $specification = "sid";

    elseif($module == "Survey" || $module == "survey"):
        
        $WhereModule = 'survey';
        $sqlverif = "sondage";
        $specification = "sid";
    
    endif;

    $Sql = mysql_query("SELECT active FROM " . $nuked['prefix'] . "_comment_mod WHERE module = '$WhereModule'");
    list($active) = mysql_fetch_array($Sql);

    $Str = mysql_query("SELECT * FROM " . $nuked['prefix'] . "_$sqlverif WHERE $specification = '" . intval($im_id) . "'");

    return (mysql_num_rows($Str) > 0 && $active == 1);
}

function NbComment($im_id, $module){
    $im_id = mysql_real_escape_string(stripslashes($im_id));
    $module = mysql_real_escape_string(stripslashes($module));
    $Sql = mysql_query("SELECT id FROM ".COMMENT_TABLE." WHERE im_id = '$im_id' AND module = '$module'");
    return mysql_num_rows($Sql);
}

function com_index($module, $im_id){
    global $user, $bgcolor1, $bgcolor2, $bgcolor3, $nuked, $visiteur, $language, $captcha;

    define('EDITOR_CHECK', 1);
    ?>
    <script  type="text/javascript">
    <!--
        function sent(pseudo, module, im_id, code){
            var editor_val = CKEDITOR.instances.e_basic.document.getBody().getChild(0).getText();
            var editor_txt = CKEDITOR.instances.e_basic.getData();
            if(editor_val == ''){
                alert('<?php echo _NOTEXT; ?>');
                return false;
            }
            else if(pseudo == ''){
                alert('<?php echo _NONICK; ?>');
                return false;
            }
            else if(code == ''){
                alert('<?php echo _CAPTCHACOM; ?>');
                return false;
            }
            else{
                var OAjax;
                if (window.XMLHttpRequest) OAjax = new XMLHttpRequest();
                else if (window.ActiveXObject) OAjax = new ActiveXObject('Microsoft.XMLHTTP');
                OAjax.open('POST',"index.php?file=Comment&nuked_nude=index&op=post_comment",true);
                OAjax.onreadystatechange = function(){
                    if (OAjax.readyState == 4 && OAjax.status==200){
                        if (document.getElementById){
                            document.getElementById("message").innerHTML = '<div style="margin:25px 5px;padding:10px 0;text-align:center;border:1px solid #e3e3e3;background:#edfff7;color:#333"><b><?php echo _THXCOM; ?></b></div>';
                            document.location = document.location;
                        }
                    }
                }
                OAjax.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                OAjax.send("texte="+encodeURIComponent(editor_txt)+"&pseudo="+pseudo+"&module="+module+"&im_id="+im_id+"&ajax=1&code_confirm="+code+"");
                return true;
            }
        }
    -->
    </script>
    <?php
    $level_access = nivo_mod("Comment");
    $level_admin = admin_mod("Comment");
    $NbComment = NbComment($im_id, $module);
    
    if(verification($_REQUEST['file'],$im_id)){

        echo '<h3 style="text-align: center">' . _LAST4COMS . '</h3>
        <table style="background:'.$bgcolor3.';margin:5px" width="98%" cellpadding="3" cellspacing="1">
            <tr style="background:'.$bgcolor3.';">
                <td style="width:30%;text-align:center"><b>'._AUTHOR.'</b></td>
                <td style="width:70%;text-align:center"><b>'._COMMENT.'</b></td>
            </tr>';

        $sql = mysql_query("SELECT id, titre, comment, autor, autor_id, date, autor_ip FROM ".COMMENT_TABLE." WHERE im_id = '$im_id' AND module = '$module' ORDER BY id DESC LIMIT 0, 4");
        $count = mysql_num_rows($sql);
        while($row = mysql_fetch_assoc($sql)){
            $test = 0;
            $row['date'] = nkDate($row['date']);
            $row['titre'] = htmlentities($row['titre']);
            $row['titre'] = nk_CSS($row['titre']);
            $row['autor'] = nk_CSS($row['autor']);
            $texte = (!empty($row['titre'])) ? '<b>'.$row['titre'].'</b><br /><br />'.$row['comment'] : $row['comment'];

            if(!empty($row['autor_id'])){
                $sql_member = mysql_query("SELECT pseudo, avatar, country FROM ".USER_TABLE." WHERE id = '{$row['autor_id']}'");
                $test = mysql_num_rows($sql_member);
            }

            if(!empty($row['autor_id']) && $test > 0) list($autor, $avatar, $country) = mysql_fetch_array($sql_member);
            else $autor = $row['autor'];

            if(empty($avatar)) $avatar = "modules/Comment/images/noavatar.png";
            if(empty($country)) $country = "France.gif";

            if($j == 0){$bg = $bgcolor2; $j++;}
            else{$bg = $bgcolor1; $j = 0;}

            if ($visiteur >= $level_admin && $level_admin > -1){

                echo '<script type="text/javascript">function delmess(pseudo, id){if(confirm(\''._DELCOMMENT.' \'+pseudo+\' ! '._CONFIRM.'\')){document.location.href = \'index.php?file=Comment&page=admin&op=del_com&cid=\'+id;}}</script>';

                $admin = '<a href="index.php?file=Comment&amp;page=admin&amp;op=edit_com&amp;cid='.$row['id'].'"><img style="border:none;" src="modules/Forum/images/buttons/'.$language.'/edit.gif" alt="" title="'._EDITTHISCOM.'" /></a>&nbsp;<a href="javascript:delmess(\''.mysql_real_escape_string(stripslashes($autor)).'\', \''.$row['id'].'\');"><img style="border:none;" src="modules/Forum/images/delete.gif" alt="" title="'._DELTHISCOM.'" /></a>';

            }else $admin = '';

            echo '<tr style="background:'.$bg.';">
                    <td style="width:30%;" valign="top"><img src="images/flags/'.$country.'" alt="'.$country.'" />&nbsp;<b>'.$autor.'</b>';

                    if ($visiteur >= $level_admin && $level_admin > -1) echo '<br />Ip : '.$row['autor_ip'];

                    echo '<br /><br /><img src="'.$avatar.'" style="max-width: 100px; max-height: 100px;" alt="" />';

                    $profil = ($test > 0) ? '<a href="index.php?file=Members&amp;op=detail&amp;autor='.urlencode($autor).'"><img style="border:none;" src="modules/Forum/images/buttons/'.$language.'/profile.gif" alt="" /></a>' : '';

            echo '  </td>
                    <td style="width:70%;" valign="top">
                        <img src="images/posticon.gif" alt="" /><small> '._POSTED.' : '.$row['date'].'</small>
                        <br /><br />'.$texte.'<br /><br />
                    </td>
                  </tr>
                  <tr style="background:'.$bg.';">
                      <td style="width:30%;">&nbsp;</td>
                    <td colspan="2">'.$profil.'&nbsp;'.$admin.'<br /></td>
                  </tr>';
            unset($avatar, $autor, $country);
        }

        if ($count == "0") echo '<tr style="background:'.$bgcolor2.';"><td align="center" colspan="2">'._NOCOMMENT.'</td></tr>';

        echo '</table>';

        if ($count >= 0){
            echo '<div style="text-align:center;padding:10px 10px 0 0"><b>'._COMMENTS.' :</b>&nbsp;'.$NbComment.'&nbsp;';

            if ($visiteur >= $level_access && $level_access > -1){
                echo '<br />[ <a href="#" onclick="javascript:window.open(\'index.php?file=Comment&amp;nuked_nude=index&amp;op=view_com&amp;im_id='.$im_id.'&amp;module='.$module.'\',\'popup\',\'toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=600,height=480,top=100,left=100\');return(false)">'._VIEWCOMMENT.'</a> ]';
            }
            echo '</div>';
        }

        $Soumission = 'sent(this.compseudo.value, this.module.value, this.imid.value, this.code.value);return false;';

        echo '<div id="message">
                <form method="post" onsubmit="'.$Soumission.'" action="">
                <table width="100%" cellspacing="5" cellpadding="0" border="0" style="padding-top:15px">';
                if($user) echo '<tr style="display: none"><td colspan="2"><input id="compseudo" type="hidden" name="pseudo" value="'.$user[2].'" /></td></tr>';
                else {
                    echo '<tr>
                        <td style="padding-left:5px;width:30%"><b>'._NICK.' :</b></td>
                        <td><input id="compseudo" type="text" size="30" name="pseudo" maxlength="30" /></td>
                    </tr>';
                }
                    echo '<tr>
                        <td colspan="2" align="center" style="padding-top: 10px"><textarea id="e_basic" name="comtexte" cols="40" rows="3"></textarea></td>
                    </tr>';

                    if ($captcha == 1) create_captcha(2);
                    else echo '<tr><td colspan="2"><input type="hidden" id="code" name="code" value="0" /></td></tr>';

        echo '        <tr>
                        <td colspan="2" align="center">
                            <input type="hidden" id="imid" name="im_id" value="'.$im_id.'" />
                            <input type="hidden" id="module" name="module" value="'.$module.'" />
                            <input type="submit" value="'._SEND_COM.'" />
                        </td>
                    </tr>
                </table>
                </form>
              </div>';
    }
}

function view_com($module, $im_id){
    
    global $user, $bgcolor2, $bgcolor3, $theme, $nuked, $language, $visiteur;

    if(!verification($module,$im_id)) exit();
    if ($language == "french" && strpos("WIN", PHP_OS)) setlocale (LC_TIME, "french");
    else if ($language == "french" && strpos("BSD", PHP_OS)) setlocale (LC_TIME, "fr_FR.ISO8859-1");
    else if ($language == "french") setlocale (LC_TIME, "fr_FR");
    else setlocale (LC_TIME, $language);

    $level_access = nivo_mod("Comment");
    $level_admin = admin_mod("Comment");
    $module = mysql_real_escape_string(stripslashes($module));

    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
          <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
          <head><title>'._COMMENTS.'</title>
          <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
          <meta http-equiv="content-style-type" content="text/css" />
          <link title="style" type="text/css" rel="stylesheet" href="themes/'.$theme.'/style.css" /></head>
          <body style="background:'.$bgcolor2.';">
          <script type="text/javascript">function delmess(autor, id){if (confirm(\''._DELCOMMENT.' \'+autor+\' ! '._CONFIRM.'\')){document.location.href = \'index.php?file=Comment&nuked_nude=index&op=del_comment&cid=\'+id;}}</script>';

    $sql = mysql_query("SELECT id, titre, comment, autor, autor_id, date, autor_ip FROM ".COMMENT_TABLE." WHERE im_id = '$im_id' AND module = '$module' ORDER BY id DESC");
    if (mysql_num_rows($sql) != 0){
        
        while($row = mysql_fetch_assoc($sql)):
            
            $row['date'] = nkDate($row['date']);
            $row['titre'] = htmlentities($row['titre']);
            $row['titre'] = nk_CSS($row['titre']);
            $row['autor'] = nk_CSS($row['autor']);
            
            if(!empty($row['autor_id'])){
                $sql_member = mysql_query("SELECT pseudo FROM ".USER_TABLE." WHERE id ='{$row['autor_id']}'");
                $test = mysql_num_rows($sql_member);
            }

            if(!empty($row['autor_id']) && $test > 0){
                list($author) = mysql_fetch_array($sql_member);
                $autor = '<a href="index.php?file=Members&amp;op=detail&amp;autor='.urlencode($author).'" onclick="window.open(this.href);return false;">'.$author.'</a>';
            }else $autor = $row['autor'];

            echo '<table style="width:90%;margin:0px auto;" cellspacing="0" cellpadding="0"><tr><td style="width:90%;"><b>'.$titre.'</b>';

            if ($visiteur >= $level_admin && $level_admin > -1){
                echo '&nbsp;('.$row['autor_ip'].') <a href="index.php?file=Comment&amp;nuked_nude=index&amp;op=edit_comment&amp;cid='.$row['id'].'"><img style="border:none;" src="images/edit.gif" alt="" title="'._EDITTHISCOM.'" /></a><a href="javascript:delmess(\''.mysql_real_escape_string($row['autor']).'\', \''.$row['id'].'\');"><img style="border:none;" src="images/del.gif" alt="" title="'._DELTHISCOM.'"></a>';
            }

            echo '</td></tr><tr><td><img src="images/posticon.gif" alt="" />&nbsp;'._POSTEDBY.'&nbsp;'.$autor.'&nbsp;'._THE.'&nbsp;'.$row['date'].'<br /><br />'.$row['comment'].'<br /><hr style="height:1px;color:'.$bgcolor3.';" /></td></tr></table>';
        
        endwhile;
    
    }else{
        echo '<div style="text-align:center;"><br /><br />'._NOCOMMENT.'<br /></div>';
    }

    if ($visiteur >= $level_access && $level_access > -1){
        echo '<div style="text-align:center;"><br /><input type="button" value="'._POSTCOMMENT.'" onclick="document.location=\'index.php?file=Comment&amp;nuked_nude=index&amp;op=post_com&amp;im_id='.$im_id.'&amp;module='.$module.'\'" /></div>';
    }

    echo '<div style="text-align:center;"><br />[ <a href="#" onclick="javascript:window.close();"><b>'._CLOSEWINDOW.'</b></a> ]</div></body></html>';
}

function post_com($module, $im_id){
    
    global $user, $nuked, $bgcolor2, $bgcolor4, $language, $theme, $visiteur, $captcha;

    define('EDITOR_CHECK', 1);

    $level_access = nivo_mod("Comment");

    if(!verification($module,$im_id)){}
    elseif($visiteur >= $level_access && $level_access > -1){
    echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
            . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n"
            . "<head><title>" . _POSTCOMMENT . "</title>\n"
            . "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n"
            . "<meta http-equiv=\"content-style-type\" content=\"text/css\" />\n"
            . "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"themes/" . $theme . "/style.css\" /></head>\n"
            . "<body style=\"background : " . $bgcolor2 . ";\">\n";

    echo "<script type=\"text/javascript\">\n"
            ."<!--\n"
            . "\n"
            . "function trim(string)\n"
            . "{"
            . "return string.replace(/(^\s*)|(\s*$)/g,'');"
            . "}\n"
            . "\n"
            . "if (trim(document.getElementById('com_pseudo').value) == \"\")\n"
            . "{\n"
            . "alert('" . _NONICK . "');\n"
            . "return false;\n"
            . "}\n"
            . "return true;\n"
            . "}\n"
            . "\n"
            . "// -->\n"
            . "</script>\n";

    echo "<form method=\"post\" action=\"index.php?file=Comment&nuked_nude=index&op=post_comment\" return verifchamps();\">\n"
            . "<table width=\"100%\" cellspacing=\"1\" cellpadding=\"0\">\n"
            . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" size=\"40\" maxlength=\"40\" /><br /><br /></td></tr>\n"
            . "<tr><td><b>" . _MESSAGE . " :</b><br />"
            . "<textarea id=\"e_basic\" name=\"texte\" cols=\"40\" rows=\"10\"></textarea></td></tr>\n"
            . "<tr><td><b>" . _NICK . " :</b>";

    if ($user){
        echo "&nbsp;&nbsp;<b>" . $user[2] . "</b><input id=\"com_pseudo\" type=\"hidden\" name=\"pseudo\" value=\"" . $user[2] . "\" /></td>\n";
    }
    else{
        echo "<input id=\"com_pseudo\" type=\"text\" size=\"30\" name=\"pseudo\" maxlength=\"30\" /></td>\n";
    }

    echo "</tr>";

    if ($captcha == 1) create_captcha(1);
    else echo "<input type=\"hidden\" id=\"code\" name=\"code\" value=\"0\" />\n";
    
    echo "<tr><td align=\"right\" colspan=\"2\">\n"
            . "<input type=\"hidden\" name=\"im_id\" value=\"" . $im_id . "\" />\n"
            . "<input type=\"hidden\" name=\"noajax\" value=\"true\" />\n"
            . "<input type=\"hidden\" name=\"module\" value=\"" . $module . "\" />\n"
            . "</td></tr></table><div style=\"text-align: center;\"><input type=\"submit\" value=\"" . _SEND . "\" /><br /></div></form>";

    echo '<script type="text/javascript" src="media/ckeditor/ckeditor.js"></script>',"\n"
            , '<script type="text/javascript">',"\n"
            , '//<![CDATA[',"\n";
    echo ConfigSmileyCkeditor().'',"\n";
    echo ' CKEDITOR.replace( \'e_basic\',',"\n"
            , '    {',"\n"
            , '        toolbar : \'Basic\',',"\n"
            , '        language : \'' . substr($language, 0,2) . '\',',"\n";
    if(!empty($bgcolor4)) echo '        uiColor : \'' . $bgcolor4 . '\'',"\n";
    echo '    });',"\n"
            , '//]]>',"\n"
            , '</script>',"\n"
            , '</body></html>',"\n";

    }
    else{
        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
                . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n"
                . "<head><title>" . _POSTCOMMENT . "</title>\n"
                . "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n"
                . "<meta http-equiv=\"content-style-type\" content=\"text/css\" />\n"
                . "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"themes/" . $theme . "/style.css\" /></head>\n"
                . "<body style=\"background : " . $bgcolor2 . ";\">\n"
                . "<div style=\"text-align: center;\"><br /><br /><br />" . _NOENTRANCE . "</div><br /></div></body></html>";
    }
}

function post_comment($im_id, $module, $titre, $texte, $pseudo){
    global $user, $nuked, $bgcolor2, $theme, $user_ip, $visiteur, $captcha;
    
    if(!isset($_REQUEST['noajax'])){
        $titre = utf8_decode($titre);
        $texte = utf8_decode($texte);
        $pseudo = utf8_decode($pseudo);
    }
    $level_access = nivo_mod("Comment");
    if (!verification($module,$im_id)){}
    else if ($visiteur >= $level_access && $level_access > -1){
        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
                . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n"
                . "<head><title>" . _POSTCOMMENT . "</title>\n"
                . "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n"
                . "<meta http-equiv=\"content-style-type\" content=\"text/css\" />\n"
                . "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"themes/" . $theme . "/style.css\" /></head>\n"
                . "<body style=\"background : " . $bgcolor2 . ";\">\n";

        if ($captcha == 1 && !ValidCaptchaCode($_REQUEST['code_confirm'])){
            die ("<div style=\"text-align: center;\"><br /><br />" . _BADCODECONFIRM . "<br /><br /><a href=\"javascript:history.back()\">[ <b>" . _BACK . "</b> ]</a></div>");
        }

        if ($visiteur > 0){
            $autor = $user[2];
            $autor_id = $user[0];
        }
        else{
            $pseudo = htmlentities($pseudo, ENT_QUOTES);
            $pseudo = verif_pseudo($pseudo);
            if ($pseudo == "error1"){
                die ("<div style=\"text-align: center;\"><br /><br />" . _PSEUDOFAILDED . "<br><a href=\"#\" onclick=\"history.back()\">" . _BACK . "</a></div>");
            }
            else if ($pseudo == "error2"){
                die ("<div style=\"text-align: center;\"><br /><br />" . _RESERVNICK . "<br><a href=\"#\" onclick=\"history.back()\">" . _BACK . "</a></div>");
            }
            else if ($pseudo == "error3"){
                die ("<div style=\"text-align: center;\"><br /><br />" . _BANNEDNICK . "<br><a href=\"#\" onclick=\"history.back()\">" . _BACK . "</a></div>");
            }
            else{
                $autor = $pseudo;
                $autor_id="";
            }
        }

        $flood = mysql_query("SELECT date FROM " . COMMENT_TABLE . " WHERE autor = '" . $autor . "' OR autor_ip = '" . $user_ip . "' ORDER BY date DESC LIMIT 0, 1");
        list($flood_date) = mysql_fetch_row($flood);
        $anti_flood = $flood_date + $nuked['post_flood'];

        $date = time();

        if ($date < $anti_flood && $user[1] < admin_mod("Comment")){
            echo "<br /><br /><div style=\"text-align: center;\">" . _NOFLOOD . "</div><br /><br />";
            $url = "index.php?file=Comment&nuked_nude=index&op=view_com&im_id=" . $im_id . "&module=" . $module;
            redirect($url, 2);
            closetable();
            footer();
            exit();
        }
        
        $texte = secu_html(html_entity_decode($texte));
        $titre = mysql_real_escape_string(stripslashes($titre));
        $texte = stripslashes($texte);
        $module = mysql_real_escape_string(stripslashes($module));

        if (strlen($titre) > 40){
             $titre = substr($titre, 0, 40) . "...";
        }

        $add = mysql_query("INSERT INTO " . COMMENT_TABLE . " ( `id` , `module` , `im_id` , `autor` , `autor_id` , `titre` , `comment` , `date` , `autor_ip` ) VALUES ( '' , '" . $module . "' , '" . $im_id . "' , '" . $autor . "' , '" . $autor_id . "' , '" . $titre . "' , '" . mysql_real_escape_string($texte) . "' , '" . $date . "' , '" . $user_ip . "')");
        echo "<div style=\"text-align: center;\"><br /><br /><br /><b>" . _COMMENTADD . "</b>";

        if ($module == "news"){
            echo "<br /><br />[ <a href=\"#\" onclick=\"javascript:window.close();window.opener.document.location.reload(true);\">" . _CLOSEWINDOW . "</a> ]</div></body></html>";
        }
        else{
            echo "</div>";
            $url_redir = "index.php?file=Comment&nuked_nude=index&op=view_com&im_id=" . $im_id . "&module=" . $module;
            if ($_REQUEST['ajax'] != 1){
                redirect($url_redir, 2);
            }
            echo "</body></html>";
        }
    }
    else{
        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
                . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n"
                . "<head><title>" . _POSTCOMMENT . "</title>\n"
                . "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n"
                . "<meta http-equiv=\"content-style-type\" content=\"text/css\" />\n"
                . "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"themes/" . $theme . "/style.css\" /></head>\n"
                . "<body style=\"background : " . $bgcolor2 . ";\">\n"
                . "<div style=\"text-align: center;\"><br /><br /><br />" . _NOENTRANCE . "</div><br /><br /><br />\n"
                . "<a href=\"#\" onclick=\"javascript:window.close()\"><b>" . _CLOSEWINDOW . "</b></a></div></body></html>";
    }
}

function del_comment($cid){
    global $nuked, $user, $theme, $bgcolor2, $nuked_nude, $visiteur;

    $level_admin = admin_mod("Comment");

    if ($visiteur >= $level_admin){
        $sql = mysql_query("SELECT module, im_id FROM " . COMMENT_TABLE . " WHERE id = '" . $cid . "'");
        list($module, $im_id) = mysql_fetch_array($sql);

        $del = mysql_query("DELETE FROM " . COMMENT_TABLE . " WHERE id = '" . $cid . "'");

        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
                . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n"
                . "<head><title>" . _COMMENTS . "</title>\n"
                . "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n"
                . "<meta http-equiv=\"content-style-type\" content=\"text/css\" />\n"
                . "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"themes/" . $theme . "/style.css\" /></head>\n"
                . "<body style=\"background : " . $bgcolor2 . ";\">\n"
                . "<div style=\"text-align: center;\"><br /><br /><br /><b>" . _COMMENTDEL . "</b></div>\n";

        $url_redir = "index.php?file=Comment&nuked_nude=index&op=view_com&im_id=" . $im_id . "&module=" . $module;
        redirect($url_redir, 2);
        echo "</body></html>";
    }
    else{
        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
                . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n"
                . "<head><title>" . _COMMENTS . "</title>\n"
                . "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n"
                . "<meta http-equiv=\"content-style-type\" content=\"text/css\" />\n"
                . "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"themes/" . $theme . "/style.css\" /></head>\n"
                . "<body style=\"background : " . $bgcolor2 . ";\">\n"
                . "<div style=\"text-align: center;\"><br /><br /><br />" . _ZONEADMIN . "</div>\n";

        $url_redir = "index.php?file=Comment&nuked_nude=index&op=view_com&im_id=" . $im_id . "&module=" . $module;
        redirect($url_redir, 5);
        echo "</body></html>";
    }
}

function modif_comment($cid, $titre, $texte, $module, $im_id){
    global $nuked, $user, $theme, $bgcolor2, $visiteur;

    $level_admin = admin_mod("Comment");
    $texte = secu_html(html_entity_decode($texte));
    if(!verification($module,$im_id)){
        exit();
    }
    if ($visiteur >= $level_admin){
        $sql = mysql_query("UPDATE " . COMMENT_TABLE . " SET titre = '" . $titre . "', comment = '" . $texte . "' WHERE id = '" . $cid . "'");

        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
                . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n"
                . "<head><title>" . _COMMENTS . "</title>\n"
                . "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n"
                . "<meta http-equiv=\"content-style-type\" content=\"text/css\" />\n"
                . "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"themes/" . $theme . "/style.css\" /></head>\n"
                . "<body style=\"background : " . $bgcolor2 . ";\">\n"
                . "<div style=\"text-align: center;\"><br /><br /><br /><b>" . _COMMENTMODIF . "</b></div>\n";

    $url_redir = "index.php?file=Comment&nuked_nude=index&op=view_com&im_id=" . $im_id . "&module=" . $module;
        redirect($url_redir, 2);
        echo "</body></html>";
    }
    else{
        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
                . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n"
                . "<head><title>" . _COMMENTS . "</title>\n"
                . "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n"
                . "<meta http-equiv=\"content-style-type\" content=\"text/css\" />\n"
                . "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"themes/" . $theme . "/style.css\" /></head>\n"
                . "<body style=\"background : " . $bgcolor2 . ";\">\n"
                . "<div style=\"text-align: center;\"><br /><br /><br />" . _ZONEADMIN . "</div>\n";

    $url_redir = "index.php?file=Comment&nuked_nude=index&op=view_com&im_id=" . $im_id . "&amp;module=" . $module;
        redirect($url_redir, 5);
        echo "</body></html>";
    }
}

function edit_comment($cid){
    global $user, $nuked, $bgcolor2, $theme, $visiteur;

    define('EDITOR_CHECK', 1);

    $level_admin = admin_mod("Comment");

    if ($visiteur >= $level_admin){
        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
                . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n"
                . "<head><title>" . _POSTCOMMENT . "</title>\n"
                . "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n"
                . "<meta http-equiv=\"content-style-type\" content=\"text/css\" />\n"
                . "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"themes/" . $theme . "/style.css\" /></head>\n"
                . "<body style=\"background : " . $bgcolor2 . ";\">\n";

        $sql = mysql_query("SELECT autor, autor_id, titre, comment, autor_ip, module, im_id FROM " . COMMENT_TABLE . " WHERE id = '" . $cid . "'");
        list($auteur, $autor_id, $titre, $texte, $ip, $module, $im_id) = mysql_fetch_array($sql);

        $titre = htmlentities($titre);

        if($autor_id != ""){
            $sql_member = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '" . $autor_id . "'");
            list($autor) = mysql_fetch_array($sql_member);
        }
        else{
            $autor = $auteur;
        }

        echo "<form method=\"post\" action=\"index.php?file=Comment&amp;nuked_nude=index&amp;op=modif_comment\" >\n"
                . "<table width=\"100%\" cellspacing=\"1\" cellpadding=\"0\">\n"
                . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" size=\"40\" maxlength=\"40\" value=\"" . $titre . "\" /><br /><br /></td></tr>\n"
                . "<tr><td><b>" . _MESSAGE . " :</b><br />\n"
                . "<textarea id=\"e_basic\" name=\"texte\" cols=\"58\" rows=\"10\">" . $texte . "</textarea></td></tr>\n"
                . "<tr><td><b>" . _NICK . " :</b> " . $autor ." ( " . $ip . " )</td></tr>\n"
                . "<tr><td align=\"right\" colspan=\"2\">\n"
                . "<input type=\"hidden\" name=\"cid\" value=\"" . $cid . "\" />\n"
                . "<input type=\"hidden\" name=\"im_id\" value=\"" . $im_id . "\" />\n"
                . "<input type=\"hidden\" name=\"module\" value=\"" . $module . "\" />\n"
                . "</td></tr></table><div style=\"text-align: center;\"><input type=\"submit\" value=\"" . _SEND . "\" /><br /><br />\n"
                . "<a href=\"#\" onclick=\"javascript:window.close()\"><b>" . _CLOSEWINDOW . "</b></a></div></form></body></html>";
    }
    else{
        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
                . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n"
                . "<head><title>" . _COMMENTS . "</title>\n"
                . "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n"
                . "<meta http-equiv=\"content-style-type\" content=\"text/css\" />\n"
                . "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"themes/" . $theme . "/style.css\" /></head>\n"
                . "<body style=\"background : " . $bgcolor2 . ";\">\n"
                . "<div style=\"text-align: center;\"><br /><br /><br />" . _ZONEADMIN . "</div>\n";

        $url_redir = "index.php?file=Comment&nuked_nude=index&op=view_com&im_id=" . $im_id . "&module=" . $module;
        redirect($url_redir, 5);
        echo "</body></html>";
    }
}

switch ($_REQUEST['op']){
    case"del_comment":
        del_comment($_REQUEST['cid']);
        break;

    case"modif_comment":
        modif_comment($_REQUEST['cid'], $_REQUEST['titre'], $_REQUEST['texte'], $_REQUEST['module'], $_REQUEST['im_id']);
        break;

    case "com_index":
        com_index($_REQUEST['im'], $_REQUEST['im_id']);
        break;

    case "post_com":
        post_com($_REQUEST['module'], $_REQUEST['im_id']);
        break;

    case "view_com":
        view_com($_REQUEST['module'], $_REQUEST['im_id']);
        break;

    case "post_comment":
        post_comment($_REQUEST['im_id'], $_REQUEST['module'], $_REQUEST['titre'], $_REQUEST['texte'], $_REQUEST['pseudo']);
        break;

    case "edit_comment":
        edit_comment($_REQUEST['cid']);
        break;

    default:
        break;
}
?>