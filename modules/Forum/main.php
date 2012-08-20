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

global $user, $nuked, $language, $cookie_forum;

include('modules/Forum/template.php');

$visiteur = $user ? $user[1] : 0;
$user_last_visit = (empty($user[4])) ? time() : $user[4];

$date_jour = nkDate(time());
$your_last_visite = nkDate($user_last_visit);

if ($nuked['forum_title'] != "")
{
    $title = "<big><b>" . $nuked['forum_title'] . "</b></big><br />" . $nuked['forum_desc'];
} 
else
{
    $title = "<big><b>Forums " . $nuked['name'] . "</b></big><br />" . $nuked['slogan'];
} 

if ($_REQUEST['cat'] != "")
{
    $sql_cat = mysql_query("SELECT nom FROM " . FORUM_CAT_TABLE . " WHERE id = '" . $_REQUEST['cat'] . "'");
    list($cat_name) = mysql_fetch_row($sql_cat);
    $cat_name = printSecuTags($cat_name); 
    $nav = "&nbsp;-&gt; <b>" . $cat_name . "</b>";    
} 

echo "<br /><form method=\"get\" action=\"index.php\">\n"
. "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"10\" border=\"0\">\n"
. "<tr><td style=\"width: 50%;\" align=\"center\">" . $title . "</td>\n"
. "<td style=\"width: 50%;\" align=\"right\"><br /><b>" . _SEARCH . " :</b> <input type=\"text\" name=\"query\" size=\"25\" /><br />\n"
.  "[ <a href=\"index.php?file=Forum&amp;page=search\">" . _ADVANCEDSEARCH . "</a> ]&nbsp;"
. "<input type=\"hidden\" name=\"file\" value=\"Forum\" />\n"
. "<input type=\"hidden\" name=\"page\" value=\"search\" />\n"
. "<input type=\"hidden\" name=\"do\" value=\"search\" />\n"
. "<input type=\"hidden\" name=\"into\" value=\"all\" /></td></tr></table></form>\n"
. "<table width=\"100%\" cellpadding=\"0\" cellspacing=\"4\" border=\"0\">\n"
. "<tr><td valign=\"bottom\"><a href=\"index.php?file=Forum\"><b>" . _INDEXFORUM . "</b></a>" . $nav . "</td><td align=\"right\" valign=\"bottom\"><small>" . _DAYIS . " : " . $date_jour;

if ($user && $user[4] != "")
{
    echo "<br />" . _LASTVISIT . " : " . $your_last_visite;
} 

echo "</small></td></tr></table>\n"
. "<table style=\"background: " . $color3 . ";\" width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"4\">\n"
. "<tr " . $background . ">\n"
. "<td style=\"width: 5%;\">&nbsp;</td>\n"
. "<td style=\"width: 40%;\" align=\"center\"><b>" . _FORUMS . "</b></td>\n"
. "<td style=\"width: 15%;\" align=\"center\"><b>" . _SUBJECTS . "</b></td>\n"
. "<td style=\"width: 15%;\" align=\"center\"><b>" . _MESSAGES . "</b></td>\n"
. "<td style=\"width: 25%;\" align=\"center\"><b>" . _LASTPOST . "</b></td></tr>\n";

if ($_REQUEST['cat'] != "")
{
    $main = mysql_query("SELECT nom, id FROM " . FORUM_CAT_TABLE . " WHERE '" . $visiteur . "' >= niveau AND id = '" . $_REQUEST['cat'] . "'");
} 
else
{
    $main = mysql_query("SELECT nom, id FROM " . FORUM_CAT_TABLE . " WHERE " . $visiteur . " >= niveau ORDER BY ordre, nom");
} 

while (list($nom_cat, $cid) = mysql_fetch_row($main))
{
    $nom_cat = printSecuTags($nom_cat);

    echo "<tr " . $background_cat . "><td colspan=\"5\">&nbsp;&nbsp;<a href=\"index.php?file=Forum&amp;cat=" . $cid . "\"><big><b>" . $nom_cat . "</b></big></a></td></tr>\n";

    $sql = mysql_query("SELECT nom, comment, id from " . FORUM_TABLE . " WHERE cat = '" . $cid . "' AND '" . $visiteur . "' >= niveau ORDER BY ordre, nom");
    while (list($nom, $comment, $forum_id) = mysql_fetch_row($sql))
    {

        $nom = printSecuTags($nom);

        $req2 = mysql_query("SELECT forum_id from " . FORUM_THREADS_TABLE . " WHERE forum_id = '" . $forum_id . "'");
        $num_post = mysql_num_rows($req2);

        $req3 = mysql_query("SELECT forum_id from " . FORUM_MESSAGES_TABLE . " WHERE forum_id = '" . $forum_id . "'");
        $num_mess = mysql_num_rows($req3);

        $req4 = mysql_query("SELECT MAX(id) from " . FORUM_MESSAGES_TABLE . " WHERE forum_id = '" . $forum_id . "'");
        $idmax = mysql_result($req4, 0, "MAX(id)");

        $req5 = mysql_query("SELECT id, thread_id, date, auteur, auteur_id FROM " . FORUM_MESSAGES_TABLE . " WHERE id = '" . $idmax . "'");
        list($mess_id, $thid, $date, $auteur, $auteur_id) = mysql_fetch_array($req5);
        $auteur = nk_CSS($auteur);

          if ($user) {
               $visits = mysql_query("SELECT user_id, forum_id FROM " . FORUM_READ_TABLE . " WHERE user_id = '" . $user[0] . "' AND forum_id LIKE '%" . ',' . $forum_id . ',' . "%' ");
               $results = mysql_fetch_assoc($visits);
               if ($num_post > 0 && strrpos($results['forum_id'], ',' . $forum_id . ',') === false) {
                $img = "<img src=\"modules/Forum/images/forum_new.gif\" alt=\"\" />";
            } 
            else
            {
                $img = "<img src=\"modules/Forum/images/forum.gif\" alt=\"\" />";
            } 
        } 
        else
        {
            $img = "<img src=\"modules/Forum/images/forum.gif\" alt=\"\" />";
        } 

        echo "<tr style=\"background: " . $color2 . ";\">\n"
        . "<td  style=\"width: 5%;\" align=\"center\">" . $img . "</td>\n"
        . "<td style=\"width: 40%;\" onmouseover=\"this.style.backgroundColor='" . $color1 . "'; this.style.cursor='hand';\" onmouseout=\"this.style.backgroundColor='" . $color2 . "'\" onclick=\"document.location='index.php?file=Forum&amp;page=viewforum&amp;forum_id=" . $forum_id . "'\"><a href=\"index.php?file=Forum&amp;page=viewforum&amp;forum_id=" . $forum_id . "\"><big><b>" . $nom ." </b></big></a><br />" . $comment . "</td>\n";

        $sql_page = mysql_query("SELECT thread_id FROM " . FORUM_MESSAGES_TABLE . " WHERE thread_id = '" . $thid . "'");
        $nb_rep = mysql_num_rows($sql_page);

        if ($nb_rep > $nuked['mess_forum_page'])
        {
            $topicpages = $nb_rep / $nuked['mess_forum_page'];
            $topicpages = ceil($topicpages);
            $link_post = "index.php?file=Forum&amp;page=viewtopic&amp;forum_id=" . $forum_id . "&amp;thread_id=" . $thid . "&amp;p=" . $topicpages . "#" . $mess_id;
        } 
        else
        {
            $link_post = "index.php?file=Forum&amp;page=viewtopic&amp;forum_id=" . $forum_id . "&amp;thread_id=" . $thid . "#" . $mess_id;
        } 

        echo "<td style=\"width: 15%;\" align=\"center\">" . $num_post . "</td>\n"
        . "<td style=\"width: 15%;\" align=\"center\">" . $num_mess . "</td>\n"
        . "<td style=\"width: 25%;\" align=\"center\"> ";

        if ($num_mess > 0)
        {
            if ($auteur_id != "")
            {
                $sq_user = mysql_query("SELECT pseudo, country FROM " . USER_TABLE . " WHERE id = '" . $auteur_id . "'");
                $test = mysql_num_rows($sq_user);
                list($author, $country) = mysql_fetch_array($sq_user);

                if ($test > 0 && $author != "")
                {
                    $autor = $author;
                } 
                else
                {
                    $autor = $auteur;
                } 
            } 
            else
            {
                $autor = $auteur;
            } 

            if (strftime("%d %m %Y", time()) ==  strftime("%d %m %Y", $date)) $date = _FTODAY . "&nbsp;" . strftime("%H:%M", $date);
            else if (strftime("%d", $date) == (strftime("%d", time()) - 1) && strftime("%m %Y", time()) == strftime("%m %Y", $date)) $date = _FYESTERDAY . "&nbsp;" . strftime("%H:%M", $date);    
            else $date = nkDate($date);

            echo $date . "<br />";

            if ($auteur_id != "")
            {
                echo "<a href=\"index.php?file=Members&amp;op=detail&amp;autor=" . urlencode($autor) . "\"><b>" . $autor . "</b></a>";
            } 
            else
            {
                echo "<b>" . $autor . "</b>";
            } 

            echo "&nbsp;<a href=\"" . $link_post . "\"><img style=\"border: 0;\" src=\"modules/Forum/images/icon_latest_reply.gif\" alt=\"\" title=\"" . _SEELASTPOST . "\" /></a>";
        } 
        else
        {
            echo _NOPOST;
        } 
        echo "</td></tr>\n";
    } 
} 

$nb = nbvisiteur();

echo "<tr " . $background . "><td colspan=\"5\"><b>" . _FWHOISONLINE . "</b></td></tr>\n"
. "<tr style=\"background: " . $color2 . ";\"><td><img src=\"modules/Forum/images/whosonline.gif\" alt=\"\" /></td>\n"
. "<td colspan=\"4\">" . _THEREARE . "&nbsp;" . $nb[0] . "&nbsp;" . _FVISITORS . ", " . $nb[1] . "&nbsp;" . _FMEMBERS . "&nbsp;" . _AND . "&nbsp;" . $nb[2] . "&nbsp;" . _FADMINISTRATORS . "&nbsp;" . _ONLINE . "<br />" . _MEMBERSONLINE . " : ";

$i = 0;
$online = mysql_query("SELECT username FROM " . NBCONNECTE_TABLE . " WHERE type > 0 ORDER BY date");
while (list($name) = mysql_fetch_row($online))
{
    $i++;
    if ($i == $nb[3])
    {
        $sep = "";
    } 
    else
    {
        $sep = ", ";
    } 

    echo "<a href=\"index.php?file=Members&amp;op=detail&amp;autor=" . urlencode($name) . "\">" . $name . "</a>" . $sep;
}

if (mysql_num_rows($online) == NULL) echo '<em>' . _NONE . '</em>';

echo "</td></tr></table><div style=\"text-align: right;\">";

if ($user)
{
    echo "<a href=\"index.php?file=Forum&amp;op=mark\">" . _MARKREAD . "</a>";
} 
if ($user && $user[4] != "")
{
    echo "<br /><a href=\"index.php?file=Forum&amp;page=search&amp;do=search&amp;date_max=" . $user[4] . "\">" . _VIEWLASTVISITMESS . "</a>";
} 

echo "</div><table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">\n"
. "<tr><td><img src=\"modules/Forum/images/forum_new.gif\" alt=\"\" /></td><td valign=\"middle\">&nbsp;" . _NEWSPOSTLASTVISIT . "</td></tr>\n"
. "<tr><td><img src=\"modules/Forum/images/forum.gif\" alt=\"\" /></td><td valign=\"middle\">&nbsp;" . _NOPOSTLASTVISIT . "</td></tr></table><br />\n";


?>
