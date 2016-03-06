<?php

$footMessage = secu_html(html_entity_decode($GLOBALS['nuked']['footmessage']));

$this->assign('footerMessage' ,$footMessage);