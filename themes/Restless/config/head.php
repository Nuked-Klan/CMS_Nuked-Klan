<?php

$this->assign('siteTitle', $GLOBALS['nuked']['name'].' - '.$GLOBALS['nuked']['slogan']);

$this->assign('siteKeywords', $GLOBALS['nuked']['keyword']);

$this->assign('siteDescription', $GLOBALS['nuked']['description']);

$this->assign('backgroundPosition', $this->get('cfg')->get('general.backgroundPosition'));

$this->assign('backgroundImage', $this->get('cfg')->get('general.backgroundImage'));

$this->assign('mainLogoPosition', $this->get('cfg')->get('general.mainLogoPosition'));