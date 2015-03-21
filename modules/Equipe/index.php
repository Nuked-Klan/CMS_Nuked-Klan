<?php 
/**
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
if (!defined("INDEX_CHECK"))
{
    die ("<div style=\"text-align: center;\">You cannot open this page directly</div>");
} 

global $language, $user;
translate("modules/Equipe/lang/" . $language . ".lang.php");
$visiteur = (!$user) ? 0 : $user[1];

$ModName = basename(dirname(__FILE__));
$level_access = nivo_mod($ModName);
if ($visiteur >= $level_access && $level_access > -1)
{

    function index() {
	 global $bgcolor1, $bgcolor2, $bgcolor3, $theme, $nuked, $cid, $user, $visiteur;
	
    opentable();

	  $sql1=mysql_query('SELECT * FROM '.$nuked['prefix'].'_staff_cat ORDER BY ordre ASC');
	  while($req1 = mysql_fetch_object($sql1))
	  {	
	  	if ($req1->img != 'non') $img_url = '<a href="index.php?file=Equipe&amp;op=view_cat&amp;cat_id='.$req1->id.'"><img src="'.$req1->img.'" alt="" style="border:none;" title="Afficher uniquement les '.$req1->nom.'" /></a>';
		else $img_url = '<a href="index.php?file=Equipe&amp;op=view_cat&amp;cat_id='.$req1->id.'">'.$req1->nom.'</a>';
	  			
		echo'<table width="100%">'
		.'<tr><td>'.$img_url.'</td></tr>'
		.'<tr><td>';
		
		$ii=1;
		echo'<table cellpadding="0" cellspacing="0" width="100%" height="100%"><tr>';
		
		 echo "<table style=\"background: " . $bgcolor2 . ";border: 1px solid " . $bgcolor3 . ";\" width=\"100%\" cellpadding=\"2\" cellspacing=\"1\">\n"
	. "<tr style=\"background: " . $bgcolor3 . ";\">\n"
	. "<td style=\"width: 5%;\">&nbsp;</td>\n"
	. "<td style=\"width: 20%;\" align=\"center\"><b>" . _NICK . "</b></td>\n";
    if ($nuked['user_email'] == 'on'){echo "<td align=\"center\"><b>" . _MAIL . "</b></td>\n";}
	if ($nuked['user_icq'] == 'on'){echo "<td align=\"center\"><b>" . _ICQ . "</b></td>\n";}
	if ($nuked['user_msn'] == 'on'){echo "<td align=\"center\"><b>" . _MSN . "</b></td>\n";}
	if ($nuked['user_aim'] == 'on'){echo "<td align=\"center\"><b>" . _AIM . "</b></td>\n";}
	if ($nuked['user_yim'] == 'on'){echo "<td align=\"center\"><b>" . _YIM . "</b></td>\n";}
	if ($nuked['user_xfire'] == 'on'){echo "<td align=\"center\"><b>" . _XFIRE . "</b></td>\n";}
	if ($nuked['user_facebook'] == 'on'){echo "<td align=\"center\"><b>" . _FACEBOOK . "</b></td>\n";}
	if ($nuked['user_origin'] == 'on'){echo "<td align=\"center\"><b>" . _ORIGINEA . "</b></td>\n";}
	if ($nuked['user_steam'] == 'on'){echo "<td align=\"center\"><b>" . _STEAM . "</b></td>\n";}
	if ($nuked['user_twitter'] == 'on'){echo "<td align=\"center\"><b>" . _TWITTER . "</b></td>\n";}	
	if ($nuked['user_skype'] == 'on'){echo "<td align=\"center\"><b>" . _SKYPE . "</b></td>\n";}
   echo "<td style=\"width: 15%;\" align=\"center\"><b>" . _RANK . "</b></td></tr>\n";
		
		  $sql2=mysql_query('SELECT * FROM '.$nuked['prefix'].'_staff WHERE categorie_id="'.$req1->id.'"');
		  while($req2 = mysql_fetch_object($sql2))
		  {
		    $sql3=mysql_query('SELECT * FROM '.$nuked['prefix'].'_users WHERE id="'.$req2->membre_id.'"');
		    $req3 = mysql_fetch_object($sql3);
			
			$sql4=mysql_query('SELECT * FROM '.$nuked['prefix'].'_users_detail WHERE user_id="'.$req2->membre_id.'"');
		    $req4 = mysql_fetch_object($sql4);
			
			$sql5=mysql_query('SELECT * FROM '.$nuked['prefix'].'_staff_status WHERE id="'.$req2->status_id.'"');
		    $req5 = mysql_fetch_object($sql5);
			
			$sql6=mysql_query('SELECT * FROM '.$nuked['prefix'].'_staff_rang WHERE id="'.$req2->rang_id.'"');
		    $req6 = mysql_fetch_object($sql6);
						
		   $img = "images/user/email.png";


			
			list ($pays, $ext) = split ('[.]', $country);
			
                        echo "<tr style=\"background: " . $bg . ";\">\n"
                        . "<td style=\"width: 5%;\" align=\"center\"><img src=\"images/flags/" . $req3->country . "\" alt=\"\" /></td>\n"
                        . "<td style=\"width: 20%;\"><a href=\"index.php?file=Members&amp;op=detail&amp;autor=".urlencode($req3->pseudo)."\"><b>".stripslashes($req1->tag)."".stripslashes($req3->pseudo)."".stripslashes($req1->tag2)."</b></a></td>\n";
                        
                        if ($nuked['user_email'] == 'on')
                  {
                        echo "<td align=\"center\">\n";

                        if ($req3->email != "" && $visiteur >= $nuked['user_social_level'])
                        {
                            echo "<a href=\"mailto:" . $req3->email . "\"><img style=\"border: 0;\" src=\"" . $img . "\" alt=\"\" title=\"" . $req3->email . "\" /></a></td>";
                        } 
                        else
                        {
                            echo "<img style=\"border: 0;\" src=\"images/user/emailna.png\" alt=\"\"/></td>";
                        } 
                  }
                        if ($nuked['user_icq'] == 'on')
                  {
                        echo "<td align=\"center\">\n";

                        if ($req3->icq != "" && $visiteur >= $nuked['user_social_level'])
                        {
                            echo "<a href=\"http://web.icq.com/whitepages/add_me?uin=" . $req3->icq . "&amp;action=add\"><img style=\"border: 0;\" src=\"images/user/icq.png\" alt=\"\" title=\"" . $req3->icq . "\" /></a></td>";
                        } 
                        else{
                        echo "<img style=\"border: 0;\" src=\"images/user/icqna.png\" alt=\"\"/></td>";
                        } 
                  }
                        if ($nuked['user_msn'] == 'on')
                  {
                        echo "<td align=\"center\">\n";

                        if ($req3->msn != "" && $visiteur >= $nuked['user_social_level'])
                        {
                            echo "<a href=\"mailto:" . $req3->msn . "\"><img style=\"border: 0;\" src=\"images/user/msn.png\" alt=\"\" title=\"" . $msn . "\" /></a></td>";
                        } 
                        else{
                            echo "<img style=\"border: 0;\" src=\"images/user/msnna.png\" alt=\"\"/></td>";
                        } 
                  }
                        if ($nuked['user_aim'] == 'on')
                  {
                        echo "<td align=\"center\">\n";

                        if ($req3->aim != "" && $visiteur >= $nuked['user_social_level'])
                        {
                            echo "<a href=\"aim:goim?screenname=" . $req3->aim . "&amp;message=Hi+" . $req3->aim . "+Are+you+there+?\"><img style=\"border: 0;\" src=\"images/user/aim.png\" alt=\"\" title=\"" . $req3->aim . "\" /></a></td>";
                        } 
                        else{
                            echo "<img style=\"border: 0;\" src=\"images/user/aimna.png\" alt=\"\"/></td>";
                        } 
                  }
                        if ($nuked['user_yim'] == 'on')
                  {
                        echo "<td align=\"center\">\n";

                        if ($req3->yim != "" && $visiteur >= $nuked['user_social_level'])
                        {
                            echo "<a href=\"http://edit.yahoo.com/config/send_webmesg?.target=" . $req3->yim . "&amp;.src=pg\"><img style=\"border: 0;\" src=\"images/user/yahoo.png\" alt=\"\" title=\"" . $req3->yim . "\" /></a></td>";
                        } 
                        else{
                            echo "<img style=\"border: 0;\" src=\"images/user/yahoona.png\" alt=\"\"/></td>";
                        } 
                  }
                        if ($nuked['user_xfire'] == 'on')
                  {
                        echo "<td align=\"center\">\n";

                        if ($req3->xfire != "" && $visiteur >= $nuked['user_social_level'])
                        {
                            echo "<a href=\"xfire:add_friend?user=" . $req3->xfire . "\"><img style=\"border: 0;\" src=\"images/user/xfire.png\" alt=\"\" title=\"" . $req3->xfire . "\" /></a></td>";
                        } 
                        else{
                            echo "<img style=\"border: 0;\" src=\"images/user/xfirena.png\" alt=\"\"/></td>";
                        } 
                  }
                        if ($nuked['user_facebook'] == 'on')
                  {
                        echo "<td align=\"center\">\n";

                        if ($req3->facebook != "" && $visiteur >= $nuked['user_social_level'])
                        {
                            echo "<a href=\"http://www.facebook.com/" . $req3->facebook . "\"><img style=\"border: 0;\" src=\"images/user/facebook.png\" alt=\"\" title=\"" . $req3->facebook . "\" /></a></td>";
                        } 
                        else{
                            echo "<img style=\"border: 0;\" src=\"images/user/facebookna.png\" alt=\"\"/></td>";
                        } 
                  }
                        if ($nuked['user_origin'] == 'on')
                  {
                        echo "<td align=\"center\">\n";

                        if ($req3->origin != "" && $visiteur >= $nuked['user_social_level'])
                        {
                            echo "<img style=\"border: 0;\" src=\"images/user/origin.png\" alt=\"\" title=\"" . $req3->origin . "\" /></td>";
                        } 
                        else{
                            echo "<img style=\"border: 0;\" src=\"images/user/originna.png\" alt=\"\"/></td>";
                        } 
                  }
                        if ($nuked['user_steam'] == 'on')
                  {
                        echo "<td align=\"center\">\n";

                        if ($req3->steam != "" && $visiteur >= $nuked['user_social_level'])
                        {
                            echo "<a href=\"http://steamcommunity.com/actions/AddFriend/" . $req3->steam . "\"><img style=\"border: 0;\" src=\"images/user/steam.png\" alt=\"\" title=\"" . $req3->steam . "\" /></a></td>";
                        } 
                        else{
                            echo "<img style=\"border: 0;\" src=\"images/user/steamna.png\" alt=\"\"/></td>";
                        } 
                  }
                        if ($nuked['user_twitter'] == 'on')
                  {
                        echo "<td align=\"center\">\n";

                        if ($req3->twitter != "" && $visiteur >= $nuked['user_social_level'])
                        {
                            echo "<a href=\"http://twitter.com/#!/" . $req3->twitter . "\"><img style=\"border: 0;\" src=\"images/user/twitter.png\" alt=\"\" title=\"" . $req3->twitter . "\" /></a></td>";
                        } 
                        else{
                            echo "<img style=\"border: 0;\" src=\"images/user/twitterna.png\" alt=\"\"/></td>";
                        } 
                  }
                        if ($nuked['user_skype'] == 'on')
                  {
                        echo "<td align=\"center\">\n";

                        if ($req3->skype != "" && $visiteur >= $nuked['user_social_level'])
                        {
                            echo "<a href=\"skype:" . $req3->skype . "?call\"><img style=\"border: 0;\" src=\"images/user/skype.png\" alt=\"\" title=\"" . $req3->skype . "\" /></a></td>";
                        } 
                        else{
                            echo "<img style=\"border: 0;\" src=\"images/user/skypena.png\" alt=\"\"/></td>";
                        } 
                  }
                  						
						echo"<td style=\"width: 20%;\" align=\"center\">\n";
						
						$nom = $req6->nom;
						$nom = stripslashes($nom);
						
						if ($req6->nom != "" && $req6->ordre >= 0)
                        {
                        echo "" . $nom . "\n";
						} 
                        else
                        {
                            echo "N/A";
                        } 
                     } 
                

                echo "</td></tr></table><tr><td align=\"right\"><a href=\"index.php?file=Equipe&amp;op=view_cat&amp;cat_id=".$req1->id."\">Voir détail de la team</a>&nbsp;</td></tr></table><br />\n";
                $j = 0;

            } 
        closetable();
    } 
	
	function view_cat($cat_id) {
	global $bgcolor1, $bgcolor2, $bgcolor3, $theme, $nuked;
	
    opentable();

	  $sql1=mysql_query('SELECT * FROM '.$nuked['prefix'].'_staff_cat WHERE id="'.$cat_id.'"');
	  while($req1 = mysql_fetch_object($sql1))
	  {
	    if ($req1->img != 'non') $img_url = '<img src="'.$req1->img.'" alt="" style="border:none;" title="'.$req1->nom.'" />';
		else $img_url = ''.$req1->nom.'';
		
		echo'<table width="100%">'
		.'<tr><td>'.$img_url.'</td></tr>'
		.'<tr><td>&nbsp;</td></tr>'
		.'<tr><td>';
		
		$ii=1;
		echo'<table cellpadding="0" cellspacing="0" width="100%" height="100%"><tr>';
		
		  $sql2=mysql_query('SELECT * FROM '.$nuked['prefix'].'_staff WHERE categorie_id="'.$req1->id.'"');
		  while($req2 = mysql_fetch_object($sql2))
		  {
		    $sql3=mysql_query('SELECT * FROM '.$nuked['prefix'].'_users WHERE id="'.$req2->membre_id.'"');
		    $req3 = mysql_fetch_object($sql3);
			
			$sql4=mysql_query('SELECT * FROM '.$nuked['prefix'].'_users_detail WHERE user_id="'.$req2->membre_id.'"');
		    $req4 = mysql_fetch_object($sql4);
			
			$sql5=mysql_query('SELECT * FROM '.$nuked['prefix'].'_staff_status WHERE id="'.$req2->status_id.'"');
		    $req5 = mysql_fetch_object($sql5);
			
			$sql6=mysql_query('SELECT * FROM '.$nuked['prefix'].'_staff_rang WHERE id="'.$req2->rang_id.'"');
		    $req6 = mysql_fetch_object($sql6);
			
			$photos_membre = ($req4->photo != "") ? '<a href="index.php?file=Members&amp;op=detail&amp;autor='.urlencode($req3->pseudo).'"><img src="'.$req4->photo.'" width="100" height="100" style="float:left;margin:5px;border:none;" alt="" /></a>' : '<a href="index.php?file=Members&amp;op=detail&amp;autor='.urlencode($req3->pseudo).'"><img src="modules/User/images/noavatar.png" width="100" height="100" style="float:left;margin:5px;border:none;" alt="" /></a>';
			
	        $age1=$req4->age;
            $age = explode('/', $age);
            
            if ($age1 != "")
            {
                list ($jour, $mois, $an) = split ('[/]', $age1);
                $age = date("Y") - $an;
                if (date("m") < $mois)
                {
                    $age = $age-1;
                } 
                if (date("d") < $jour && date("m") == $mois)
                {
                    $age = $age - 1;
                } 
            } 
            else
            {
                $age = "N/A";
            } 
			
			$age = ($age != 'N/A') ? $age.' ans' : 'N/A';
			$membre_ville = ($req4->ville != '') ? $req4->ville : 'N/A';
			
			$prenom = ($req4->prenom != '') ? $req4->prenom : 'N/A';
			
		    echo'<td style="width:50%;"><table style="background: ' . $bgcolor2 . ';border: 1px solid ' . $bgcolor3 . ';" width="100%" cellpadding="2" cellspacing="1">
			<tr><td style="background-color:'.$bgcolor3.';width:120px;">'.$photos_membre.'</td>
			<td style="background-color:'.$bgcolor1.';padding:5px;border-left: 1px solid '.$bgcolor3.';" width="100%">
			<table cellpadding="0" cellspacing="0" style="" width="100%">
			<tr><td style="text-align:center;" width="100%"><a href="index.php?file=Members&amp;op=detail&amp;autor='.urlencode($req3->pseudo).'"><b>'.stripslashes($req1->tag).''.stripslashes($req3->pseudo).''.stripslashes($req1->tag2).'</b></a></td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td width="100%">&raquo; Prenom : <b>'.stripslashes($prenom).'</b><br />&raquo; Age : <b>'.$age.'</b><br />&raquo; Ville : <b>'. $membre_ville.'</b><br />&raquo; Status : <b>'.$req5->nom.'</b><br />&raquo; Rang : <b>'.$req6->nom.'</b></td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td style="text-align:center;" width="100%"><a href="index.php?file=Members&amp;op=detail&amp;autor='.urlencode($req3->pseudo).'"><b>Voir le profil</b></a></td></tr></table>
			</td></tr></table></td>';
			
			if($ii == 2)
			{
			  echo'</tr><tr><td>&nbsp;</td></tr><tr>';
			  $ii=0;
			}
			else
			{
			  echo'<td>&nbsp;</td>';
			}
			
			$ii++;
			$aze_id=$req2->categorie_id;
		  }
		  
		if($req1->id == $aze_id)
		{
		  if($ii == 2)
		  {
		    echo'<td style="width:50%;"></td>';
		  }
		  else
		  {
		    echo'<td></td>';
		  }
		}
		else
		{
		  echo'<td style="text-align:center" width="100%">Aucun membre dans cette catégorie.</td>';
		}
		  
		echo'</tr></table>';
		
		echo '</td></tr>';
		
		echo'</table>';
		
		echo'<div style="text-align: center;"><br />[ <a href="index.php?file=Equipe"><b>Retour</b></a> ]</div>';
	  }

    closetable();
	}

    switch($_REQUEST['op'])
    {
        case"index":
            index();
            break;

        case"view_cat":
            view_cat($_REQUEST['cat_id']);
            break;

        default:
            index();
    } 

} 
else if ($level_access == -1)
{
    opentable();
    echo "<br /><br /><div style=\"text-align: center;\">" . _MODULEOFF . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />";
    closetable();
} 
else if ($level_access == 1 && $visiteur == 0)
{
    opentable();
    echo "<br /><br /><div style=\"text-align: center;\">" . _USERENTRANCE . "<br /><br /><b><a href=\"index.php?file=User&amp;op=login_screen\">" . _LOGINUSER . "</a> | "
    . "<a href=\"index.php?file=User&amp;op=reg_screen\">" . _REGISTERUSER . "</a></b></div><br /><br />";
    closetable();
} 
else
{
    opentable();
    echo "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />";
    closetable();
} 

?>