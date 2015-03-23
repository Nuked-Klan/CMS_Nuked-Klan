<?php

$this->assign('blockGalleryTitle', $this->get('cfg')->get('blockGallery.title'));

$this->assign('blockGalleryActive', $this->get('cfg')->get('blockGallery.active'));

$this->assign('blockGalleryNbItems', $this->get('cfg')->get('blockGallery.nbItems'));

$this->assign('blockGalleryLightbox', $this->get('cfg')->get('blockGallery.lightbox'));

$catId =  $this->get('cfg')->get('blockGallery.catId');

$where = null;

if(!empty($catId)){
    $where = ' WHERE cat = "'.intval($catId).'" ';
}

$dbsGallery = 'SELECT sid, titre, url, url2
               FROM '.GALLERY_TABLE.'
               '.$where.'
               ORDER BY date DESC
               LIMIT 0, '.$this->get('blockGalleryNbItems');
$dbeGallery = mysql_query($dbsGallery) or die(mysql_error());

$arrayTemp = array();
$i = 0;

while ($dbrGallery = mysql_fetch_assoc($dbeGallery)) {
    $src = $biSrc = 'themes/Restless/images/no_image_gallery.png';
    if(!empty($dbrGallery['url'])){
        $src = $bigSrc = $dbrGallery['url'];
    }

    if(!empty($dbrGallery['url2'])){
        $src = $dbrGallery['url2'];
    }

    $arrayTemp[$i]['src'] = $src;
    $arrayTemp[$i]['bigSrc'] = $bigSrc;
    $arrayTemp[$i]['link'] = 'index.php?file=Gallery&op=description&sid='.$dbrGallery['sid'];
    $arrayTemp[$i]['title'] = $dbrGallery['titre'];

    if($this->get('blockGalleryLightbox') == true){
        $arrayTemp[$i]['link'] = $bigSrc;
    }

    $i++;
}

$this->assign('nbImages', count($arrayTemp));

for($i = $this->get('nbImages');
    $i < $this->get('blockGalleryNbItems');
    $i++){
    $arrayTemp[$i+1]['src'] = 'themes/Restless/images/no_image_gallery.png';
    $arrayTemp[$i+1]['link'] = '#';
    $arrayTemp[$i+1]['title'] = 'No image';
}

$this->assign('blockGalleryContent', $arrayTemp);