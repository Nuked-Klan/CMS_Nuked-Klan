<?php

$data = $this->get('data');

$this->assign('blockCenterTitle', $data['titre']);

$this->assign('blockCenterContent', $data['content']);