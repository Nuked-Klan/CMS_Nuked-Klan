<?php 
/**
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
if (eregi("blok.php", $_SERVER['PHP_SELF']))
{
    die ("You cannot open this page directly");
} 

global $nuked;

  $sql1=mysql_query('SELECT * FROM '.$nuked['prefix'].'_staff ORDER BY rand() LIMIT 1');
  $req1 = mysql_fetch_object($sql1);
  
  $sql2=mysql_query('SELECT * FROM '.$nuked['prefix'].'_users WHERE id="'.$req1->membre_id.'"');
  $req2 = mysql_fetch_object($sql2);

  $sql3=mysql_query('SELECT * FROM '.$nuked['prefix'].'_users_detail WHERE user_id="'.$req1->membre_id.'"');
  $req3 = mysql_fetch_object($sql3);
  
  $sql4=mysql_query('SELECT * FROM '.$nuked['prefix'].'_staff_status WHERE id="'.$req1->status_id.'"');
  $req4 = mysql_fetch_object($sql4);
  
  $sql5=mysql_query('SELECT * FROM '.$nuked['prefix'].'_staff_rang WHERE id="'.$req1->rang_id.'"');
  $req5 = mysql_fetch_object($sql5);
  
  $photos_membre = ($req3->photo != "") ? '<a href="index.php?file=Members&amp;op=detail&amp;autor='.urlencode($req2->pseudo).'"><img src="'.$req3->photo.'" width="100" style=";margin:5px;border:none;" /></a>' : '<a href="index.php?file=Members&amp;op=detail&amp;autor='.urlencode($req2->pseudo).'"><img src="modules/User/images/noavatar.png" width="100" style=";margin:5px;border:none;" /></a>';

  $age1=$req3->age;
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
  $membre_ville = ($req3->ville != '') ? $req3->ville : 'N/A';

		    echo'<p align="center">'.$photos_membre.'<br />Pseudo : '.stripslashes($req2->pseudo).'<br />Age : '.$age.'<br />Ville : '. $membre_ville.'<br />Status : '.$req4->nom.'<br />Rang : '.$req5->nom.'<br /><br /><a href="index.php?file=Members&amp;op=detail&amp;autor='.urlencode($req2->pseudo).'"><b>[ + D\'INFOS ]</b></a></p>';
?>