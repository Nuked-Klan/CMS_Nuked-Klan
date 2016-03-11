<?php

$this->assign('blockDownloadTitle', $this->get('cfg')->get('blockDownload.title'));

$this->assign('blockDownloadActive', $this->get('cfg')->get('blockDownload.active'));

$dbsDownload = 'SELECT id, titre, count
                 FROM '.DOWNLOAD_TABLE.'
                 ORDER BY date DESC
                 LIMIT 0, '.$this->get('cfg')->get('blockDownload.nbItems').' ';

$dbeDownload = nkDB_execute($dbsDownload) or die(mysql_error());

$arrayTemp = array();
$i = 0;

while ($dbrDownload = nkDB_fetchAssoc($dbeDownload)) {
    $arrayTemp[$i]['title'] = $dbrDownload['titre'];
    $arrayTemp[$i]['link'] = 'index.php?file=Download&op=description&dl_id='.$dbrDownload['id'];
    $arrayTemp[$i]['count'] = $dbrDownload['count'];
    $i++;
}

$this->assign('blockDownloadContent', $arrayTemp);

$this->assign('nbDownloads', count($arrayTemp));