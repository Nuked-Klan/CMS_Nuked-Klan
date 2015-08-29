<?php

$this->assign('blockForumTitle', $this->get('cfg')->get('blockForum.title'));

$this->assign('blockForumNbTopics', $this->get('cfg')->get('blockForum.nbItems'));

$this->assign('blockForumActive', $this->get('cfg')->get('blockForum.active'));

$dbsForum = ' SELECT titre, auteur, date, forum_id, thread_id
              FROM '.FORUM_MESSAGES_TABLE.'
              ORDER BY date DESC
              LIMIT 0, '.$this->get('blockForumNbTopics');
$dbeForum = mysql_query($dbsForum) or die(mysql_error());

$arrayTemp = array();
$i = 0;

while ($row = mysql_fetch_assoc($dbeForum)) {
    $arrayTemp[$i]['lien'] = 'index.php?file=Forum&page=viewtopic&forum_id='.$row['forum_id'].'&thread_id='.$row['thread_id'];
    $arrayTemp[$i]['auteur'] = $row['auteur'];
    $arrayTemp[$i]['titre'] = substr($row['titre'], 0, 45).'...';

    $date = strftime('%d / %m / %Y', $row['date']);


    $arrayTemp[$i]['date'] = $date;
    $i++;
}

$this->assign('blockForumContent', $arrayTemp);