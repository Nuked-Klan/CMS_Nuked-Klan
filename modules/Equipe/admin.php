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

global $user;
$visiteur = (!$user) ? 0 : $user[1];

include("modules/Admin/design.php");

$ModName = basename(dirname(__FILE__));
$level_admin = admin_mod($ModName);
if ($visiteur >= $level_admin && $level_admin > -1)
{
    function main() {
	global $bgcolor4, $bgcolor1, $bgcolor2, $nuked;
	  admintop();

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
	. "<div class=\"content-box-header\"><h3>Gestion des Equipes</h3>\n"
    . "<div style=\"text-align:right;\">\n"
	. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" />\n"
	. "</div></div>\n"
	. "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\">\n"
	. "	Equipe<b> |\n" 
	. "	<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=add_staff\">Ajouter un membre</a> |\n"
	. "	<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=gestion_cat\">Gestion des catégories</a> |\n" 
	. "	<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=gestion_status\">Gestion des Status</a> |\n"
	. "	<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=gestion_rang\">Gestion des Rang</a></b><br />\n" 
	. "<br /><table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"70%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\"><tr>\n"
	. "<td style=\"text-align: center;\"><b>Pseudo</b></td>\n"
	. "<td style=\"text-align: center;\"><b>Date d'arrivé</b></td>\n"
	. "<td style=\"text-align: center;\"><b>Catégorie</b></td>\n"
	. " <td style=\"text-align: center;\"><b>Editer</b></td>\n"
	. "<td style=\"text-align: center;\"><b>Supprimer</b></td></tr>";
	  
	  $oo=1;
	  $sql1=mysql_query('SELECT * FROM '.$nuked['prefix'].'_staff ORDER by date DESC');
	  while($req1 = mysql_fetch_object($sql1))
	  {
	    $sql2=mysql_query('SELECT * FROM '.$nuked['prefix'].'_users WHERE id="'.$req1->membre_id.'"');
	    $req2 = mysql_fetch_object($sql2);
	  
	    $sql3=mysql_query('SELECT * FROM '.$nuked['prefix'].'_staff_cat WHERE id="'.$req1->categorie_id.'"');
	    $req3 = mysql_fetch_object($sql3);
	  
	    $color_font = ($oo == 1) ? $bgcolor2 : $bgcolor1;
	  
	    echo'<tr>
		<td style="text-align: center;">'.$req2->pseudo.'</td>
		<td style="text-align: center;">'.date('d.m.y', $req1->date).'</td>
		<td style="text-align: center;">'.stripslashes($req3->nom).'</td>
		<td style="text-align: center;"><a href="index.php?file=Equipe&amp;page=admin&amp;op=modif_staff&amp;staff_id_get='.$req1->id.'"><img src="images/edit.gif" alt="" style="border:none;" /></a></td>
		<td style="text-align: center;"><a href="index.php?file=Equipe&amp;page=admin&amp;op=dell_staff&amp;membre_id='.$req2->id.'&amp;cat_id='.$req1->categorie_id.'"><img src="images/del.gif" alt="" style="border:none;" /></a></td>
		</tr>';
		
		$oo = ($oo == 2) ? 0 : $oo;
		$oo++;
	  }
		
	  echo'</tr></table><div style="text-align: center;"><br />[ <a href="index.php?file=Admin"><b>Retour</b></a> ]</div></div></div><br />';
	  
	  adminfoot();
    } 
	
	function add_staff() {
	global $nuked;
	  admintop();
	  
	  echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
	. "<div class=\"content-box-header\"><h3>Ajouter un Membre</h3>\n"
    . "<div style=\"text-align:right;\">\n"
	. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" />\n"
	. "</div></div>\n"
	. "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\">\n"
		. "<b><a href=\"index.php?file=Equipe&amp;page=admin\">Equipe</a> | </b>\n"
		. "Ajouter un membre<b> | \n"
		. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=gestion_cat\">Gestion des catégories</a> | \n"
		. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=gestion_status\">Gestion des Status</a> | \n"
		. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=gestion_rang\">Gestion des Rang</a>\n" 
		. "</b></div><br />";
		
	  echo'<form action="index.php?file=Equipe&amp;page=admin&amp;op=save_staff" method="post"><table align="center"><tr><td>Pseudo :</td><td><select name="membre_id">';
		
	    $sql1=mysql_query('SELECT * FROM '.$nuked['prefix'].'_users WHERE niveau>="2"');
	    while($req1 = mysql_fetch_object($sql1))
		{
		  echo'<option value="'.$req1->id.'">'.stripslashes($req1->pseudo).'</option>';
		}
		
		echo'</select></td></tr><tr><td>Catégorie :</td><td><select name="categorie">';

	    $sql2=mysql_query('SELECT * FROM '.$nuked['prefix'].'_staff_cat');
	    while($req2 = mysql_fetch_object($sql2))
		{
		  echo'<option value="'.$req2->id.'">'.stripslashes($req2->nom).'</option>';
		}
		
		echo'</select></td></tr><tr><td>Status :</td><td><select name="status">';

	    $sql3=mysql_query('SELECT * FROM '.$nuked['prefix'].'_staff_status');
	    while($req3 = mysql_fetch_object($sql3))
		{
		  echo'<option value="'.$req3->id.'">'.stripslashes($req3->nom).'</option>';
		}
		
		echo'</select></td></tr><tr><td>Rang :</td><td><select name="rang">';

	    $sql4=mysql_query('SELECT * FROM '.$nuked['prefix'].'_staff_rang');
	    while($req4 = mysql_fetch_object($sql4))
		{
		  echo'<option value="'.$req4->id.'">'.stripslashes($req4->nom).'</option>';
		}
		
	  echo'</select></td></tr><tr><td colspan="2" style="text-align:center;"><input type="submit" value="Envoyer" /></td></tr></table></form>';
		
	  echo'<div style="text-align: center;"><br />[ <a href="index.php?file=Admin"><b>Retour</b></a> ]</div><br /></div></div>';
		
	  adminfoot();
	}
	
	function save_staff() {
	global $nuked;
	  admintop();

	  extract($_POST);
	  if(!empty($membre_id) && !empty($categorie) && !empty($status) && !empty($rang))
	  {
		$verification = mysql_query('SELECT COUNT(*) FROM '.$nuked['prefix'].'_staff WHERE membre_id="'.$membre_id.'" && categorie_id="'.$categorie.'"') or die (mysql_error());
		$utilise = mysql_fetch_array($verification);
		
		if($utilise['COUNT(*)'] >= 1)
		{
		 echo "<div class=\"notification error png_bg\">\n"
	  . "<div>\n"
	  . "<b>Une erreur ses produite : Ce membre figure déja dans cette catégorie.</b> !!!"
	  . "</div>\n"
      . "</div>\n";
	  redirect("index.php?file=Equipe&page=admin&op=add_staff", 2); 
		}
		else
		{
	      mysql_query('INSERT into '.$nuked['prefix'].'_staff (membre_id, categorie_id, date, status_id, rang_id) VALUES ("'.$membre_id.'","'.$categorie.'","'.time().'","'.$status.'","'.$rang.'")');
		  echo "<div class=\"notification success png_bg\">\n"
		. "<div>\n"
		. "<b>Membre ajouté dans le staff avec succes.</b>\n"
		. "</div>\n"
		. "</div>\n";
		 redirect("index.php?file=Equipe&page=admin", 2);
		}
	  }
	  else
	  {
       
	  echo "<div class=\"notification error png_bg\">\n"
	  . "<div>\n"
	  . "<b>Une erreur ses produite : Vous n'avez pas remplis tout les champs.</b> !!!"
	  . "</div>\n"
      . "</div>\n";
	  redirect("index.php?file=Equipe&page=admin&op=add_staff", 2);

    
	  }
	
	  adminfoot();
	}
	
	function dell_staff($membre_id_get, $cat_id_get) {
	global $nuked;
	  admintop();
	 
	    mysql_query('DELETE FROM '.$nuked['prefix'].'_staff WHERE membre_id="'.$membre_id_get.'" && categorie_id="'.$cat_id_get.'"');
		echo "<div class=\"notification success png_bg\">\n"
		. "<div>\n"
		. "<b>Membre supprimé du staff avec succes.</b>\n"
		. "</div>\n"
		. "</div>\n";
		redirect("index.php?file=Equipe&page=admin", 2);
	 
	  adminfoot();
	}
	
	function modif_staff($staff_id_get) {
	global $nuked;
	  admintop();
	  
	echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
	. "<div class=\"content-box-header\"><h3>Editer un membre</h3>\n"
    . "<div style=\"text-align:right;\">\n"
	. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" />\n"
	. "</div></div>\n"
	. "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\">\n"
		. "<b><a href=\"index.php?file=Equipe&amp;page=admin\">Equipe</a> | \n"
		. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=add_staff\">Ajouter un membre</a> | \n"
		. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=gestion_cat\">Gestion des catégories</a> | \n"
		. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=gestion_status\">Gestion des Status</a>\n"
		. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=gestion_rang\">Gestion des Rang</a>\n" 
		. "</b></div><br />";
		
	  echo'<form action="index.php?file=Equipe&amp;page=admin&amp;op=save_modif_staff&amp;staff_id_get='.$staff_id_get.'" method="post"><table align="center"><tr><td>Pseudo :</td><td><select name="membre_id">';
	  
	  $sql1=mysql_query('SELECT * FROM '.$nuked['prefix'].'_staff WHERE id='.$staff_id_get);
	  $req1 = mysql_fetch_object($sql1);
		
	    $sql2=mysql_query('SELECT * FROM '.$nuked['prefix'].'_users WHERE niveau>="2"');
	    while($req2 = mysql_fetch_object($sql2))
		{
		  $selected = ($req1->membre_id == $req2->id) ? 'selected="selected"' : '';
		  echo'<option value="'.$req2->id.'" '.$selected.'>'.stripslashes($req2->pseudo).'</option>';
		}
		
		echo'</select></td></tr><tr><td>Catégorie :</td><td><select name="categorie">';

	    $sql3=mysql_query('SELECT * FROM '.$nuked['prefix'].'_staff_cat');
	    while($req3 = mysql_fetch_object($sql3))
		{
		  $selected2 = ($req1->categorie_id == $req3->id) ? 'selected="selected"' : '';
		  echo'<option value="'.$req3->id.'" '.$selected2.'>'.stripslashes($req3->nom).'</option>';
		}
		
		echo'</select></td></tr><tr><td>Status :</td><td><select name="status">';

	    $sql4=mysql_query('SELECT * FROM '.$nuked['prefix'].'_staff_status');
	    while($req4 = mysql_fetch_object($sql4))
		{
		  $selected3 = ($req1->status_id == $req4->id) ? 'selected="selected"' : '';
		  echo'<option value="'.$req4->id.'" '.$selected3.'>'.stripslashes($req4->nom).'</option>';
		}
		
		echo'</select></td></tr><tr><td>Rang :</td><td><select name="rang">';

	    $sql5=mysql_query('SELECT * FROM '.$nuked['prefix'].'_staff_rang');
	    while($req5 = mysql_fetch_object($sql5))
		{
		  $selected4 = ($req1->rang_id == $req5->id) ? 'selected="selected"' : '';
		  echo'<option value="'.$req5->id.'" '.$selected4.'>'.stripslashes($req5->nom).'</option>';
		}
		
	  echo'</select></td></tr><tr><td colspan="2" style="text-align:center;"><input type="submit" value="Modifier" /></td></tr></table></form>';
		
	  echo'<div style="text-align: center;"><br />[ <a href="index.php?file=Equipe&amp;page=admin"><b>Retour</b></a> ]</div><br /></div></div>';
		
	  adminfoot();
	}
	
	function save_modif_staff($staff_id_get) {
	global $nuked;
	  admintop();

	  extract($_POST);
	  if(!empty($membre_id) && !empty($categorie) && !empty($status) && !empty($rang))
	  {
	    $sql1=mysql_query('SELECT * FROM '.$nuked['prefix'].'_staff WHERE id='.$staff_id_get);
		$req1=mysql_fetch_object($sql1);
		
		mysql_query('DELETE FROM '.$nuked['prefix'].'_staff WHERE id='.$staff_id_get);
	 
	    mysql_query('INSERT into '.$nuked['prefix'].'_staff (membre_id, categorie_id, date, status_id, rang_id) VALUES ("'.$membre_id.'","'.$categorie.'","'.$req1->date.'","'.$status.'","'.$rang.'")');
		echo "<div class=\"notification success png_bg\">\n"
		. "<div>\n"
		. "<b>Membre modifié avec succes.</b>\n"
		. "</div>\n"
		. "</div>\n";
		redirect("index.php?file=Equipe&page=admin", 2);
	  }
	  else
	  {
	    mysql_query('INSERT into '.$nuked['prefix'].'_staff (membre_id, categorie_id, date, status_id, rang_id) VALUES ("'.$req1->membre_id.'","'.$req1->categorie_id.'","'.$req1->date.'","'.$req1->status_id.'","'.$req1->rang_id.'")');
	    echo "<div class=\"notification error png_bg\">\n"
	  . "<div>\n"
	  . "<b>Une erreur ses produite : Vous n'avez pas remplis tout les champs.</b> !!!"
	  . "</div>\n"
      . "</div>\n";
	  redirect("index.php?file=Equipe&page=admin&op=modif_staff", 2);

	  }
	
	  adminfoot();
	}
	
	function gestion_cat() {
	global $bgcolor4, $bgcolor1, $bgcolor2, $nuked;;
	  admintop();
	  
	  echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
	. "<div class=\"content-box-header\"><h3>Gestion des Catégories</h3>\n"
    . "<div style=\"text-align:right;\">\n"
	. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" />\n"
	. "</div></div>\n"
	. "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\">\n"
		. "<b><a href=\"index.php?file=Equipe&amp;page=admin\">Equipe</a> | \n"
		. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=add_staff\">Ajouter un membre</a> | </b>\n"
		. "Gestion des catégories<b> | \n"
		. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=gestion_status\">Gestion des Status</a> | \n"
		. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=gestion_rang\">Gestion des Rang</a>\n" 
		. "</b></div><br /><div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Equipe&amp;page=admin&amp;op=add_cat\"><b>Ajouter une catégorie</b></a> ]</div><br />";


		
	  echo"<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"70%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\"><tr>\n"
	  . "<td style=\"text-align: center;\"><b>Catégorie</b></td>\n"
	  . "<td style=\"text-align: center;\"><b>Editer</b></td>\n"
	  . "<td style=\"text-align: center;\"><b>Supprimer</b></td></tr>";
		
	  $oo=1;
	  $sql1=mysql_query('SELECT * FROM '.$nuked['prefix'].'_staff_cat');
	  while($req1 = mysql_fetch_object($sql1))
	  {
	    $color_font = ($oo == 1) ? $bgcolor2 : $bgcolor1;
	  
	    echo'<tr>
		<td style="text-align: center;">'.stripslashes($req1->nom).'</td>
		<td style="text-align: center;"><a href="index.php?file=Equipe&amp;page=admin&amp;op=modif_cat&amp;cat_id='.$req1->id.'"><img src="images/edit.gif" alt="" style="border:none;" /></a></td>
		<td style="text-align: center;"><a href="index.php?file=Equipe&amp;page=admin&amp;op=dell_cat&amp;cat_id='.$req1->id.'"><img src="images/del.gif" alt="" style="border:none;" /></a></td>
		</tr>';
		
		if($oo == 2)
		{
		  $oo=0;
		}
		$oo++;
	  }
		
	  echo'</tr></table><div style="text-align: center;"><br />[ <a href="index.php?file=Admin"><b>Retour</b></a> ]</div><br /></div></div>';
	  
	  adminfoot();
	}
	
	function add_cat() {
	global $nuked;
	  admintop();

     echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
	. "<div class=\"content-box-header\"><h3>Ajouter une Catégorie</h3>\n"
    . "<div style=\"text-align:right;\">\n"
	. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" />\n"
	. "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\">\n"
	. "<b><a href=\"index.php?file=Equipe&amp;page=admin\">Equipe</a> | \n"
	. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=add_staff\">Ajouter un membre</a> | \n"
	. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=gestion_cat\">Gestion des catégories</a> | \n"
	. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=gestion_status\">Gestion des Status</a> | \n"
	. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=gestion_rang\">Gestion des Rang</a>\n" 
	. "</b></div><br />";
		
	  echo'<form action="index.php?file=Equipe&amp;page=admin&amp;op=save_cat" method="post" enctype="multipart/form-data"><table align="center" >'
	  .'<tr><td>Catégorie :</td><td><input type="text" name="nom"></td></tr>'
	  .'<tr><td>Tag Préfixe :</td><td><input type="text" name="tag"></td></tr>'
	  .'<tr><td>Tag Suffixe :</td><td><input type="text" name="tag2"></td></tr>'
	  .'<tr><td>Ordre :</td><td><input type="text" name="ordre"></td></tr>'
	  .'<tr><td>Url de l\'image :</td><td><input type="text" name="url_img" value="http://"></td></tr>'
	  .'<tr><td></td><td>
	  <input type="hidden" name="MAX_FILE_SIZE" value="1048576" />
	  <input type="file" name="cat_img" />
	  </td></tr>'
	  .'<tr><td colspan="2" style="text-align:center;"><input type="submit" value="Ajouter" /></td></tr>'
	  .'</table></form>';
		
	  echo'<div style="text-align: center;"><br />[ <a href="index.php?file=Equipe&amp;page=admin&amp;op=gestion_cat"><b>Retour</b></a> ]</div><br /></div></div>';
		
	  adminfoot();
	}
	
	function save_cat() {
	global $nuked;
	  admintop();
	  
	  extract($_POST);
	  if(!empty($nom))
	  {
	    $url_img =($url_img != '' && $url_img != 'http://') ? $url_img  : 'non';
		
		if (!empty($_FILES['cat_img']['size']))
		{
		  $cat_img_name = $_FILES['cat_img']['name'];
		  $cat_img_tmpname = $_FILES['cat_img']['tmp_name'];
		 
		  $ext = substr(strrchr($cat_img_name, '.') ,1);
		  
		  if($ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'png')
		  {
		  $url_img='upload/Equipe/' . time() . '.' . $ext;
		 
		  move_uploaded_file($cat_img_tmpname, $url_img);
		 
		  @chmod ($url_img, 0644);
		  }
		  else
		  {
		 echo "<div class=\"notification error png_bg\">\n"
	     . "<div>\n"
	     . "<b>Une erreur ses produite : L\'image est dans un format autre que .jpg, .jpeg, .gif, .png.</b>"
	     . "</div>\n"
         . "</div>\n";
	     redirect("index.php?file=Equipe&amp;page=admin&amp;op=add_cat", 2);
		 $url_img='non';
		  }
		}
		
	    mysql_query('INSERT into '.$nuked['prefix'].'_staff_cat (nom, img, ordre, tag, tag2) VALUES ("'.addslashes($nom).'","'.$url_img.'","'.$ordre.'","'.addslashes($tag).'","'.addslashes($tag2).'")');
		echo "<div class=\"notification success png_bg\">\n"
		. "<div>\n"
		. "<b>Catégorie ajouté avec succes.</b>\n"
		. "</div>\n"
		. "</div>\n";
		redirect("index.php?file=Equipe&page=admin&op=gestion_cat", 2);
	  }
	  else
	  {
	  	 echo "<div class=\"notification error png_bg\">\n"
	     . "<div>\n"
	     . "<b>Une erreur ses produite : Vous n'avez pas remplis tout les champs.</b>"
	     . "</div>\n"
         . "</div>\n";
	     redirect("index.php?file=Equipe&amp;page=admin&amp;op=add_cat", 2);
	  }
	
	  adminfoot();
	}
	
	function dell_cat($cat_id_get) {
	global $nuked;
	  admintop();
	 
	    mysql_query('DELETE FROM '.$nuked['prefix'].'_staff_cat WHERE id="'.$cat_id_get.'"');
        echo "<div class=\"notification success png_bg\">\n"
		. "<div>\n"
		. "<b>Catégorie supprimé avec succes.</b>\n"
		. "</div>\n"
		. "</div>\n";
		redirect("index.php?file=Equipe&page=admin&op=gestion_cat", 2);
	 
	  adminfoot();
	}
	
	function modif_cat($cat_id_get) {
	global $nuked;
	  admintop();
	  
	  
     echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
	. "<div class=\"content-box-header\"><h3>Editer une Catégorie</h3>\n"
    . "<div style=\"text-align:right;\">\n"
	. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" />\n"
	. "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\">\n"
	. "<b><a href=\"index.php?file=Equipe&amp;page=admin\">Equipe</a> | \n"
	. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=add_staff\">Ajouter un membre</a> | \n"
	. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=gestion_cat\">Gestion des catégories</a> | \n"
	. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=gestion_status\">Gestion des Status</a> | \n"
	. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=gestion_rang\">Gestion des Rang</a>\n" 
	. "</b></div><br />";
		
	  $sql1=mysql_query('SELECT * FROM '.$nuked['prefix'].'_staff_cat WHERE id="'.$cat_id_get.'"');
	  $req1 = mysql_fetch_object($sql1);
		
	  echo'<form action="index.php?file=Equipe&amp;page=admin&amp;op=save_modif_cat&amp;cat_id='.$cat_id_get.'" method="post" enctype="multipart/form-data"><table align="center">'
	  .'<tr><td>Catégorie :</td><td><input type="text" name="nom" value="'.stripslashes($req1->nom).'" /></td></tr>'
	  .'<tr><td>Url de l\'image :</td><td><input type="text" name="url_img" value="'.$req1->img.'" /></td></tr>'
	  .'<tr><td>Ordre :</td><td><input type="text" name="ordre" value="'.$req1->ordre.'" /></td></tr>'
	  .'<tr><td>Tag Préfixe :</td><td><input type="text" name="tag" value="'.stripslashes($req1->tag).'"></td></tr>'
	  .'<tr><td>Tag Suffixe :</td><td><input type="text" name="tag2" value="'.stripslashes($req1->tag2).'"></td></tr>'
	  .'<tr><td></td><td>
	  <input type="hidden" name="MAX_FILE_SIZE" value="1048576" />
	  <input type="file" name="cat_img" />
	  </td></tr>'
	  .'<tr><td colspan="2" style="text-align:center;"><input type="submit" value="Modifier" /></td></tr>'
	  .'</table></form>';
		
	  echo'<div style="text-align: center;"><br />[ <a href="index.php?file=Equipe&amp;page=admin&amp;op=gestion_cat"><b>Retour</b></a> ]</div><br /></div></div>';
		
	  adminfoot();
	}
	
	function save_modif_cat($cat_id_get) {
	global $nuked;
	  admintop();

	  extract($_POST);
	  if(!empty($nom))
	  {
	    $url_img =($url_img != '' && $url_img != 'http://') ? $url_img  : 'non';
		
		if (!empty($_FILES['cat_img']['size']))
		{
		  $cat_img_name = $_FILES['cat_img']['name'];
		  $cat_img_tmpname = $_FILES['cat_img']['tmp_name'];
		 
		  $ext = substr(strrchr($cat_img_name, '.') ,1);
		  
		  if($ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'png')
		  {
		  $url_img='upload/Equipe/' . time() . '.' . $ext;
		 
		  move_uploaded_file($cat_img_tmpname, $url_img);
		 
		  @chmod ($url_img, 0644);
		  }
		  else
		  {
            echo "<div class=\"notification error png_bg\">\n"
	     . "<div>\n"
	     . "<b>Une erreur ses produite : L\'image est dans un format autre que .jpg, .jpeg, .gif, .png.</b>"
	     . "</div>\n"
         . "</div>\n";
	     redirect("index.php?file=Equipe&amp;page=admin&amp;op=add_cat", 2);
		 $url_img='non';
		  }
		}
		
	    mysql_query('UPDATE '.$nuked['prefix'].'_staff_cat SET  nom="'.addslashes($nom).'", img="'.$url_img.'",ordre="'.$ordre.'",tag="'.addslashes($tag).'",tag2="'.addslashes($tag2).'" WHERE id='.$cat_id_get.'');
		echo "<div class=\"notification success png_bg\">\n"
		. "<div>\n"
		. "<b>Catégorie ajouté avec succes.</b>\n"
		. "</div>\n"
		. "</div>\n";
		redirect("index.php?file=Equipe&page=admin&op=gestion_cat", 2);
	   }
	   else
	   {
	  echo "<div class=\"notification error png_bg\">\n"
	     . "<div>\n"
	     . "<b>Une erreur ses produite : Vous n'avez pas remplis tout les champs.</b>"
	     . "</div>\n"
         . "</div>\n";
	     redirect("index.php?file=Equipe&amp;page=admin&amp;op=gestion_cat", 2);
	  }
	
	  adminfoot();
	}
	
	function gestion_status() {
	global $bgcolor4, $bgcolor1, $bgcolor2, $nuked;
	  admintop();
	  
	echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
	. "<div class=\"content-box-header\"><h3>Gestion des Status </h3>\n"
    . "<div style=\"text-align:right;\">\n"
	. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" />\n"
	. "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\">\n"
	. "<b><a href=\"index.php?file=Equipe&amp;page=admin\">Equipe</a> | \n"
	. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=add_staff\">Ajouter un membre</a> | \n"
	. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=gestion_cat\">Gestion des catégories</a> | \n"
	. "</b>Gestion des Status<b> | \n"
	. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=gestion_rang\">Gestion des Rang</a><br />\n" 
	. "</b></div><div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Equipe&amp;page=admin&amp;op=add_status\"><b>Ajouter un Status</b></a> ]</div><br />";
		
	  echo"<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"70%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\"><tr>\n"
	  . "<td style=\"text-align: center;\"><b>Status</b></td>\n"
	  . "<td style=\"text-align: center;\"><b>Editer</b></td>\n"
	  . "<td style=\"text-align: center;\"><b>Supprimer</b></td></tr>";
		
	  $oo=1;
	  $sql1=mysql_query('SELECT * FROM '.$nuked['prefix'].'_staff_status');
	  while($req1 = mysql_fetch_object($sql1))
	  {
	    $color_font = ($oo == 1) ? $bgcolor2 : $bgcolor1;
		
	    echo"<tr>\n"
		. "<td style=\"text-align: center;\">".stripslashes($req1->nom)."</td>\n"
		. "<td style=\"text-align: center;\"><a href=\"index.php?file=Equipe&amp;page=admin&amp;op=modif_status&amp;status_id=".$req1->id."\"><img src=\"images/edit.gif\" alt=\"\" style=\"border:none;\" /></a></td>\n"
		. "<td style=\"text-align: center;\"><a href=\"index.php?file=Equipe&amp;page=admin&amp;op=dell_status&amp;status_id=".$req1->id."\"><img src=\"images/del.gif\" alt=\"\" style=\"border:none;\" /></a></td>\n"
		. "<td style=\"text-align: center;\"></tr>";
		
		$oo = ($oo == 2) ? 0 : $oo;
		$oo++;
	  }
		
	  echo'</tr></table><div style="text-align: center;"><br />[ <a href="index.php?file=Admin"><b>Retour</b></a> ]</div><br /></div></div>';
	  
	  adminfoot();
	}
	
	function add_status() {
	global $nuked;
	  admintop();

      echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
	. "<div class=\"content-box-header\"><h3>Ajouter un Status</h3>\n"
    . "<div style=\"text-align:right;\">\n"
	. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" />\n"
	. "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\">\n"
	. "<b><a href=\"index.php?file=Equipe&amp;page=admin\">Equipe</a> | \n"
	. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=add_staff\">Ajouter un membre</a> | \n"
	. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=gestion_cat\">Gestion des catégories</a> | \n"
	. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=gestion_status\">Gestion des Status</a> | \n"
	. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=gestion_rang\">Gestion des Rang</a>\n" 
	. "</b></div><br />";
	  
	  echo'<form action="index.php?file=Equipe&amp;page=admin&amp;op=save_status" method="post"><table align="center" >'
	  .'<tr><td>Status :</td><td><input type="text" name="nom" maxlength="25" /></td></tr>'
	  .'<tr><td colspan="2" style="text-align:center;"><input type="submit" value="Ajouter" /></td></tr>'
	  .'</table></form>';
		
	  echo'<div style="text-align: center;"><br />[ <a href="index.php?file=Equipe&amp;page=admin&amp;op=gestion_status"><b>Retour</b></a> ]</div><br /></div></div>';
		
	  adminfoot();
	}
	
	function save_status() {
	global $nuked;
	  admintop();

	  extract($_POST);
	  if(!empty($nom))
	  {
		$verification = mysql_query('SELECT COUNT(*) FROM '.$nuked['prefix'].'_staff WHERE membre_id="'.$membre_id.'" && categorie_id="'.$categorie.'"') or die (mysql_error());
		$utilise = mysql_fetch_array($verification);
		
		if($utilise['COUNT(*)'] >= 1)
		{
		 echo "<div class=\"notification error png_bg\">\n"
	     . "<div>\n"
	     . "<b>Une erreur ses produite : Ce status figure déja dans la base de données.</b>"
	     . "</div>\n"
         . "</div>\n";
	     redirect("index.php?file=Equipe&amp;page=admin&amp;op=add_status", 2);
		 }
		else
		{
	      mysql_query('INSERT into '.$nuked['prefix'].'_staff_status (nom) VALUES ("'.addslashes($nom).'")');
		  echo "<div class=\"notification success png_bg\">\n"
		. "<div>\n"
		. "<b>Status ajouté avec succes.</b>\n"
		. "</div>\n"
		. "</div>\n";
		redirect("index.php?file=Equipe&page=admin&op=gestion_status", 2);
		}
	  }
	  else
	  {
	    echo "<div class=\"notification error png_bg\">\n"
	     . "<div>\n"
	     . "<b>Une erreur ses produite : Vous n'avez pas remplis tout les champs.</b>"
	     . "</div>\n"
         . "</div>\n";
	     redirect("index.php?file=Equipe&amp;page=admin&amp;op=add_status", 2);
	  }
	
	  adminfoot();
	}
	
	function dell_status($status_id_get) {
	global $nuked;
	  admintop();
	 
	    mysql_query('DELETE FROM '.$nuked['prefix'].'_staff_status WHERE id="'.$status_id_get.'"');
		echo "<div class=\"notification success png_bg\">\n"
		. "<div>\n"
		. "<b>Status supprimé avec succes.</b>\n"
		. "</div>\n"
		. "</div>\n";
		redirect("index.php?file=Equipe&page=admin&op=gestion_status", 2);
	 
	  adminfoot();
	}
	
	function modif_status($status_id_get) {
	global $nuked;
	  admintop();
	  
    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
	. "<div class=\"content-box-header\"><h3>Editer un Status</h3>\n"
    . "<div style=\"text-align:right;\">\n"
	. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" />\n"
	. "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\">\n"		
    . "<b><a href=\"index.php?file=Equipe&amp;page=admin\">Equipe</a> |\n" 
    . "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=add_staff\">Ajouter un membre</a> |\n" 
	. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=gestion_cat\">Gestion des catégories</a> |\n" 
	. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=gestion_status\">Gestion des Status</a>\n" 
	. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=gestion_rang\">Gestion des Rang</a>\n" 
	. "</b></div><br />";
		
	  $sql1=mysql_query('SELECT * FROM '.$nuked['prefix'].'_staff_status WHERE id="'.$status_id_get.'"');
	  $req1 = mysql_fetch_object($sql1);
		
	  echo'<form action="index.php?file=Equipe&amp;page=admin&amp;op=save_modif_status&amp;status_id='.$status_id_get.'" method="post" enctype="multipart/form-data"><table align="center">'
	  .'<tr><td>Status :</td><td><input type="text" name="nom" value="'.stripslashes($req1->nom).'" maxlength="25" /></td></tr>'
	  .'<tr><td colspan="2" style="text-align:center;"><input type="submit" value="Modifier" /></td></tr>'
	  .'</table></form>';
		
	  echo'<div style="text-align: center;"><br />[ <a href="index.php?file=Equipe&amp;page=admin&amp;op=gestion_status"><b>Retour</b></a> ]</div><br /></div></div>';
		
	  adminfoot();
	}
	
	function save_modif_status($status_id_get) {
	global $nuked;
	  admintop();

	  extract($_POST);
	  if(!empty($nom))
	  {
	    mysql_query('UPDATE '.$nuked['prefix'].'_staff_status SET nom="'.addslashes($nom).'" WHERE id='.$status_id_get);
		echo "<div class=\"notification success png_bg\">\n"
		. "<div>\n"
		. "<b>Status modifié avec succes.</b>\n"
		. "</div>\n"
		. "</div>\n";
		redirect("index.php?file=Equipe&page=admin&op=gestion_status", 2);
	  }
	  else
	  {
	    echo "<div class=\"notification error png_bg\">\n"
	     . "<div>\n"
	     . "<b>Une erreur ses produite : Vous n'avez pas remplis tout les champs.</b>"
	     . "</div>\n"
         . "</div>\n";
	     redirect("index.php?file=Equipe&amp;page=admin&amp;op=gestion_status", 2); 
	  }
	
	  adminfoot();
	}
	
	function gestion_rang() {
	global $bgcolor4, $bgcolor1, $bgcolor2, $nuked;
	  admintop();
	  
	  echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
	. "<div class=\"content-box-header\"><h3>Gestion des Rangs</h3>\n"
    . "<div style=\"text-align:right;\">\n"
	. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" />\n"
	. "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\">\n"
		. "<b><a href=\"index.php?file=Equipe&amp;page=admin\">Equipe</a> |\n" 
		. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=add_staff\">Ajouter un membre</a> |\n" 
		. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=gestion_cat\">Gestion des catégories</a> |\n" 
		. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=gestion_status\">Gestion des Status</a> |\n" 
		. "</b>Gestion des Rang \n"
		. "</div><div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Equipe&amp;page=admin&amp;op=add_rang\"><b>Ajouter un Rang</b></a> ]</div><br />";
	  echo"<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"70%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\"><tr>\n"
	  . "<td style=\"text-align: center;\"><b>Rang</b></td>\n"
	  . "<td style=\"text-align: center;\"><b>Editer</b></td>\n"
	  . "<td style=\"text-align: center;\"><b>Supprimer</b></td></tr>";
		
	  $oo=1;
	  $sql1=mysql_query('SELECT * FROM '.$nuked['prefix'].'_staff_rang');
	  while($req1 = mysql_fetch_object($sql1))
	  {
	    $color_font = ($oo == 1) ? $bgcolor2 : $bgcolor1;
		
	    echo"<tr>\n"
		. "<td style=\"text-align: center;\">".stripslashes($req1->nom)."</td>\n"
		. "<td style=\"text-align: center;\"><a href=\"index.php?file=Equipe&amp;page=admin&amp;op=modif_rang&amp;rang_id=".$req1->id."\"><img src=\"images/edit.gif\" alt=\"\" style=\"border:none;\" /></a></td>\n"
		. "<td style=\"text-align: center;\"><a href=\"index.php?file=Equipe&amp;page=admin&amp;op=dell_rang&amp;rang_id=".$req1->id."\"><img src=\"images/del.gif\" alt=\"\" style=\"border:none;\" /></a></td>\n"
		. "</tr>";
		
		$oo = ($oo == 2) ? 0 : $oo;
		$oo++;
	  }
		
	  echo'</tr></table><div style="text-align: center;"><br />[ <a href="index.php?file=Admin"><b>Retour</b></a> ]</div><br /></div></div>';
	  
	  adminfoot();
	}
	
	function add_rang() {
	global $nuked;
	  admintop();

      echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
	. "<div class=\"content-box-header\"><h3>Ajouter un Rang</h3>\n"
    . "<div style=\"text-align:right;\">\n"
	. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" />\n"
	. "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\">\n"
	 		. "<b><a href=\"index.php?file=Equipe&amp;page=admin\">Equipe</a> |\n" 
		. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=add_staff\">Ajouter un membre</a> | \n"
		. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=gestion_cat\">Gestion des catégories</a> | \n"
		. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=gestion_status\">Gestion des Status</a> | \n" 
		. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=gestion_rang\">Gestion des Rang</a>\n" 
		. "</b></div><br />";
	  echo'<form action="index.php?file=Equipe&amp;page=admin&amp;op=save_rang" method="post"><table align="center" >'
	  .'<tr><td>Rang :</td><td><input type="text" name="nom" maxlength="25" /></td></tr>'
	  .'<tr><td>Ordre :</td><td><input type="text" name="ordre" maxlength="25" /></td></tr>'
	  .'<tr><td colspan="2" style="text-align:center;"><input type="submit" value="Ajouter" /></td></tr>'
	  .'</table></form>';
		
	  echo'<div style="text-align: center;"><br />[ <a href="index.php?file=Equipe&amp;page=admin&amp;op=gestion_rang"><b>Retour</b></a> ]</div><br /></div></div>';
		
	  adminfoot();
	}
	
	function save_rang() {
	global $nuked;
	  admintop();

	  extract($_POST);
	  if(!empty($nom))
	  {
		$verification = mysql_query('SELECT COUNT(*) FROM '.$nuked['prefix'].'_staff WHERE membre_id="'.$membre_id.'" && categorie_id="'.$categorie.'"') or die (mysql_error());
		$utilise = mysql_fetch_array($verification);
		
		if($utilise['COUNT(*)'] >= 1)
		{
		 echo "<div class=\"notification error png_bg\">\n"
	     . "<div>\n"
	     . "<b>Une erreur ses produite : Ce rang figure déja dans la base de données.</b>"
	     . "</div>\n"
         . "</div>\n";
         redirect("index.php?file=Equipe&amp;page=admin&amp;op=add_rang", 2); 
		}
		else
		{
	      mysql_query('INSERT into '.$nuked['prefix'].'_staff_rang (nom, ordre) VALUES ("'.addslashes($nom).'","'.$ordre.'")');
          echo "<div class=\"notification success png_bg\">\n"
		. "<div>\n"
		. "<b>Rang ajouté avec succes.</b>\n"
		. "</div>\n"
		. "</div>\n";
		redirect("index.php?file=Equipe&page=admin&op=gestion_rang", 2);
		}
	  }
	  else
	  {
	  echo "<div class=\"notification error png_bg\">\n"
	     . "<div>\n"
	     . "<b>Une erreur ses produite : Vous n'avez pas remplis tout les champs.</b>"
	     . "</div>\n"
         . "</div>\n";
         redirect("index.php?file=Equipe&amp;page=admin&amp;op=add_rang", 2); 
	  }
	
	  adminfoot();
	}
	
	function dell_rang($rang_id_get) {
	global $nuked;
	  admintop();
	 
	    mysql_query('DELETE FROM '.$nuked['prefix'].'_staff_rang WHERE id="'.$rang_id_get.'"');
		echo "<div class=\"notification success png_bg\">\n"
		. "<div>\n"
		. "<b>Rang supprimé avec succes.</b>\n"
		. "</div>\n"
		. "</div>\n";
		redirect("index.php?file=Equipe&page=admin&op=gestion_rang", 2);
	 
	  adminfoot();
	}
	
	function modif_rang($rang_id_get) {
	global $nuked;
	  admintop();
	  
	  echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
	. "<div class=\"content-box-header\"><h3>Editer un Rang</h3>\n"
    . "<div style=\"text-align:right;\">\n"
	. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" />\n"
	. "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\">\n"
	 		. "<b><a href=\"index.php?file=Equipe&amp;page=admin\">Equipe</a> |\n" 
		. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=add_staff\">Ajouter un membre</a> | \n"
		. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=gestion_cat\">Gestion des catégories</a> | \n"
		. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=gestion_status\">Gestion des Status</a> | \n" 
		. "<a href=\"index.php?file=Equipe&amp;page=admin&amp;op=gestion_rang\">Gestion des Rang</a>\n" 
		. "</b></div><br />";

		
	  $sql1=mysql_query('SELECT * FROM '.$nuked['prefix'].'_staff_rang WHERE id="'.$rang_id_get.'"');
	  $req1 = mysql_fetch_object($sql1);
		
	  echo'<form action="index.php?file=Equipe&amp;page=admin&amp;op=save_modif_rang&amp;rang_id='.$rang_id_get.'" method="post" enctype="multipart/form-data"><table align="center">'
	  .'<tr><td>Rang :</td><td><input type="text" name="nom" value="'.stripslashes($req1->nom).'" maxlength="25" /></td></tr>'
	  .'<tr><td>Rang :</td><td><input type="text" name="ordre" value="'.stripslashes($req1->ordre).'" maxlength="25" /></td></tr>'
	  .'<tr><td colspan="2" style="text-align:center;"><input type="submit" value="Modifier" /></td></tr>'
	  .'</table></form>';
		
	  echo'<div style="text-align: center;"><br />[ <a href="index.php?file=Equipe&amp;page=admin&amp;op=gestion_rang"><b>Retour</b></a> ]</div><br /></div></div>';
		
	  adminfoot();
	}
	
	function save_modif_rang($rang_id_get) {
	global $nuked;
	  admintop();

	  extract($_POST);
	  if(!empty($nom))
	  {
	    mysql_query('UPDATE '.$nuked['prefix'].'_staff_rang SET nom="'.addslashes($nom).'",ordre="'.$ordre.'" WHERE id='.$rang_id_get);
	    echo "<div class=\"notification success png_bg\">\n"
		. "<div>\n"
		. "<b>Rang modifié avec succes.</b>\n"
		. "</div>\n"
		. "</div>\n";
		redirect("index.php?file=Equipe&page=admin&op=gestion_rang", 2);	
	  }
	  else
	  {
	   echo "<div class=\"notification error png_bg\">\n"
	     . "<div>\n"
	     . "<b>Une erreur ses produite : Vous n'avez pas remplis tout les champs.</b>"
	     . "</div>\n"
         . "</div>\n";
         redirect("index.php?file=Equipe&amp;page=admin&amp;op=gestion_rang", 2); 
	  }
	
	  adminfoot();
	}
	
	switch($_REQUEST['op'])
    {
        case 'main':
            main();
            break;
			
		case 'add_staff':
			add_staff();
			break;
			
		case 'save_staff':
			save_staff();
			break;
			
		case 'dell_staff':
			dell_staff($_REQUEST['membre_id'], $_REQUEST['cat_id']);
			break;
			
		case 'modif_staff':
			modif_staff($_REQUEST['staff_id_get']);
			break;
			
		case 'save_modif_staff':
			save_modif_staff($_REQUEST['staff_id_get']);
			break;
			
		case 'gestion_cat':
			gestion_cat();
			break;
			
		case 'add_cat':
			add_cat();
			break;
			
		case 'save_cat':
			save_cat();
			break;
			
		case 'dell_cat':
			dell_cat($_REQUEST['cat_id']);
			break;
			
		case 'modif_cat':
			modif_cat($_REQUEST['cat_id']);
			break;
			
		case 'save_modif_cat':
			save_modif_cat($_REQUEST['cat_id']);
			break;
			
		case 'gestion_status':
			gestion_status();
			break;
			
		case 'add_status':
			add_status();
			break;
			
		case 'save_status':
			save_status();
			break;
			
		case 'dell_status':
			dell_status($_REQUEST['status_id']);
			break;
			
		case 'modif_status':
			modif_status($_REQUEST['status_id']);
			break;
			
		case 'save_modif_status':
			save_modif_status($_REQUEST['status_id']);
			break;
			
		case 'gestion_rang':
			gestion_rang();
			break;
			
		case 'add_rang':
			add_rang();
			break;
			
		case 'save_rang':
			save_rang();
			break;
			
		case 'dell_rang':
			dell_rang($_REQUEST['rang_id']);
			break;
			
		case 'modif_rang':
			modif_rang($_REQUEST['rang_id']);
			break;
			
		case 'save_modif_rang':
			save_modif_rang($_REQUEST['rang_id']);
			break;
						
		case 'preferences':
			preferences();
			break;
			
        default:
            main();
            break;
    } 

} 
else if ($level_admin == -1)
{
    admintop();
    echo "<br /><br /><div style=\"text-align: center;\">" . _MODULEOFF . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />";
    adminfoot();
} 
else if ($visiteur > 1)
{
    admintop();
    echo "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></div></a><br /><br />";
    adminfoot();
} 
else
{
    admintop();
    echo "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />";
    adminfoot();
} 

?>