<?php
/**
 * index.php
 *
 * Frontend of News module
 *
 * @version     1.8
 * @link https://nuked-klan.fr Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! moduleInit('News'))
    return;

compteur('News');


function index(){

    global $op, $nuked, $language, $theme, $p;

    $max_news = $nuked['max_news'];
    $day = time();

    if ($op == 'categorie') {
        $_REQUEST['cat_id'] = (int) $_REQUEST['cat_id'];

        $where = "WHERE cat = '{$_REQUEST['cat_id']}' AND $day >= date";
    } elseif ($op == 'suite' || $op == 'index_comment') {
        $_REQUEST['news_id'] = (int) $_REQUEST['news_id'];

        $where = "WHERE id = '{$_REQUEST['news_id']}' AND $day >= date";
    } else {
        $where = "WHERE $day >= date";
    }

    $sql_nbnews = nkDB_execute("SELECT id FROM ".NEWS_TABLE." $where");
    $nb_news = nkDB_numRows($sql_nbnews);

    $start = $p * $max_news - $max_news;

    if ($op == 'categorie') {
        $WhereNews = "WHERE cat = '{$_REQUEST['cat_id']}' AND $day >= date ORDER BY date DESC LIMIT $start, $max_news";
    } elseif ($op == 'suite' || $op == 'index_comment') {
        $WhereNews = "WHERE id = '{$_REQUEST['news_id']}'";
    } else {
        $WhereNews = "WHERE $day >= date ORDER BY date DESC LIMIT $start, $max_news";
    }

    $sql = nkDB_execute("SELECT id, auteur, auteur_id, date, titre, coverage, texte, suite, cat FROM ".NEWS_TABLE." $WhereNews");

    if (nkDB_numRows($sql) <= 0) {
        echo '<p style="text-align: center">' . _NONEWSINDB . '</p>';
    }

    while ($TabNews = nkDB_fetchAssoc($sql)) {
        $TabNews['titre'] = printSecuTags($TabNews['titre']);

        $sql2 = nkDB_execute("SELECT im_id FROM ".COMMENT_TABLE." WHERE im_id = '{$TabNews['id']}' AND module = 'news'");
        $nb_comment = nkDB_numRows($sql2);

        $sql3 = nkDB_execute("SELECT titre, image FROM ".NEWS_CAT_TABLE." WHERE nid = '{$TabNews['cat']}'");
        $TabCat = nkDB_fetchAssoc($sql3);

        $data['date'] = nkDate($TabNews['date']);
        $data['date_timestamp'] = $TabNews['date'];
        $data['cat'] = $TabCat['titre'];
        $data['catid'] = $TabNews['cat'];
        $data['id'] = $TabNews['id'];
        $data['titre'] = printSecuTags($TabNews['titre']);

        if ($TabNews['auteur_id'] != '') {
            $data['authorLink'] = '<a href="index.php?file=Members&amp;op=detail&amp;autor='. urlencode($TabNews['auteur']) .'">'. $TabNews['auteur'] .'</a>';
        }
        else {
            $data['authorLink'] = $TabNews['auteur'];
        }

        $data['auteur'] = $TabNews['auteur'];
        $data['nb_comment'] = $nb_comment;
        $data['printpage'] = '<a title="'._PDF.'" href="index.php?file=News&amp;op=pdf&amp;news_id='.$TabNews['id'].'" onclick="window.open(this.href); return false;"><img style="border:none;" src="images/pdf.gif" alt="'._PDF.'" title="'._PDF.'" width="16" height="16" /></a>';
        $data['friend'] = '<a title="'._FSEND.'" href="index.php?file=News&amp;op=sendfriend&amp;news_id='.$TabNews['id'].'"><img style="border:none;" src="images/friend.gif" alt="'._FSEND.'" title="'._FSEND.'" width="16" height="16" /></a>';

        $data['image'] = (!empty($TabCat['image'])) ? '<a title="'.$TabCat['titre'].'" href="index.php?file=Archives&amp;op=sujet&amp;cat_id='.$TabNews['cat'].'"><img style="float:right;border:0;" src="'.$TabCat['image'].'" alt="'.$TabCat['titre'].'" title="'.$TabCat['titre'].'" /></a>' : '';
        $data['coverageImg'] = (!empty($TabNews['coverage'])) ? '<img class="nkNewsCoverage" src="'.$TabNews['coverage'].'" alt="'.$TabNews['titre'].'" title="'.$TabNews['titre'].'" />' : '';
        $data['coverage'] = (!empty($TabNews['coverage'])) ? ''.$TabNews['coverage'].'' : '';

        if ($op == 'suite' || $op == 'index_comment' && !empty($TabNews['suite'])) {
            $data['texte'] = $TabNews['texte'].'<br /><br />'.$TabNews['suite'];
        } elseif (!empty($TabNews['suite'])) {
            // Bouton lire la suite du thème ou texte par défaut
            $data['bouton'] = (is_file('themes/' . $theme . '/images/readmore.png')) ? '<img src="themes/' . $theme . '/images/readmore.png" alt="" title="' . _READMORE . '" />' : _READMORE;

            $data['texte'] = $TabNews['texte'].'<div class="nkNewsReadmore" style="text-align:right;"><a title="'._READMORE.'" href="index.php?file=News&amp;op=suite&amp;news_id='.$TabNews['id'].'">' . $data['bouton'] . '</a></div>';
        } else {
            $data['texte'] = $TabNews['texte'];
        }

        news($data);

    }

    $url = ($op == 'categorie') ? 'index.php?file=News&amp;op=categorie&amp;cat_id='.$_REQUEST['cat_id'] : 'index.php?file=News';

    if ($nb_news > $max_news) {
        echo '&nbsp;';
        number($nb_news, $max_news, $url);
        echo '<br /><br />';
    }
}

function index_comment($news_id) {

    global $user, $visiteur, $nuked;

    $news_id = (int) $news_id;

    if( $visiteur >= admin_mod("News")){
        echo '<script type="text/javascript">function delnews(id){if(confirm(\''._DELTHISNEWS.' ?\')){document.location.href = \'index.php?file=News&page=admin&op=do_del&news_id=\'+id;}}</script>
        <div style="text-align:right; margin:10px;">
            <div class="nkButton-group">
                <a href="index.php?file=News&amp;page=admin&amp;op=edit&amp;news_id='.$news_id.'" title="'._EDIT.'" class="nkButton icon alone edit"></a>
                <a href="javascript:delnews(\''.$news_id.'\');" title="'._DEL.'" class="nkButton icon alone remove danger"></a>
            </div>
        </div>';
    }

    index();

    $sql = nkDB_execute(
        'SELECT active
        FROM '. COMMENT_MODULES_TABLE .'
        WHERE module = \'news\''
    );

    $row = nkDB_fetchArray($sql);

    if ($row['active'] == 1 && $visiteur >= nivo_mod('Comment') && nivo_mod('Comment') > -1) {
        include_once 'modules/Comment/index.php';
        com_index('news', $news_id);
    }
}

function suite($news_id) {
    global $user, $visiteur, $nuked;

    $news_id = (int) $news_id;

    if ($visiteur >= admin_mod("News")) {
        echo '<script type="text/javascript">function delnews(id){if(confirm(\''._DELTHISNEWS.' ?\')){document.location.href = \'index.php?file=News&page=admin&op=do_del&news_id=\'+id;}}</script>
        <div style="text-align:right; margin:10px;">
            <div class="nkButton-group">
                <a href="index.php?file=News&amp;page=admin&amp;op=edit&amp;news_id='.$news_id.'" title="'._EDIT.'" class="nkButton icon alone edit"></a>
                <a href="javascript:delnews(\''.$news_id.'\');" title="'._DEL.'" class="nkButton icon alone remove danger"></a>
            </div>
        </div>';
    }

    index();

    $sql = nkDB_execute(
        'SELECT active
        FROM '. COMMENT_MODULES_TABLE .'
        WHERE module = \'news\''
    );

    $row = nkDB_fetchArray($sql);

    if ($row['active'] == 1 && $visiteur >= nivo_mod('Comment') && nivo_mod('Comment') > -1) {
        include_once 'modules/Comment/index.php';
        com_index('news', $news_id);
    }

}

function categorie(){
    index();
}

function sujet(){
    global $nuked;

    opentable();

    echo '<br /><div style="text-align:center;"><big><b>'._SUBJECTNEWS.'</b></big></div><br /><br />
            <table cellspacing="0" cellpadding="3" border="0">';

    $sql = nkDB_execute("SELECT nid, titre, description, image FROM ".NEWS_CAT_TABLE." ORDER BY titre");
    while ($row = nkDB_fetchAssoc($sql)) {

        $row['titre'] = printSecuTags($row['titre']);

        echo '<tr>';

        if (!empty($row['image'])) {
            echo '<td><a href="index.php?file=News&amp;op=categorie&amp;cat_id='.$row['nid'].'">
                    <img style="border:none;" src="'.$row['image'].'" align="left" alt="" title="'._SEENEWS.'&nbsp;'.$row['titre'].'" /></a></td>';
        }

        echo '<td><b>'.$row['titre'].' :</b><br />'.$row['description'].'</td></tr><tr><td colspan="2">&nbsp;</td></tr>';
    }

    echo '</table><br /><br /><div style="text-align:center;"><small><i>( '._CLICSCREEN.' )</i></small></div><br />';

    closetable();
}

function pdf($news_id) {
    global $nuked, $language, $defaultHttpHeader;

    $news_id = (int) $news_id;

    nkTemplate_setPageDesign('none');
    $defaultHttpHeader = false;

    if ($language == "french" && strpos("WIN", PHP_OS)) setlocale (LC_TIME, "french");
    else if ($language == "french" && strpos("BSD", PHP_OS)) setlocale (LC_TIME, "fr_FR.ISO8859-1");
    else if ($language == "french") setlocale (LC_TIME, "fr_FR");
    else setlocale (LC_TIME, $language);

    $sql = nkDB_execute("SELECT auteur, auteur_id, date, titre, texte, suite FROM ".NEWS_TABLE." WHERE id = '$news_id'");
    $row = nkDB_fetchAssoc($sql);

    $heure = strftime("%H:%M", $row['date']);
    $text = $row['texte'].'<br><br>'.$row['suite'];

    if ($row['auteur_id'] != '') {
        $author = @nkHtmlEntityDecode($row['auteur']);
        $authorLink = '<a href="'. $nuked['url'] .'/index.php?file=Members&amp;op=detail&amp;autor='. urlencode($author) .'">'. $author .'</a>';
    }
    else {
        $authorLink = $row['auteur'];
    }

    $date = nkDate($row['date']);

    $posted = '<font size="1">'._NEWSPOSTBY.' '. $authorLink .' '.$date.'</font><br><br>';

    $texte = $posted.$text;

    $articleurl = $nuked['url'].'/index.php?file=News&op=index_comment&news_id='.$news_id;

    include 'Includes/html2pdf/html2pdf.class.php';
    $sitename = $nuked['name'].' - '.$nuked['slogan'];
    $sitename  = @nkHtmlEntityDecode($sitename);

    $texte = "<h1>{$row['titre']}</h1><hr />$texte<hr />$sitename<br />$articleurl.";

    $file = $sitename .'_'. $row['titre'];
    $file = str_replace(' ', '_', $file);
    $file .= '.pdf';

    $pdf = new HTML2PDF('P','A4','fr');
    $pdf->setDefaultFont('dejavusans');
    $pdf->WriteHTML(utf8_encode($texte));
    $pdf->Output($file);
}

function sendfriend($news_id) {
    global $nuked, $user;

    $news_id = (int) $news_id;

    opentable();

    echo '<script type="text/javascript">
        function verifchamps(){
            if(document.getElementById(\'sf_pseudo\').value.length == 0){
                alert(\''._NONICK.'\');
                return false;
            }
            if(document.getElementById(\'sf_mail\').value.indexOf(\'@\') == -1){
                alert(\''._BADMAIL.'\');
                return false;
            }
            return true;
        }
        </script>';

    $sql = nkDB_execute("SELECT titre FROM ".NEWS_TABLE." WHERE id = '$news_id'");
    list($title) = nkDB_fetchArray($sql);

    echo '<form method="post" action="index.php?file=News" onsubmit="return verifchamps()">
            <table style="margin:0 auto;text-align:left;" width="60%" cellspacing="1" cellpadding="1" border="0">
            <tr><td align="center"><br /><big><b>'._FSEND.'</b></big><br /><br />'._YOUSUBMIT.' :<br /><br />
            <b>'.$title.'</b><br /><br /></td></tr><tr><td align="left">
            <b>'._NYNICK.' : </b>&nbsp;<input type="text" id="sf_pseudo" name="pseudo" value=""'.$user[2].'" size="20" /></td></tr>
            <tr><td><b>'._FMAIL.' : </b>&nbsp;<input type="text" id="sf_mail" name="mail" value="mail@gmail.com" size="25" /></td></tr>
            <tr><td><b>'._NYCOMMENT.' : </b><br /><textarea name="comment" style="width:100%;" rows="10"></textarea></td></tr>
            <tr><td align="center">';

    if (initCaptcha()) echo create_captcha();

    echo '<input type="hidden" name="op" value="sendnews" />
            <input type="hidden" name="news_id" value="'.$news_id.'" />
            <input type="hidden" name="title" value="'.$title.'" />
            <input class="nkButton" type="submit" value="'.__('SEND').'" /></td></tr></table></form><br />';

    closetable();
}

function sendnews($title, $news_id, $comment, $mail, $pseudo) {
    global $nuked, $user_ip;

    $news_id = (int) $news_id;

    if (initCaptcha() && ! validCaptchaCode())
        return;

    if ($pseudo == '' || ctype_space($pseudo)) {
        printNotification(stripslashes(_NONICK), 'error', array('backLinkUrl' => 'javascript:history.back()'));
        return;
    }

    if ($mail == '' || ctype_space($mail)) {
        printNotification(stripslashes(_BADMAIL), 'error', array('backLinkUrl' => 'javascript:history.back()'));
        return;
    }

    $date2 = time();
    $date2 = nkDate($date2);
    $mail = trim($mail);
    $pseudo = trim($pseudo);

    $subject = $nuked['name'].', '.$date2;
    $corps = $pseudo." (IP : $user_ip) "._READNEWS." $title, "._NEWSURL."\r\n{$nuked['url']}/index.php?file=News&op=index_comment&news_id=$news_id\r\n\r\n"._NYCOMMENT." : $comment\r\n\r\n\r\n{$nuked['name']} - {$nuked['slogan']}";
    $from = "From: {$nuked['name']} <{$nuked['mail']}>\r\nReply-To: ".$nuked['mail'];

    $subject = @nkHtmlEntityDecode($subject);
    $corps = @nkHtmlEntityDecode($corps);
    $from = @nkHtmlEntityDecode($from);

    mail($mail, $subject, $corps, $from);

    printNotification(_SENDFMAIL, 'success');
    redirect('index.php?file=News', 2);
}

switch ($GLOBALS['op']) {

    case'index':
    index();
    break;

    case'index_comment':
    index_comment($_REQUEST['news_id']);
    break;

    case'suite':
    suite($_REQUEST['news_id']);
    break;

    case'categorie':
    categorie();
    break;

    case'sujet':
    sujet();
    break;

    case'pdf':
    pdf($_REQUEST['news_id']);
    break;

    case'sendfriend':
    sendfriend($_REQUEST['news_id']);
    break;

    case'sendnews':
    opentable();
    sendnews($_REQUEST['title'], $_REQUEST['news_id'], $_REQUEST['comment'], $_REQUEST['mail'], $_REQUEST['pseudo']);
    closetable();
    break;

    default:
    index();
    break;
}

?>
