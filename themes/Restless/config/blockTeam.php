<?php

$this->assign('blockTeamTitle', $this->get('cfg')->get('blockTeam.title'));

$this->assign('blockTeamActive', $this->get('cfg')->get('blockTeam.active'));

$this->assign('blockTeamContent', array(
    array('image' => 'themes/Restless/images/misc/team_bo2.png', 'title' => 'Call of Duty Black Ops 2'),
    array('image' => 'themes/Restless/images/misc/team_csgo.png', 'title' => 'Counter Strike Global Offensive')
));