<?php

$arrayBlocks = array('Match', 'Team', 'Forum', 'Download', 'Guestbook', 'Article', 'Gallery', 'Social');

$arraySelect = array('Match', 'Team', 'Forum', 'Download', 'Guestbook', 'Gallery');

$arraySelectGallery = array(3, 6, 9);

$this->assign('adminBlocksError', false);

try {
    foreach ($arrayBlocks as $block) {
        $inputCheckboxName = 'block'.$block.'Active';
        $newState = false;

        if (array_key_exists($inputCheckboxName, $_REQUEST) && $_REQUEST[$inputCheckboxName]) {
            $newState = true;
        }

        $this->get('cfg')->set('block'.$block.'.active', $newState);

        $inputTitleName = 'block'.$block.'Title';

        $titleLentgh = stripslashes($_REQUEST[$inputTitleName]);
        
        $newTitle = htmlentities($titleLentgh, ENT_COMPAT, "ISO-8859-1");

        if ($block != 'Article') {
            if (strlen($titleLentgh) < 4) {
                throw new Exception(TITLE_SMALL_ERROR.' ('.$newTitle.')');
            }
            else if(strlen($titleLentgh) > 28) {
                throw new Exception(TITLE_LONG_ERROR.' ('.$newTitle.')');
            }
            else{
                $this->get('cfg')->set('block'.$block.'.title', $newTitle);
            }
        }
    }

    foreach ($arraySelect as $block) {
        $inputSelectName = 'block'.$block.'NbItems';
        $newState = false;

        if (array_key_exists($inputSelectName, $_REQUEST) && $_REQUEST[$inputSelectName]) {
            if ($block == Gallery && !in_array($_REQUEST[$inputSelectName], $arraySelectGallery)) {
                throw new Exception(SELECT_GALLERY_ERROR);
            }
            else if($block != 'Gallery' && ($_REQUEST[$inputSelectName] < 1 || $_REQUEST[$inputSelectName] > 10) ){
                throw new Exception(SELECT_ERROR.' ('.$block.')');
            }
            else{
                $this->get('cfg')->set('block'.$block.'.nbItems', intval($_REQUEST[$inputSelectName]));
            }
        }
    }

    $newStateLightbox = false;
    if (array_key_exists('blockGalleryLightbox', $_REQUEST) && $_REQUEST['blockGalleryLightbox']) {
        $newStateLightbox = true;
    }

    $this->get('cfg')->set('blockGallery.lightbox', $newStateLightbox);

    if (array_key_exists('blockGalleryCat', $_REQUEST)) {
        $newValue = intval($_REQUEST['blockGalleryCat']);
        if ($_REQUEST['blockGalleryCat'] == 0) {
            $newValue = null;
        }
        $this->get('cfg')->set('blockGallery.catId', $newValue);
    }

    $arraySocial = array('Twitter', 'Facebook', 'Google', 'Steam', 'Twitch', 'Youtube');

    foreach($arraySocial as $social){
        $socialInputName = 'social'.$social;
        $newLink = null;

        if(array_key_exists($socialInputName, $_REQUEST)){
            $newLink = $_REQUEST[$socialInputName];
        }

        $this->get('cfg')->set('social.'.$social, $newLink);
    }

    $this->get('cfg')->save();
}
catch (Exception $e) {
    $this->assign('adminBlocksError', true);
    $this->assign('errorMessage', $e->getMessage());
}