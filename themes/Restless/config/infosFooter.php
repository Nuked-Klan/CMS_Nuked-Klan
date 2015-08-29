<?php
$nb = nbvisiteur();

$this->assign('nbAdmins', $nb[2]);
$this->assign('nbMembers', $nb[1]);
$this->assign('nbVisitors', $nb[0]);

$this->assign('adminsPlural', null);
$this->assign('membersPlural', null);
$this->assign('visitorsPlural', 's');

if($nb[2] > 1){
    $this->assign('adminsPlural', 's');
}

if($nb[1] > 1){
    $this->assign('membersPlural', 's');
}

if($nb[0] == 1){
    $this->assign('visitorsPlural', null);
}