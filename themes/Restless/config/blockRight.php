<?php

$data = $this->get('data');

$this->assign('blockRightTitle', $data['titre']);

$this->assign('blockRightContent', $data['content']);