<?php

$this->assign('blockGuestbookTitle', $this->get('cfg')->get('blockGuestbook.title'));

$this->assign('blockGuestbookActive', $this->get('cfg')->get('blockGuestbook.active'));


$dbsGuestbook = 'SELECT name, date, comment
                 FROM '.GUESTBOOK_TABLE.'
                 ORDER BY date DESC
                 LIMIT 0, '.$this->get('cfg')->get('blockGuestbook.nbItems').' ';

$dbeGuestbook = mysql_query($dbsGuestbook) or die(mysql_error());

$arrayTemp = array();
$i = 0;

while ($dbrGuestbook = mysql_fetch_assoc($dbeGuestbook)) {
    $arrayTemp[$i]['author'] = $dbrGuestbook['name'];
    $arrayTemp[$i]['date'] = date('d/m/Y', $dbrGuestbook['date']).' &agrave; '.date('G:i', $dbrGuestbook['date']);
    $arrayTemp[$i]['text'] = substr($dbrGuestbook['comment'], 0, 150);
    $i++;
}

$this->assign('blockGuestbookContent', $arrayTemp);

$this->assign('nbComments', count($this->get('blockGuestbookContent')));