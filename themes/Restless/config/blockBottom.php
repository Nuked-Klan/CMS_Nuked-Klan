<?php

$data = $this->get('data');

$this->assign('blockBottomTitle', $data['titre']);

$this->assign('blockBottomContent', $data['content']);