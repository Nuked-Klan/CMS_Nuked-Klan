<?php

$this->assign('blockForumTitle', $this->get('cfg')->get('blockForum.title'));

$this->assign('blockForumNbTopics', $this->get('cfg')->get('blockForum.nbItems'));

$this->assign('blockForumActive', $this->get('cfg')->get('blockForum.active'));


if (!$GLOBALS['user']) {
    $visiteur = 0;
}
else {
    $visiteur = $GLOBALS['user'][1];
}

$arrayTemp = array();
$i = 0;

$sql = mysql_query('SELECT FTT.id, FTT.titre, FTT.last_post, FTT.forum_id FROM ' . FORUM_THREADS_TABLE . ' AS FTT INNER JOIN ' . FORUM_TABLE . ' AS FT ON FT.id = FTT.forum_id WHERE FT.niveau <= ' . $visiteur . ' ORDER BY last_post DESC LIMIT 0, '.$this->get('blockForumNbTopics'));
while (list($thread_id, $titre, $last_post, $forum_id) = mysql_fetch_row($sql)) {
			  
	$titre = printSecuTags($titre);
	$date = nkDate($last_post);

    $sql2 = mysql_query("SELECT id, auteur FROM " . FORUM_MESSAGES_TABLE . " WHERE thread_id = '" . $thread_id . "' ORDER BY id DESC LIMIT 0, 1");
    list($mess_id, $auteur) = mysql_fetch_array($sql2);

    $arrayTemp[$i]['lien'] = 'index.php?file=Forum&page=viewtopic&forum_id='.$forum_id.'&thread_id='.$thread_id.'#'.$mess_id;
    $arrayTemp[$i]['auteur'] = $auteur;
    $arrayTemp[$i]['titre'] = substr($titre, 0, 45).'...';

    $date = strftime('%d / %m / %Y', $last_post);

    $arrayTemp[$i]['date'] = $date;
    $i++;
}

$this->assign('blockForumContent', $arrayTemp);
