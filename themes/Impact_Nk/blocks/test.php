<?php
/************************************************
*	Thème Impact_Nk pour Nuked Klan	*
*	Design :  Djgrim (http://www.impact-design.fr/)	*
*	Codage : fce (http://www.impact-design.fr/)			*
************************************************/
defined("INDEX_CHECK") or die ("<div style=\"text-align: center;\">Access deny</div>");

$nb=nbvisiteur();
if($nb[0]>1){$s0="s";}
if($nb[1]>1){$s1="s";}
if($nb[2]>1){$s2="s";}

$sql2 = mysql_query("SELECT mid FROM " . USERBOX_TABLE . " WHERE user_for = '" . $user[0] . "' AND status = 0");
$nb_mess_lu = mysql_num_rows($sql2);
list($mid) = mysql_fetch_array($sql2);

if($nb_mess_lu>0){
    $mess="<a href=\"index.php?file=Userbox&amp;op=show_message&amp;mid=".$mid."\">"._YOUVE." ".$nb_mess_lu." </a> "._INNEW."</a>.";
}
else{
    $mess="<a href=\"index.php?file=Userbox\">"._YOUVE." 0 "._INNEW."</a>";
}

if (!$user){
    $theuser = include("themes/Impact_Nk/blocks/login.php");
}
else{
    include("themes/Impact_Nk/blocks/user.php");
}
?>