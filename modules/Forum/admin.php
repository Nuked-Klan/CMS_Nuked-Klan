<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
if (!defined("INDEX_CHECK"))
{
    die ("<div style=\"text-align: center;\">You cannot open this page directly</div>");
}

global $language, $user;
translate("modules/Forum/lang/" . $language . ".lang.php");
include("modules/Admin/design.php");
admintop();

if (!$user)
{
    $visiteur = 0;
}
else
{
    $visiteur = $user[1];
}
$ModName = basename(dirname(__FILE__));
$level_admin = admin_mod($ModName);
if ($visiteur >= $level_admin && $level_admin > -1)
{
    function main_cat()
    {
        global $nuked, $language;

        echo "<script type=\"text/javascript\">\n"
    ."<!--\n"
    ."\n"
    . "function delcat(titre, id)\n"
    . "{\n"
    . "if (confirm('" . _DELETEFORUM . " '+titre+' ! " . _CONFIRM . "'))\n"
    . "{document.location.href = 'index.php?file=Forum&page=admin&op=del_cat&cid='+id;}\n"
    . "}\n"
        . "\n"
    . "// -->\n"
    . "</script>\n";

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _ADMINFORUM . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Forum.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Forum&amp;page=admin\">" . _FORUM . "</a> | "
    . "<a href=\"index.php?file=Forum&amp;page=admin&amp;op=add_forum\">" . _ADDFORUM . "</a> | "
    . "</b>" . _CATMANAGEMENT . "<b><br />"
    . "<a href=\"index.php?file=Forum&amp;page=admin&amp;op=main_rank\">" . _RANKMANAGEMENT . "</a> | "
    . "<a href=\"index.php?file=Forum&amp;page=admin&amp;op=prune\">" . _PRUNE . "</a> | "
    . "<a href=\"index.php?file=Forum&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a></b></div><br />\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"70%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
    . "<tr>\n"
    . "<td style=\"width: 50%;\" align=\"center\"><b>" . _CAT . "</b></td>\n"
    . "<td style=\"width: 10%;\" align=\"center\"><b>" . _ORDER . "</b></td>\n"
    . "<td style=\"width: 20%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
    . "<td style=\"width: 20%;\" align=\"center\"><b>" . _DEL . "</b></td></tr>\n";

        $sql = mysql_query("SELECT id, nom, ordre FROM " . FORUM_CAT_TABLE . " ORDER BY ordre, nom");
        while (list($cid, $nom, $ordre) = mysql_fetch_row($sql))
        {
            $nom = printSecuTags($nom);

           
            echo "<tr>\n"
            . "<td align=\"center\">" . $nom . "</td>\n"
            . "<td align=\"center\">" . $ordre . "</td>\n"
            . "<td align=\"center\"><a href=\"index.php?file=Forum&amp;page=admin&amp;op=edit_cat&amp;cid=" . $cid . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISCAT . "\" /></a></td>\n"
            . "<td align=\"center\"><a href=\"javascript:delcat('" . mysql_real_escape_string(stripslashes($nom)) . "', '" . $cid . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISCAT . "\" /></a></td></tr>\n";
        }

        echo "</table><br /><div style=\"text-align: center;\">[ <a href=\"index.php?file=Forum&amp;page=admin&amp;op=add_cat\"><b>" . _ADDCAT . "</b></a> ]</div>\n"
    . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Forum&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div><br /></div></div>\n";
    }

    function add_cat()
    {
        global $language;

       echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _ADMINFORUM . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Forum.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Forum&amp;page=admin&amp;op=send_cat\">\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">\n"
    . "<tr><td><b>" . _NAME . " :</b> <input type=\"text\" name=\"nom\" size=\"30\" /></td></tr>\n"
    . "<tr><td><b>" . _NIVEAU . " :</b> <select name=\"niveau\">\n"
    . "<option>0</option>\n"
    . "<option>1</option>\n"
    . "<option>2</option>\n"
    . "<option>3</option>\n"
    . "<option>4</option>\n"
    . "<option>5</option>\n"
    . "<option>6</option>\n"
    . "<option>7</option>\n"
    . "<option>8</option>\n"
    . "<option>9</option></select>"
    . "&nbsp;<b>" . _ORDER . " :</b> <input type=\"text\" name=\"ordre\" value=\"0\" size=\"2\" /></td></tr></table>\n"
    . "<div style=\"text-align: center;\"><br /><input type=\"submit\" value=\"" . _CREATECAT . "\" /></div>"
    . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Forum&amp;page=admin&amp;op=main_cat\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    }

    function send_cat($nom, $niveau, $ordre)
    {
        global $nuked, $user;

        $nom = mysql_real_escape_string(stripslashes($nom));

        $sql = mysql_query("INSERT INTO " . FORUM_CAT_TABLE . " ( `id` , `nom` , `ordre` , `niveau` ) VALUES ( '' , '" . $nom . "' , '" . $ordre . "' , '" . $niveau . "' )");
        // Action
        $texteaction = "". _ACTIONADDCATFO .": ".$nom."";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
        . "<div>\n"
        . "" . _CATADD . "\n"
        . "</div>\n"
        . "</div>\n";
        echo "<script>\n"
            ."setTimeout('screen()','3000');\n"
            ."function screen() { \n"
            ."screenon('index.php?file=Forum', 'index.php?file=Forum&page=admin&op=main_cat');\n"
            ."}\n"
            ."</script>\n";
    }

    function del_cat($cid)
    {
        global $nuked, $user;
        
        $sql2 = mysql_query("SELECT nom FROM " . FORUM_CAT_TABLE . " WHERE id = '" . $cid . "'");
        list($nom) = mysql_fetch_array($sql2);
        $nom = mysql_real_escape_string($nom);
        $sql = mysql_query("DELETE FROM " . FORUM_CAT_TABLE . " WHERE id = '" . $cid . "'");
        // Action
        $texteaction = "". _ACTIONDELCATFO .": ".$nom."";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
        . "<div>\n"
        . "" . _CATDEL . "\n"
        . "</div>\n"
        . "</div>\n";
        echo "<script>\n"
            ."setTimeout('screen()','3000');\n"
            ."function screen() { \n"
            ."screenon('index.php?file=Forum', 'index.php?file=Forum&page=admin&op=main_cat');\n"
            ."}\n"
            ."</script>\n";
    }

    function edit_cat($cid)
    {
        global $nuked, $language;

        $sql = mysql_query("SELECT nom, niveau, ordre FROM " . FORUM_CAT_TABLE . " WHERE id = '" . $cid . "'");
        list($nom, $niveau, $ordre) = mysql_fetch_array($sql);

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _ADMINFORUM . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Forum.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Forum&amp;page=admin&amp;op=modif_cat\">\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">\n"
    . "<tr><td><b>" . _NAME . " :</b> <input type=\"text\" name=\"nom\" size=\"30\" value=\"" . $nom . "\" /></td></tr>\n"
    . "<tr><td><b>" . _NIVEAU . " :</b> <select name=\"niveau\"><option>" . $niveau . "</option>\n"
    . "<option>0</option>\n"
    . "<option>1</option>\n"
    . "<option>2</option>\n"
    . "<option>3</option>\n"
    . "<option>4</option>\n"
    . "<option>5</option>\n"
    . "<option>6</option>\n"
    . "<option>7</option>\n"
    . "<option>8</option>\n"
    . "<option>9</option></select>"
    . "&nbsp;<b>" . _ORDER . " :</b> <input type=\"text\" name=\"ordre\" value=\"" . $ordre . "\" size=\"2\" /></td></tr></table>\n"
    . "<div style=\"text-align: center;\"><br /><input type=\"hidden\" name=\"cid\" value=\"" . $cid . "\" /><input type=\"submit\" value=\"" . _MODIFTHISCAT . "\" /></div>"
    . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Forum&amp;page=admin&amp;op=main_cat\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>";
    }

    function modif_cat($cid, $nom, $niveau, $ordre)
    {
        global $nuked, $user;

        $nom = mysql_real_escape_string(stripslashes($nom));

        $sql = mysql_query("UPDATE " . FORUM_CAT_TABLE . " SET nom = '" . $nom . "', niveau = '" . $niveau . "', ordre = '" . $ordre . "' WHERE id = '" . $cid . "'");
        $sql_forum = mysql_query("UPDATE " . FORUM_TABLE . " SET niveau = '" . $niveau . "' WHERE cat = '" . $cid . "'");
        // Action
        $texteaction = "". _ACTIONMODIFCATFO .": ".$nom."";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
        . "<div>\n"
        . "" . _CATMODIF . "\n"
        . "</div>\n"
        . "</div>\n";
        echo "<script>\n"
            ."setTimeout('screen()','3000');\n"
            ."function screen() { \n"
            ."screenon('index.php?file=Forum', 'index.php?file=Forum&page=admin&op=main_cat');\n"
            ."}\n"
            ."</script>\n";
    }

    function select_forum_cat()
    {
        global $nuked;

        $sql = mysql_query("SELECT id, nom FROM " . FORUM_CAT_TABLE . " ORDER BY ordre, nom");
        while (list($cid, $nom) = mysql_fetch_row($sql))
        {
            $nom = printSecuTags($nom);

            echo "<option value=\"" . $cid . "\">" . $nom . "</option>\n";
        }
    }

    function add_forum()
    {
        global $language;

       echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _ADMINFORUM . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Forum.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Forum&amp;page=admin\">" . _FORUM . "</a> | "
    . "</b>" . _ADDFORUM . "<b> | "
    . "<a href=\"index.php?file=Forum&amp;page=admin&amp;op=main_cat\">" . _CATMANAGEMENT . "</a><br />"
    . "<a href=\"index.php?file=Forum&amp;page=admin&amp;op=main_rank\">" . _RANKMANAGEMENT . "</a> | "
    . "<a href=\"index.php?file=Forum&amp;page=admin&amp;op=prune\">" . _PRUNE . "</a> | "
    . "<a href=\"index.php?file=Forum&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a></b></div><br />\n"
    . "<form method=\"post\" action=\"index.php?file=Forum&amp;page=admin&amp;op=send_forum\">\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">\n"
    . "<tr><td><b>" . _NAME . " :</b> <input type=\"text\" name=\"titre\" size=\"30\" /></td></tr>\n"
    . "<tr><td><b>" . _CAT . " :</b> <select name=\"cat\">\n";

        select_forum_cat();

        echo"</select></td></tr>\n"
    . "<tr><td align=\"left\"><b>" . _DESCR . " : </b><br /><textarea class=\"editor\" name=\"description\" rows=\"10\" cols=\"69\"></textarea></td></tr>\n"
    . "<tr><td><b>" . _LEVELACCES . " :</b> <select name=\"niveau\">\n"
    . "<option>0</option>\n"
    . "<option>1</option>\n"
    . "<option>2</option>\n"
    . "<option>3</option>\n"
    . "<option>4</option>\n"
    . "<option>5</option>\n"
    . "<option>6</option>\n"
    . "<option>7</option>\n"
    . "<option>8</option>\n"
    . "<option>9</option></select>&nbsp;<b>" . _LEVELPOST . " :</b> <select name=\"level\">\n"
    . "<option>0</option>\n"
    . "<option>1</option>\n"
    . "<option>2</option>\n"
    . "<option>3</option>\n"
    . "<option>4</option>\n"
    . "<option>5</option>\n"
    . "<option>6</option>\n"
    . "<option>7</option>\n"
    . "<option>8</option>\n"
    . "<option>9</option></select>&nbsp;<b>" . _ORDER . " :</b> <input type=\"text\" name=\"ordre\" size=\"2\" value=\"0\" /></td></tr>\n"
    . "<tr><td><b>" . _LEVELPOLL . " :</b> <select name=\"level_poll\">\n"
    . "<option>0</option>\n"
    . "<option>1</option>\n"
    . "<option>2</option>\n"
    . "<option>3</option>\n"
    . "<option>4</option>\n"
    . "<option>5</option>\n"
    . "<option>6</option>\n"
    . "<option>7</option>\n"
    . "<option>8</option>\n"
    . "<option>9</option></select>&nbsp;<b>" . _LEVELVOTE . " :</b> <select name=\"level_vote\">\n"
    . "<option>0</option>\n"
    . "<option>1</option>\n"
    . "<option>2</option>\n"
    . "<option>3</option>\n"
    . "<option>4</option>\n"
    . "<option>5</option>\n"
    . "<option>6</option>\n"
    . "<option>7</option>\n"
    . "<option>8</option>\n"
    . "<option>9</option></select></td></tr>\n"
    . "<tr><td><b>" . _MODERATEUR . " :</b> <select name=\"modo\"><option value=\"\">" . _NONE . "</option>\n";


        $sql = mysql_query("SELECT id, pseudo FROM " . USER_TABLE . " WHERE niveau > 0 ORDER BY niveau DESC, pseudo");
        while (list($id_user, $pseudo) = mysql_fetch_row($sql))
        {

            echo "<option value=\"" . $id_user . "\">" . $pseudo . "</option>\n";
        }


    echo "</select></td></tr></table>\n"
    . "<div style=\"text-align: center;\"><br /><input type=\"submit\" value=\"" . _ADDTHISFORUM . "\" /></div>\n"
    . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Forum&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div>\n";
    }

    function send_forum($titre, $description, $cat, $modo, $niveau, $level, $ordre, $level_poll, $level_vote)
    {
        global $nuked, $user;

        $description = html_entity_decode($description);
        $titre = mysql_real_escape_string(stripslashes($titre));
        $description = mysql_real_escape_string(stripslashes($description));

        $sql = mysql_query("INSERT INTO " . FORUM_TABLE . " ( `id` , `cat` , `nom` , `comment` , `moderateurs` , `niveau` , `level` , `ordre` , `level_poll` , `level_vote` ) VALUES ( '' , '" . $cat . "' , '" . $titre . "' , '" . $description . "' , '" . $modo . "' , '" . $niveau . "' , '" . $level . "' , '" . $ordre . "' , '" . $level_poll . "' , '" . $level_vote . "' )");
        // Action
        $texteaction = "". _ACTIONADDFO .": ".$titre."";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
        . "<div>\n"
        . "" . _FORUMADD . "\n"
        . "</div>\n"
        . "</div>\n";
        echo "<script>\n"
            ."setTimeout('screen()','3000');\n"
            ."function screen() { \n"
            ."screenon('index.php?file=Forum', 'index.php?file=Forum&page=admin&op=main');\n"
            ."}\n"
            ."</script>\n";
    }

    function del_forum($id)
    {
        global $nuked, $user;

        $sqls = mysql_query("SELECT nom FROM " . FORUM_TABLE . " WHERE id = '" . $id . "'");
        list($titre) = mysql_fetch_array($sqls);
        $titre= mysql_real_escape_string($titre);
        $sql = mysql_query("SELECT id, sondage FROM " . FORUM_THREADS_TABLE . " WHERE forum_id = '" . $id . "'");
        while (list($thread_id, $sondage) = mysql_fetch_row($sql))
        {
            if ($sondage == 1)
            {
                $sql_poll = mysql_query("SELECT id FROM " . FORUM_POLL_TABLE . " WHERE thread_id = '" . $thread_id . "'");
                list($poll_id) = mysql_fetch_row($sql_poll);

                $sup1 = mysql_query("DELETE FROM " . FORUM_POLL_TABLE . " WHERE id = '" . $poll_id . "'");
                $sup2 = mysql_query("DELETE FROM " . FORUM_OPTIONS_TABLE . " WHERE poll_id = '" . $poll_id . "'");
                $sup3 = mysql_query("DELETE FROM " . FORUM_VOTE_TABLE . " WHERE poll_id = '" . $poll_id . "'");
            }
        }

        mysql_query("DELETE FROM " . FORUM_TABLE . " WHERE id = '" . $id . "'");
        mysql_query("DELETE FROM " . FORUM_THREADS_TABLE . " WHERE forum_id = '" . $id . "'");
        mysql_query("DELETE FROM " . FORUM_MESSAGES_TABLE . " WHERE forum_id = '" . $id . "'");
        // Action
        $texteaction = "". _ACTIONDELFO .": ".$titre."";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
        . "<div>\n"
        . "" . _FORUMDEL . "\n"
        . "</div>\n"
        . "</div>\n";
        echo "<script>\n"
            ."setTimeout('screen()','3000');\n"
            ."function screen() { \n"
            ."screenon('index.php?file=Forum', 'index.php?file=Forum&page=admin&op=main');\n"
            ."}\n"
            ."</script>\n";
    }

    function edit_forum($id)
    {
        global $nuked, $language;

        $sql = mysql_query("SELECT nom, comment, cat, moderateurs, niveau, level, ordre, level_poll, level_vote FROM " . FORUM_TABLE . " WHERE id = '" . $id . "'");
        list($titre, $description, $cat, $modo, $niveau, $level, $ordre, $level_poll, $level_vote) = mysql_fetch_array($sql);

        $categorie = mysql_query("select nom FROM " . FORUM_CAT_TABLE . " WHERE id = '" . $cat . "'");
        list($cat_name) = mysql_fetch_array($categorie);
        $cat_name = printSecuTags($cat_name);

        if ($modo != "")
        {
            $moderateurs = explode('|', $modo);
            for ($i = 0;$i < count($moderateurs);$i++)
            {
                if ($i > 0) $sep = ', ';
                $sql2 = mysql_query("SELECT id, pseudo FROM " . USER_TABLE . " WHERE id = '" . $moderateurs[$i] . "'");
                list($id_user, $modo_pseudo) = mysql_fetch_row($sql2);
                $modos .= $sep . $modo_pseudo . "&nbsp;(<a href=\"index.php?file=Forum&amp;page=admin&amp;op=del_modo&amp;uid=" . $id_user . "&amp;forum_id=" . $id . "\"><img width=\"7\" style=\"border: 0;\" src=\"modules/Forum/images/del.gif\" alt=\"\" title=\"" . _DELTHISMODO . "\" /></a>)";
            }
        }
        else
        {
            $modos = _NONE;
        }

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _ADMINFORUM . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Forum.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Forum&amp;page=admin&amp;op=modif_forum\">\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">\n"
    . "<tr><td><b>" . _NAME . " :</b> <input type=\"text\" name=\"titre\" size=\"30\" value=\"" . $titre . "\" /></td></tr>\n"
    . "<tr><td><b>" . _CAT . " :</b> <select name=\"cat\"><option value=\"" . $cat . "\">" . $cat_name . "</option>\n";

        select_forum_cat();

        echo"</select></td></tr>\n"
    . "<tr><td align=\"left\"><b>" . _DESCR . " : </b><br /><textarea class=\"editor\" name=\"description\" rows=\"10\" cols=\"69\">" . $description . "</textarea></td></tr>\n"
    . "<tr><td><b>" . _LEVELACCES . " :</b> <select name=\"niveau\"><option>" . $niveau . "</option>\n"
    . "<option>0</option>\n"
    . "<option>1</option>\n"
    . "<option>2</option>\n"
    . "<option>3</option>\n"
    . "<option>4</option>\n"
    . "<option>5</option>\n"
    . "<option>6</option>\n"
    . "<option>7</option>\n"
    . "<option>8</option>\n"
    . "<option>9</option></select>&nbsp;<b>" . _LEVELPOST . " :</b> <select name=\"level\"><option>" . $level . "</option>\n"
    . "<option>0</option>\n"
    . "<option>1</option>\n"
    . "<option>2</option>\n"
    . "<option>3</option>\n"
    . "<option>4</option>\n"
    . "<option>5</option>\n"
    . "<option>6</option>\n"
    . "<option>7</option>\n"
    . "<option>8</option>\n"
    . "<option>9</option></select>&nbsp;<b>" . _ORDER . " :</b> <input type=\"text\" name=\"ordre\" size=\"2\" value=\"" . $ordre . "\" /></td></tr>\n"
    . "<tr><td><b>" . _LEVELPOLL . " :</b> <select name=\"level_poll\"><option>" . $level_poll . "</option>\n"
    . "<option>0</option>\n"
    . "<option>1</option>\n"
    . "<option>2</option>\n"
    . "<option>3</option>\n"
    . "<option>4</option>\n"
    . "<option>5</option>\n"
    . "<option>6</option>\n"
    . "<option>7</option>\n"
    . "<option>8</option>\n"
    . "<option>9</option></select>&nbsp;<b>" . _LEVELVOTE . " :</b> <select name=\"level_vote\"><option>$level_vote</option>\n"
    . "<option>0</option>\n"
    . "<option>1</option>\n"
    . "<option>2</option>\n"
    . "<option>3</option>\n"
    . "<option>4</option>\n"
    . "<option>5</option>\n"
    . "<option>6</option>\n"
    . "<option>7</option>\n"
    . "<option>8</option>\n"
    . "<option>9</option></select></td></tr>\n"
    . "<tr><td><b>" . _MODO . " :</b> " . $modos . "</td></tr>\n"
    . "<tr><td><b>" . _ADDMODO . " :</b> <select name=\"modo\"><option value=\"\">" . _NONE . "</option>\n";

        $sql = mysql_query("SELECT id, pseudo FROM " . USER_TABLE . " WHERE niveau > 0 ORDER BY niveau DESC, pseudo");
        while (list($id_user, $pseudo) = mysql_fetch_row($sql))
        {
            if (!is_int(strpos($modos, $id_user)))
            {
                echo "<option value=\"" . $id_user . "\">" . $pseudo . "</option>\n";
            }
        }

    echo "</select><input type=\"hidden\" name=\"id\" value=\"" . $id . "\" /></td></tr></table>\n"
    . "<div style=\"text-align: center;\"><br /><input type=\"submit\" value=\"" . _MODIFTHISFORUM . "\" /></div>\n"
    . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Forum&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    }

    function modif_forum($id, $titre, $cat, $description, $niveau, $level, $ordre, $level_poll, $level_vote, $modo)
    {
        global $nuked, $user;

        $description = html_entity_decode($description);
        $titre = mysql_real_escape_string(stripslashes($titre));
        $description = mysql_real_escape_string(stripslashes($description));

        if ($modo != "")
        {
            $sql = mysql_query("SELECT moderateurs FROM " . FORUM_TABLE . " WHERE id = '" . $id . "'");
            list($listmodo) = mysql_fetch_row($sql);

            if ($listmodo != "") $modos = $listmodo . "|" . $modo;
            else $modos = $modo;

            $upd_modo = mysql_query("UPDATE " . FORUM_TABLE . " SET moderateurs = '" . $modos . "' WHERE id = '" . $id . "'");
        }

        $upd = mysql_query("UPDATE " . FORUM_TABLE . " SET nom = '" . $titre . "', comment = '" . $description . "', cat = '" . $cat . "', niveau = '" . $niveau . "', level = '" . $level . "', ordre = '" . $ordre . "', level_poll = '" . $level_poll . "', level_vote = '" . $level_vote . "' WHERE id = '" . $id . "'");
        // Action
        $texteaction = "". _ACTIONMODIFFO .": ".$titre."";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
        . "<div>\n"
        . "" . _FORUMMODIF . "\n"
        . "</div>\n"
        . "</div>\n";
        echo "<script>\n"
            ."setTimeout('screen()','3000');\n"
            ."function screen() { \n"
            ."screenon('index.php?file=Forum', 'index.php?file=Forum&page=admin');\n"
            ."}\n"
            ."</script>\n";
    }

    function del_modo($uid, $forum_id)
    {
        global $nuked, $user;
        
        $sql = mysql_query("SELECT moderateurs FROM " . FORUM_TABLE . " WHERE id = '" . $forum_id . "'");
        list($listmodo) = mysql_fetch_row($sql);
        $list = explode("|", $listmodo);
        for($i = 0; $i <= count($list)-1;$i++)
        {
            if ($i == 0 || ($i == 1 && $list[0] == $uid))
            {
                $sep = "";
            }
            else
            {
                $sep = "|";
            }

            if ($list[$i] != $uid)
            {
                $modos .= $sep . $list[$i];
            }
        }

        $upd = mysql_query("UPDATE " . FORUM_TABLE . " SET moderateurs = '" . $modos . "' WHERE id = '" . $forum_id . "'");
        
        $sql = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '".$uid."'");
        list($pseudo) = mysql_fetch_array($sql);
        $pseudo = mysql_real_escape_string($pseudo);
        // Action
        $texteaction = "". _ACTIONDELMODOFO .": ".$pseudo."";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
        . "<div>\n"
        . "" . _MODODEL . "\n"
        . "</div>\n"
        . "</div>\n";
        
        $url = "index.php?file=Forum&page=admin&op=edit_forum&id=" . $forum_id;
        redirect($url, 2);
    }

    function main()
    {
        global $nuked, $language;

        echo "<script type=\"text/javascript\">\n"
    ."<!--\n"
    ."\n"
    . "function delforum(nom, id)\n"
    . "{\n"
    . "if (confirm('" . _DELETEFORUM . " '+nom+' ! " . _CONFIRM . "'))\n"
    . "{document.location.href = 'index.php?file=Forum&page=admin&op=del_forum&id='+id;}\n"
    . "}\n"
        . "\n"
    . "// -->\n"
    . "</script>\n";

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _ADMINFORUM . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Forum.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\">" . _FORUM . "<b> | "
    . "<a href=\"index.php?file=Forum&amp;page=admin&amp;op=add_forum\">" . _ADDFORUM . "</a> | "
    . "<a href=\"index.php?file=Forum&amp;page=admin&amp;op=main_cat\">" . _CATMANAGEMENT . "</a><br />"
    . "<a href=\"index.php?file=Forum&amp;page=admin&amp;op=main_rank\">" . _RANKMANAGEMENT . "</a> | "
    . "<a href=\"index.php?file=Forum&amp;page=admin&amp;op=prune\">" . _PRUNE . "</a> | "
    . "<a href=\"index.php?file=Forum&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a></b></div><br />\n"
    . "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
    . "<tr>\n"
    . "<td style=\"width: 20%;\" align=\"center\"><b>" . _NAME . "</b></td>\n"
    . "<td style=\"width: 20%;\" align=\"center\"><b>" . _CAT . "</b></td>\n"
    . "<td style=\"width: 20%;\" align=\"center\"><b>" . _LEVELACCES . "</b></td>\n"
    . "<td style=\"width: 20%;\" align=\"center\"><b>" . _LEVELPOST . "</b></td>\n"
    . "<td style=\"width: 10%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
    . "<td style=\"width: 10%;\" align=\"center\"><b>" . _DEL . "</b></td></tr>\n";

        $sql = mysql_query("SELECT A.id, A.nom, A.niveau, A.level, A.cat, B.nom FROM " . FORUM_TABLE . " AS A LEFT JOIN " . FORUM_CAT_TABLE . " AS B ON B.id = A.cat ORDER BY B.ordre, B.nom, A.ordre, A.nom");
        while (list($id, $titre, $niveau, $level, $cat, $cat_name) = mysql_fetch_row($sql))
        {

            $titre = printSecuTags($titre);
            $cat_name = printSecuTags($cat_name);

            echo "<tr>\n"
            . "<td style=\"width: 20%;\">" . $titre . "</td>\n"
            . "<td style=\"width: 20%;\" align=\"center\">" . $cat_name . "</td>\n"
            . "<td style=\"width: 20%;\" align=\"center\">" . $niveau . "</td>\n"
            . "<td style=\"width: 20%;\" align=\"center\">" . $level . "</td>\n"
            . "<td style=\"width: 10%;\" align=\"center\"><a href=\"index.php?file=Forum&amp;page=admin&amp;op=edit_forum&amp;id=" . $id . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISFORUM . "\" /></a></td>\n"
            . "<td style=\"width: 10%;\" align=\"center\"><a href=\"javascript:delforum('" . mysql_real_escape_string(stripslashes($titre)) . "', '" . $id . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISFORUM . "\" /></a></td></tr>\n";
        }
        echo "</table><div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Admin\"><b>" . _BACK . "</b></a> ]</div><br /></div></div>\n";
    }

    function main_rank()
    {
        global $nuked, $language;

        echo "<script type=\"text/javascript\">\n"
    ."<!--\n"
    ."\n"
    . "function delrank(titre, id)\n"
    . "{\n"
    . "if (confirm('" . _DELETEFORUM . " '+titre+' ! " . _CONFIRM . "'))\n"
    . "{document.location.href = 'index.php?file=Forum&page=admin&op=del_rank&rid='+id;}\n"
    . "}\n"
        . "\n"
    . "// -->\n"
    . "</script>\n";

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _ADMINFORUM . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Forum.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Forum&amp;page=admin\">" . _FORUM . "</a> | "
    . "<a href=\"index.php?file=Forum&amp;page=admin&amp;op=add_forum\">" . _ADDFORUM . "</a> | "
    . "<a href=\"index.php?file=Forum&amp;page=admin&amp;op=main_cat\">" . _CATMANAGEMENT . "</a><br />"
    . "</b>" . _RANKMANAGEMENT . "<b> | "
    . "<a href=\"index.php?file=Forum&amp;page=admin&amp;op=prune\">" . _PRUNE . "</a> | "
    . "<a href=\"index.php?file=Forum&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a></b></div><br />\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"80%\" cellpadding=\"2\" cellspacing=\"1\">\n"
    . "<tr>\n"
    . "<td style=\"width: 25%;\" align=\"center\"><b>" . _NAME . "</b></td>\n"
    . "<td style=\"width: 25%;\"align=\"center\"><b>" . _TYPE . "</b></td>\n"
    . "<td style=\"width: 20%;\" align=\"center\"><b>" . _MESSAGES . "</b></td>\n"
    . "<td style=\"width: 15%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
    . "<td style=\"width: 15%;\" align=\"center\"><b>" . _DEL . "</b></td></tr>\n";

        $sql = mysql_query("SELECT id, nom, type, post FROM " . FORUM_RANK_TABLE . " ORDER by type DESC, post");
        while (list($rid, $nom, $type, $nbpost) = mysql_fetch_row($sql))
        {
            $nom = printSecuTags($nom);

            if ($type == 1)
            {
                $name = "<b>" . $nom . "</b>";
                $type_name = _MODERATEUR;
                $nb_post = "-";
                $del = "-";
            }
            else if ($type == 2)
            {
                $name = "<b>" . $nom . "</b>";
                $type_name = _ADMINISTRATOR;
                $nb_post = "-";
                $del = "-";
            }
            else
            {
                $name = $nom;
                $type_name = _MEMBER;
                $nb_post = $nbpost;
                $del = "<a href=\"javascript:delrank('" . mysql_real_escape_string(stripslashes($nom)) . "', '" . $rid . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISRANK . "\" /></a>";
            }

            echo "<tr>\n"
            . "<td style=\"width: 25%;\" align=\"center\">" . $name . "</td>\n"
            . "<td style=\"width: 25%;\" align=\"center\">" . $type_name . "</td>\n"
            . "<td style=\"width: 20%;\" align=\"center\">" . $nb_post . "</td>\n"
            . "<td style=\"width: 15%;\" align=\"center\"><a href=\"index.php?file=Forum&amp;page=admin&amp;op=edit_rank&amp;rid=" . $rid . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISRANK . "\" /></a></td>\n"
            . "<td style=\"width: 15%;\" align=\"center\">" . $del . "</td></tr>\n";
        }

        echo "</table><br /><div style=\"text-align: center;\">[ <a href=\"index.php?file=Forum&amp;page=admin&amp;op=add_rank\"><b>" . _ADDRANK . "</b></a> ]</div>\n"
    . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Forum&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div><br /></div></div>\n";
    }

    function add_rank()
    {
        global $language;

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _ADMINFORUM . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Forum.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Forum&amp;page=admin&amp;op=send_rank\">\n"
    . "<table  style=\"margin-left: auto;margin-right: auto;text-align: left;\"  border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\n"
    . "<tr><td><b>" . _NAME . " : </b> <input type=\"text\" name=\"nom\" size=\"30\" /></td></tr>\n"
    . "<tr><td><b>" . _IMAGE . " :</b> <input type=\"text\" name=\"image\" value=\"http://\" size=\"38\" maxlength=\"200\" /></td></tr>\n"
    . "<tr><td><b>" . _MESSAGES . " :</b> <input type=\"text\" name=\"post\" size=\"4\" value=\"0\" maxlength=\"5\" /></td></tr>\n"
    . "<tr><td>&nbsp;<input type=\"hidden\" name=\"type\" value=\"0\" /></td></tr></table>\n"
    . "<div style=\"text-align: center;\"><input type=\"submit\" value=\"" . _CREATERANK . "\" /></div>\n"
    . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Forum&amp;page=admin&amp;op=main_rank\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    }

    function send_rank($nom, $type, $post, $image)
    {
        global $nuked, $user;

        $nom = mysql_real_escape_string(stripslashes($nom));

        $sql = mysql_query("INSERT INTO " . FORUM_RANK_TABLE . " ( `id` , `nom` , `type` , `post` , `image` ) VALUES ( '' , '" . $nom . "' , '" . $type . "' , '" . $post . "' , '" . $image . "' )");
        // Action
        $texteaction = "". _ACTIONADDRANKFO .": ".$nom."";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
        . "<div>\n"
        . "" . _RANKADD . "\n"
        . "</div>\n"
        . "</div>\n";
        redirect("index.php?file=Forum&page=admin&op=main_rank", 2);
    }

    function del_rank($rid)
    {
        global $nuked, $user;

        $sqlr = mysql_query("SELECT nom FROM " . FORUM_RANK_TABLE . " WHERE id = '" . $rid . "'");
        list($nom) = mysql_fetch_array($sqlr);
        $nom = mysql_real_escape_string($nom);
        $sql = mysql_query("DELETE FROM " . FORUM_RANK_TABLE . " WHERE id = '" . $rid . "'");
        // Action
        $texteaction = "". _ACTIONDELRANKFO .": ".$nom."";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
        . "<div>\n"
        . "" . _RANKDEL . "\n"
        . "</div>\n"
        . "</div>\n";
        redirect("index.php?file=Forum&page=admin&op=main_rank", 2);
    }

    function edit_rank($rid)
    {
        global $language, $nuked;

        $sql = mysql_query("SELECT nom, type, post, image FROM " . FORUM_RANK_TABLE . " WHERE id = '" . $rid . "'");
        list($nom, $type, $post, $image) = mysql_fetch_array($sql);

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _ADMINFORUM . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Forum.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Forum&amp;page=admin&amp;op=modif_rank\">\n"
    . "<table  style=\"margin-left: auto;margin-right: auto;text-align: left;\"  border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\n"
    . "<tr><td><b>" . _NAME . " : </b> <input type=\"text\" name=\"nom\" size=\"30\" value=\"" . $nom . "\" /></td></tr>\n"
    . "<tr><td><b>" . _IMAGE . " :</b> <input type=\"text\" name=\"image\" value=\"" . $image . "\" size=\"38\" maxlength=\"200\" />";

        if ($type == 0)
        {
            echo "</td></tr><tr><td><b>" . _MESSAGES . " :</b> <input type=\"text\" name=\"post\" size=\"4\" value=\"" . $post . "\" maxlength=\"5\" /></td></tr>\n";
    }
    else
    {
            echo "<input type=\"hidden\" name=\"post\" value=\"" . $post . "\" /></td></tr>\n";
    }

    echo "<tr><td>&nbsp;<input type=\"hidden\" name=\"type\" value=\"" . $type . "\" /><input type=\"hidden\" name=\"rid\" value=\"" . $rid . "\" /></td></tr></table>\n"
    . "<div style=\"text-align: center;\"><input type=\"submit\" value=\"" . _MODIFTHISRANK . "\" /></div>\n"
    . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Forum&amp;page=admin&amp;op=main_rank\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    }

    function modif_rank($rid, $nom, $type, $post, $image)
    {
        global $nuked, $user;

        $nom = mysql_real_escape_string(stripslashes($nom));

        $sql = mysql_query("UPDATE " . FORUM_RANK_TABLE . " SET nom = '" . $nom . "', type = '" . $type . "', post = '" . $post . "', image = '" . $image . "' WHERE id = '" . $rid . "'");
        
        // Action
        $texteaction = "". _ACTIONMODIFRANKFO .": ".$nom."";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
        . "<div>\n"
        . "" . _RANKMODIF . "\n"
        . "</div>\n"
        . "</div>\n";
        redirect("index.php?file=Forum&page=admin&op=main_rank", 2);
    }

    function prune()
    {
        global $nuked, $language;

        echo "<script type=\"text/javascript\">\n"
    ."<!--\n"
    ."\n"
    . "function verifchamps()\n"
    . "{\n"
    . "if (document.getElementById('prune_day').value.length == 0)\n"
    . "{\n"
    . "alert('" . _NODAY . "');\n"
    . "return false;\n"
    . "}\n"
    . "return true;\n"
    . "}\n"
        . "\n"
    . "// -->\n"
    . "</script>\n";

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _ADMINFORUM . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Forum.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Forum&amp;page=admin\">" . _FORUM . "</a> | "
    . "<a href=\"index.php?file=Forum&amp;page=admin&amp;op=add_forum\">" . _ADDFORUM . "</a> | "
    . "<a href=\"index.php?file=Forum&amp;page=admin&amp;op=main_cat\">" . _CATMANAGEMENT . "</a><br />"
    . "<a href=\"index.php?file=Forum&amp;page=admin&amp;op=main_rank\">" . _RANKMANAGEMENT . "</a> | "
    . "</b>" . _PRUNE . "<b> | "
    . "<a href=\"index.php?file=Forum&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a></b></div><br />\n"
    . "<form method=\"post\" action=\"index.php?file=Forum&amp;page=admin&amp;op=do_prune\" onsubmit=\"return verifchamps();\">\n"
    . "<table  style=\"margin-left: auto;margin-right: auto;text-align: left;\"  border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
    . "<tr><td>" . _DELOLDMESSAGES . "</td></tr>\n"
    . "<tr><td><b>" . _NUMBEROFDAY . " :</b> <input id=\"prune_day\" type=\"text\" name=\"day\" size=\"3\" maxlength=\"3\" /></td></tr>\n"
    . "<tr><td><b>" . _FORUM . " :</b> <select name=\"forum_id\"><option value=\"\">" . _ALL . "</option>\n";

        $sql_cat = mysql_query("SELECT id, nom FROM " . FORUM_CAT_TABLE . " ORDER BY ordre, nom");
        while (list($cat, $cat_name) = mysql_fetch_row($sql_cat))
        {
            $cat_name = printSecuTags($cat_name);

            echo "<option value=\"cat_" . $cat . "\">* " . $cat_name . "</option>\n";

            $sql_forum = mysql_query("SELECT nom, id FROM " . FORUM_TABLE . " WHERE cat = '" . $cat . "' ORDER BY ordre, nom");
            while (list($forum_name, $fid) = mysql_fetch_row($sql_forum))
            {
                $forum_name = printSecuTags($forum_name);

                echo "<option value=\"" . $fid . "\">&nbsp;&nbsp;&nbsp;" . $forum_name . "</option>\n";
            }
        }

        echo "</select></td></tr></table>\n"
    . "<div style=\"text-align: center;\"><br /><input type=\"submit\" value=\"" . _SEND . "\" /></div>\n"
    . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Forum&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    }

    function do_prune($day, $forum_id)
    {
        global $nuked, $user;

        
        $sql_forum = mysql_query("SELECT nom FROM " . FORUM_TABLE . " WHERE id = '" . $forum_id . "'");
        list($nom) = mysql_fetch_array($sql_forum);
        
        $prunedate = time() - (86400 * $day);

        if (strpos("cat_", $forum_id))
        {
                $cat = preg_replace("`cat_`i", "", $forum_id);
                $and = "AND cat = '" . $cat . "'";
        }
        else if ($forum_id != "")
        {
            $and = "AND forum_id = '" . $forum_id . "'";
        }
        else
        {
                $and = "";
        }

        $sql = mysql_query("SELECT id, sondage FROM " . FORUM_THREADS_TABLE . " WHERE " . $prunedate . " >= last_post AND annonce = 0 " . $and);
        while (list($thread_id, $sondage) = mysql_fetch_row($sql))
        {
            if ($sondage == 1)
            {
                $sql_poll = mysql_query("SELECT id FROM " . FORUM_POLL_TABLE . " WHERE thread_id = '" . $thread_id . "'");
                list($poll_id) = mysql_fetch_row($sql_poll);
                $del1 = mysql_query("DELETE FROM " . FORUM_POLL_TABLE . " WHERE id = '" . $poll_id . "'");
                $del2 = mysql_query("DELETE FROM " . FORUM_OPTIONS_TABLE . " WHERE poll_id = '" . $poll_id . "'");
                $del3 = mysql_query("DELETE FROM " . FORUM_VOTE_TABLE . " WHERE poll_id = '" . $poll_id . "'");
            }

            mysql_query("DELETE FROM " . FORUM_MESSAGES_TABLE . " WHERE thread_id = '" . $thread_id . "'");
            mysql_query("DELETE FROM " . FORUM_THREADS_TABLE . " WHERE id = '" . $thread_id . "'");
        }
        // Action
        $texteaction = "". _ACTIONPRUNEFO .": ".$nom."";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
        . "<div>\n"
        . "" .  _FORUMPRUNE . "\n"
        . "</div>\n"
        . "</div>\n";
        redirect("index.php?file=Forum&page=admin", 2);
    }

    function main_pref()
    {
        global $nuked, $language;

        if ($nuked['forum_file'] == "on") $checked1 = "checked=\"checked\"";
        if ($nuked['forum_rank_team'] == "on") $checked2 = "checked=\"checked\"";

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _ADMINFORUM . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Forum.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Forum&amp;page=admin\">" . _FORUM . "</a> | "
    . "<a href=\"index.php?file=Forum&amp;page=admin&amp;op=add_forum\">" . _ADDFORUM . "</a> | "
    . "<a href=\"index.php?file=Forum&amp;page=admin&amp;op=main_cat\">" . _CATMANAGEMENT . "</a><br />\n"
    . "<a href=\"index.php?file=Forum&amp;page=admin&amp;op=main_rank\">" . _RANKMANAGEMENT . "</a> | "
    . "<a href=\"index.php?file=Forum&amp;page=admin&amp;op=prune\">" . _PRUNE . "</a> | "
    . "</b>" . _PREFS . "</div><br />\n"
    . "<form method=\"post\" action=\"index.php?file=Forum&amp;page=admin&amp;op=change_pref\">\n"
    . "<table  style=\"margin-left: auto;margin-right: auto;text-align: left;\"  border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
    . "<tr><td align=\"center\" colspan=\"2\"><big>" . _PREFS . "</big></td></tr>\n"
    . "<tr><td colspan=\"2\"><b>" . _FORUMTITLE . " :</b> <input type=\"text\" name=\"forum_title\" size=\"40\" value=\"" . $nuked['forum_title'] . "\" /></td></tr>\n"
    . "<tr><td colspan=\"2\"><b>" . _FORUMDESC . " :</b><br /><textarea name=\"forum_desc\" cols=\"55\" rows=\"5\">" . $nuked['forum_desc'] . "</textarea></td></tr>\n"
    . "<tr><td colspan=\"2\">&nbsp;</td></tr>\n"
    . "<tr><td>" . _USERANKTEAM . " :</td><td><input class=\"checkbox\" type=\"checkbox\" name=\"forum_rank_team\" value=\"on\" " . $checked2 . " /></td></tr>\n"
    . "<tr><td>" . _NUMBERTHREAD . " :</td><td><input type=\"text\" name=\"thread_forum_page\" size=\"2\" value=\"" . $nuked['thread_forum_page'] . "\" /></td></tr>\n"
    . "<tr><td>" . _NUMBERPOST . " :</td><td><input type=\"text\" name=\"mess_forum_page\" size=\"2\" value=\"" . $nuked['mess_forum_page'] . "\" /></td></tr>\n"
    . "<tr><td>" . _TOPICHOT . " :</td><td><input type=\"text\" name=\"hot_topic\" size=\"2\" value=\"" . $nuked['hot_topic'] . "\" /></td></tr>\n"
    . "<tr><td>" . _POSTFLOOD . " :</td><td><input type=\"text\" name=\"post_flood\" size=\"2\" value=\"" . $nuked['post_flood'] . "\" /></td></tr>\n"
    . "<tr><td>" . _MAXFIELD . " :</td><td><input type=\"text\" name=\"forum_field_max\" size=\"2\" value=\"" . $nuked['forum_field_max'] . "\" /></td></tr>\n"
    . "<tr><td>" . _ATTACHFILES . " :</td><td><input class=\"checkbox\" type=\"checkbox\" name=\"forum_file\" value=\"on\" " . $checked1 . " /></td></tr>\n"
    . "<tr><td>" . _FILELEVEL . " :</td><td><select name=\"forum_file_level\"><option>" . $nuked['forum_file_level'] . "</option>\n"
    . "<option>0</option>\n"
    . "<option>1</option>\n"
    . "<option>2</option>\n"
    . "<option>3</option>\n"
    . "<option>4</option>\n"
    . "<option>5</option>\n"
    . "<option>6</option>\n"
    . "<option>7</option>\n"
    ." <option>8</option>\n"
    . "<option>9</option></select></td></tr>"
    . "<tr><td>" . _MAXSIZEFILE . " :</td><td><input type=\"text\" name=\"forum_file_maxsize\" size=\"6\" value=\"" . $nuked['forum_file_maxsize'] . "\" /></td></tr>\n"
    . "</table><div style=\"text-align: center;\"><br /><input type=\"submit\" value=\"" . _SEND . "\" /></div>\n"
    . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Forum&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    }

    function change_pref($forum_title, $forum_desc, $forum_rank_team, $thread_forum_page, $mess_forum_page, $hot_topic, $post_flood, $forum_field_max, $forum_file, $forum_file_level, $forum_file_maxsize)
    {
        global $nuked, $user;

        if ($forum_file != "on")
        {
            $forum_file = "off";
        }

        if ($forum_rank_team != "on")
        {
            $forum_rank_team = "off";
        }

        $forum_title = mysql_real_escape_string(stripslashes($forum_title));
        $forum_desc = mysql_real_escape_string(stripslashes($forum_desc));

        $upd1 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $forum_title . "' WHERE name = 'forum_title'");
        $upd2 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $forum_desc . "' WHERE name = 'forum_desc'");
        $upd3 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $forum_rank_team . "' WHERE name = 'forum_rank_team'");
        $upd4 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $thread_forum_page . "' WHERE name = 'thread_forum_page'");
        $upd5 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $mess_forum_page . "' WHERE name = 'mess_forum_page'");
        $upd6 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $hot_topic . "' WHERE name = 'hot_topic'");
        $upd7 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $post_flood . "' WHERE name = 'post_flood'");
        $upd8 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $forum_field_max . "' WHERE name = 'forum_field_max'");
        $upd9 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $forum_file . "' WHERE name = 'forum_file'");
        $upd10 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $forum_file_level . "' WHERE name = 'forum_file_level'");
        $upd11 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $forum_file_maxsize . "' WHERE name = 'forum_file_maxsize'");
        
        // Action
        $texteaction = "". _ACTIONPREFFO .".";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
        . "<div>\n"
        . "" . _PREFUPDATED . "\n"
        . "</div>\n"
        . "</div>\n";
        redirect("index.php?file=Forum&page=admin", 2);
    }

    switch ($_REQUEST['op'])
    {
        case "edit_forum":
            edit_forum($_REQUEST['id']);
            break;

        case "modif_forum":
            modif_forum($_REQUEST['id'], $_REQUEST['titre'], $_REQUEST['cat'], $_REQUEST['description'], $_REQUEST['niveau'], $_REQUEST['level'], $_REQUEST['ordre'], $_REQUEST['level_poll'], $_REQUEST['level_vote'], $_REQUEST['modo']);
            break;

        case "add_forum":
            add_forum();
            break;

        case "del_modo":
            del_modo($_REQUEST['uid'], $_REQUEST['forum_id']);
            break;

        case "send_cat":
            send_cat($_REQUEST['nom'], $_REQUEST['niveau'], $_REQUEST['ordre']);
            break;

        case "add_cat":
            add_cat();
            break;

        case "main_cat":
            main_cat();
            break;

        case "edit_cat":
            edit_cat($_REQUEST['cid']);
            break;

        case "modif_cat":
            modif_cat($_REQUEST['cid'], $_REQUEST['nom'], $_REQUEST['niveau'], $_REQUEST['ordre']);
            break;

        case "del_cat":
            del_cat($_REQUEST['cid']);
            break;

        case "del_forum":
            del_forum($_REQUEST['id']);
            break;

        case "send_forum":
            send_forum($_REQUEST['titre'], $_REQUEST['description'], $_REQUEST['cat'], $_REQUEST['modo'], $_REQUEST['niveau'], $_REQUEST['level'], $_REQUEST['ordre'], $_REQUEST['level_poll'], $_REQUEST['level_vote']);
            break;

        case "main_rank":
            main_rank();
            break;

        case "add_rank":
            add_rank();
            break;

        case "send_rank":
            send_rank($_REQUEST['nom'], $_REQUEST['type'], $_REQUEST['post'], $_REQUEST['image']);
            break;

        case "del_rank":
            del_rank($_REQUEST['rid']);
            break;

        case "edit_rank":
            edit_rank($_REQUEST['rid']);
            break;

        case "modif_rank":
            modif_rank($_REQUEST['rid'], $_REQUEST['nom'], $_REQUEST['type'], $_REQUEST['post'], $_REQUEST['image']);
            break;

        case "prune":
            prune();
            break;

        case "do_prune":
            do_prune($_REQUEST['day'], $_REQUEST['forum_id']);
            break;

        case "main_pref":
            main_pref();
            break;

        case "change_pref":
            change_pref($_REQUEST['forum_title'], $_REQUEST['forum_desc'], $_REQUEST['forum_rank_team'], $_REQUEST['thread_forum_page'], $_REQUEST['mess_forum_page'], $_REQUEST['hot_topic'], $_REQUEST['post_flood'], $_REQUEST['forum_field_max'], $_REQUEST['forum_file'], $_REQUEST['forum_file_level'], $_REQUEST['forum_file_maxsize']);
            break;

        default:
            main();
            break;
    }

}
else if ($level_admin == -1)
{
    echo "<div class=\"notification error png_bg\">\n"
    . "<div>\n"
    . "<br /><br /><div style=\"text-align: center;\">" . _MODULEOFF . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
    . "</div>\n"
    . "</div>\n";
}
else if ($visiteur > 1)
{
    echo "<div class=\"notification error png_bg\">\n"
    . "<div>\n"
    . "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
    . "</div>\n"
    . "</div>\n";
}
else
{
    echo "<div class=\"notification error png_bg\">\n"
    . "<div>\n"
    . "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
    . "</div>\n"
    . "</div>\n";
}

adminfoot();

?>