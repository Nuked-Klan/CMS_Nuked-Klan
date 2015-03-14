<?php

$this->assign('blockSocialTitle', $this->get('cfg')->get('blockSocial.title'));

$this->assign('blockSocialActive', $this->get('cfg')->get('blockSocial.active'));

$arraySocial = array('Twitter', 'Facebook', 'Google', 'Twitch', 'Steam', 'Youtube');

$arrayTemp = array();

foreach ($arraySocial as $social) {
    $socialLink = $this->get('cfg')->get('social.'.$social);
    if (!empty($socialLink)) {
        $arrayTemp[$social] = $socialLink;
    }
}

$this->assign('blockSocialContent', $arrayTemp);