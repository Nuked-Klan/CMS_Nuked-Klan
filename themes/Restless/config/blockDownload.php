<?php

$this->assign('blockDownloadTitle', $this->get('cfg')->get('blockDownload.title'));

$this->assign('blockDownloadActive', $this->get('cfg')->get('blockDownload.active'));

$dbsDownload = 'SELECT id, titre, count
                 FROM '.DOWNLOAD_TABLE.'
                 ORDER BY date DESC
                 LIMIT 0, '.$this->get('cfg')->get('blockDownload.nbItems').' ';

$dbeDownload = mysql_query($dbsDownload) or die(mysql_error());

$arrayTemp = array();
$i = 0;

while ($dbrDownload = mysql_fetch_assoc($dbeDownload)) {
    $arrayTemp[$i]['title'] = $dbrDownload['titre'];
    $arrayTemp[$i]['link'] = 'index.php?file=Download&op=description&dl_id='.$dbrDownload['id'];
    $arrayTemp[$i]['count'] = $dbrDownload['count'];
    $i++;
}

$this->assign('blockDownloadContent', $arrayTemp);

$this->assign('nbDownloads', count($arrayTemp));