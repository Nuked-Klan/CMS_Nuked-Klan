<?php

$this->assign('blockTitle', $this->get('cfg')->get('block'.$this->get('currentAdminBlock').'.title'));

$this->assign('blockNbItems', $this->get('cfg')->get('block'.$this->get('currentAdminBlock').'.nbItems'));

$galleryBlock = $this->get('currentAdminBlock') == 'Gallery' ? true : false;

$articleBlock = $this->get('currentAdminBlock') == 'Article' ? true : false;

$start = 1;
$end = 10;
$increment = 1;
$this->assign('makeForm', false);
$this->assign('makeTitle', false);
$this->assign('makeSelect', false);
$this->assign('makeSelectCat', false);
$this->assign('makeCheckbox', false);
$this->assign('checkboxChecked', false);
$this->assign('makeInputSocial', false);
$this->assign('lightboxChecked', false);
$this->assign('lightboxInputName', null);

if($this->get('currentAdminBlock') == 'Social'){
    $arraySocial = array('Twitter', 'Facebook', 'Google', 'Steam', 'Twitch', 'Youtube');

    $arrayTemp = array();

    foreach($arraySocial as $social){
        $arrayTemp[$social] = $this->get('cfg')->get('social.'.$social);
    }

    $this->assign('arrayInputSocial', $arrayTemp);

    $this->assign('makeInputSocial', true);
}

if ($galleryBlock === true) {
    $start = 3;
    $end = 9;
    $increment = 3;
    $this->assign('makeCheckbox', true);
    $this->assign('makeSelectCat', true);

    $this->assign('checkboxLabel', ACTIVE_LIGHTBOX);

    if ($this->get('cfg')->get('blockGallery.lightbox') == 1) {
        $this->assign('checkboxChecked', true);
    }

    $this->assign('checkboxInputName', 'block'.$this->get('currentAdminBlock').'Lightbox');

    $this->assign('selectedCat', 0);

    if($this->get('cfg')->get('blockGallery.catId') != null){
        $this->assign('selectedCat', $this->get('cfg')->get('blockGallery.catId'));
    }

    $arrayTemp = array(0 => NONE_CAT);

    $dbsGalleryCat = 'SELECT cid AS id, titre AS name
                      FROM '.GALLERY_CAT_TABLE.'
                      ORDER BY name';
    $dbeGalleryCat = mysql_query($dbsGalleryCat);

    while ($dbrGalleryCat = mysql_fetch_assoc($dbeGalleryCat)) {
        $arrayTemp[$dbrGalleryCat['id']] = $dbrGalleryCat['name'];
    }

    $this->assign('selectCat', $arrayTemp);
}

if($articleBlock === true){
    $this->assign('makeCheckbox', true);
    $this->assign('checkboxLabel', ACTIVE_UNIK_CENTER_FULL);
    $this->assign('checkboxInputName', 'block'.$this->get('currentAdminBlock').'FullPage');

    if ($this->get('cfg')->get('blockArticle.fullPage') == 1) {
        $this->assign('checkboxChecked', true);
    }
}

$arrayTemp = array();

for ($i = $start; $i <= $end; $i += $increment) {
    $selected = null;
    if ($i == $this->get('blockNbItems')) {
        $selected = 'selected="selected"';
    }
    $arrayTemp[$i] = $selected;
}

$this->assign('selectBlock', $arrayTemp);


$arrayForm = array('TopMatch', 'Article', 'Match', 'Team', 'Forum', 'Download', 'Guestbook', 'Gallery', 'Social');

$arrayTitle = array('TopMatch', 'Match', 'Team', 'Forum', 'Download', 'Guestbook', 'Gallery', 'Social');

$arraySelect = array('Match', 'Team', 'Forum', 'Download', 'Guestbook', 'Gallery');

if (in_array($this->get('currentAdminBlock'), $arrayForm)) {
    $this->assign('makeForm', true);
}

if (in_array($this->get('currentAdminBlock'), $arrayTitle)) {
    $this->assign('makeTitle', true);
}

if (in_array($this->get('currentAdminBlock'), $arraySelect)) {
    $this->assign('makeSelect', true);
}