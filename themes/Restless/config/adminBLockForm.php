<?php

$this->assign('blockTitle', $this->get('cfg')->get('block'.$this->get('currentAdminBlock').'.title'));

$this->assign('blockNbItems', $this->get('cfg')->get('block'.$this->get('currentAdminBlock').'.nbItems'));

$galleryBlock = $this->get('currentAdminBlock') == 'Gallery' ? true : false;

$articleBlock = $this->get('currentAdminBlock') == 'Article' ? true : false;

$aboutBlock = $this->get('currentAdminBlock') == 'About' ? true : false;

$start = 1;
$end = 10;
$increment = 1;

$this->assign('makeForm', false);
$this->assign('makeTitle', false);
$this->assign('makeSelect', false);
$this->assign('makeSelectCat', false);
$this->assign('makeSelectMatch', false);
$this->assign('makeCheckbox', false);
$this->assign('checkboxChecked', false);
$this->assign('makeInputSocial', false);
$this->assign('lightboxChecked', false);
$this->assign('lightboxInputName', null);
$this->assign('makeTextarea', false);

if($this->get('currentAdminBlock') == 'Social'){
    $arraySocial = array('Twitter', 'Facebook', 'Google', 'Steam', 'Twitch', 'Youtube');

    $arrayTemp = array();

    foreach($arraySocial as $social){
        $arrayTemp[$social] = $this->get('cfg')->get('social.'.$social);
    }

    $this->assign('arrayInputSocial', $arrayTemp);

    $this->assign('makeInputSocial', true);
}

if($this->get('currentAdminBlock') == 'TopMatch'){
    $this->assign('makeSelectMatch', true);

    $this->assign('selectedMatch', 0);

    $currentId = $this->get('cfg')->get('blockTopMatch.id');

    if(!empty($currentId)){
        $this->assign('selectedMatch', $currentId);
    }

    $selected = null;

    if($currentId == 0){
        $selected = 'selected="selected"';
    }

    $arrayTemp = array(
        0 => array(
            'name' => NONE_MATCH,
            'selected' => $selected)
    );

    $dbsTopMatchCat = 'SELECT A.warid AS id, A.adversaire AS opponent, B.titre AS team,
                              UNIX_TIMESTAMP(DATE(CONCAT(date_an, \'-\', date_mois, \'-\', date_jour, \' \', heure, \':00\'))) AS date
                      FROM '.WARS_TABLE.' AS A
                      LEFT JOIN '.TEAM_TABLE.' AS B
                      ON B.cid = A.team
                      ORDER BY date DESC';
    $dbeTopMatchCat = mysql_query($dbsTopMatchCat);

    while ($dbrTopMatchCat = mysql_fetch_assoc($dbeTopMatchCat)) {
        $selected = null;

        if($currentId == $dbrTopMatchCat['id']){
            $selected = 'selected="selected"';
        }
        $arrayTemp[$dbrTopMatchCat['id']] = array(
            'name' => $dbrTopMatchCat['team'].' vs '.$dbrTopMatchCat['opponent'].' : '.nkdate($dbrTopMatchCat['date'], true),
            'selected' => $selected);
    }

    $this->assign('selectMatch', $arrayTemp);
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

    $selected = null;
    if($this->get('cfg')->get('blockGallery.catId') == 0){
        $selected = 'selected="selected"';
    }

    $arrayTemp = array(
        0 => array(
            'name' => NONE_CAT,
            'selected' => $selected)
    );

    $dbsGalleryCat = 'SELECT cid AS id, titre AS name
                      FROM '.GALLERY_CAT_TABLE.'
                      ORDER BY name';
    $dbeGalleryCat = mysql_query($dbsGalleryCat);

    while ($dbrGalleryCat = mysql_fetch_assoc($dbeGalleryCat)) {
        $selected = null;

        if($this->get('cfg')->get('blockGallery.catId') == $dbrGalleryCat['id']){
            $selected = 'selected="selected"';
        }
        $arrayTemp[$dbrGalleryCat['id']] = array(
            'name' => $dbrGalleryCat['name'],
            'selected' => $selected
        );
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

if($aboutBlock === true){
    $this->assign('makeTextarea', true);
    $this->assign('textareaTitle', CONTENT);
    $this->assign('textareaContent', $this->get('cfg')->get('blockAbout.content'));
}


$arrayForm = array('About', 'TopMatch', 'Article', 'Match', 'Team', 'Forum', 'Download', 'Guestbook', 'Gallery', 'Social', 'Sponsors');

$arrayTitle = array('About', 'TopMatch', 'Match', 'Team', 'Forum', 'Download', 'Guestbook', 'Gallery', 'Social', 'Sponsors');

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