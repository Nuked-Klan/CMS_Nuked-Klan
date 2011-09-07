<?php
/************************************************
*	Thème Impact_Nk pour Nuked Klan	*
*	Design :  djGrim (http://www.impact-design.fr/)	*
*	Codage : fce & huge (http://www.impact-design.fr/)			*
************************************************/
defined("INDEX_CHECK") or die ("<div style=\"text-align: center;\">Access deny</div>");

include('themes/Impact_Nk/block-best.php');
global $language;
translate("themes/Impact_Nk/lang/" . $language . ".lang.php");
if ($user[1] < 9){
	echo '<div style="text-align: center;margin:30px 0;">'._INWARNINGMODTHEME.'</div>';
}
else{
	function index() {
?>
		<div style="text-align: center;margin:20px 0;">
			<a style="display:block;" href="index.php?file=Admin&amp;page=theme&amp;sub=menu">
				<img src="themes/Impact_Nk/admin/images/menu.png" alt="menu"/>
			</a>
			<a style="display:block;" href="index.php?file=Admin&amp;page=theme&amp;sub=cat_colonne">
				<img src="themes/Impact_Nk/admin/images/colonne.png" alt="colonne"/>
			</a>
			<a style="display:block;" href="index.php?file=Admin&amp;page=theme&amp;sub=logo">
				<img src="themes/Impact_Nk/admin/images/logo.png" alt="logo"/>
			</a>
			<a style="display:block;" href="index.php?file=Admin&amp;page=theme&amp;sub=couleur">
				<img src="themes/Impact_Nk/admin/images/couleur.png" alt="logo"/>
			</a>
		</div>
<?php
	}
	
	function cat_colonne(){
?>
		<div style="width:80%; margin:auto;">
			<div class="notification attention png_bg">
				<div>
					<?php echo _ALERTENOT; ?>. <?php echo _CHOOSEADMIN;?>
				</div>
			</div>
		</div>
		<div style="text-align: center;margin: 20px 0;">
			<a style="display:block;font-size: 16px;margin-bottom:15px;" href="index.php?file=Admin&amp;page=theme&amp;sub=module_complet" ><?php echo _INMODULEAT; ?> 100%</a>
			<a style="display:block;font-size: 16px;" href="index.php?file=Admin&amp;page=theme&amp;sub=module_gauche" ><?php echo _INMODULEAT; ?> 75%</a>
		</div>		
<?php
		echo '<div style="text-align: center;margin:10px 0;">[ <a href="index.php?file=Admin&amp;page=theme"><b>' . _BACK . '</b></a> ]</div>';
	}
	
	function menu(){
		global $nbr_menu;

		if($_GET['action'] == 'save'){		
			$ecriretexte = '<?php';
			$nbr = 1;
			while ($nbr <= $nbr_menu){		
				$ecriretexte .= "\n".'$menu['.$nbr.'] = "'.$_POST['menu'.$nbr].'";'."\n".'$menu1['.$nbr.'] = "'.$_POST['menu1'.$nbr].'";';
				$nbr++;
			}
		
			$fichier = 'themes/Impact_Nk/admin/menu.php';
			$ecrire = fopen($fichier, "w+");
			fwrite($ecrire, $ecriretexte."\n?>");
			fclose($ecrire);
			
			echo '<div style="text-align: center;margin:20px 0;">'. _INSAVEMOD .'</div>';
			redirect ("index.php?file=Admin&page=theme".$iframe, 2);

		}
		else{
			include('themes/Impact_Nk/admin/menu.php');
?>
			<div style="text-align: center;margin:20px 0;">
				<h3><?php echo _INMANAGEMENU;?></h3>
				<form method="post" action="index.php?file=Admin&amp;page=theme&amp;sub=menu&amp;action=save<?php echo $iframe ;?>">
                <fieldset>
<?php
			$nbr = 1;
			while ($nbr <= $nbr_menu){
?>		
					<p style="font-weight: bold; text-decoration: underline;">Menu n°<?php echo $nbr ;?></p>
					<div style="margin-bottom:10px;">
						<label for="lmenu<?php echo $nbr; ?>">
							Titre : 
							<input type="text" id="lmenu<?php echo $nbr ;?>" name="menu<?php echo $nbr ;?>" value="<?php echo stripslashes($menu[$nbr]) ;?>" />
						</label>
						<label for="lmenu1<?php echo $nbr; ?>">
							Url :
							<input type="text" id="lmenu1<?php echo $nbr ;?>" name="menu1<?php echo $nbr ;?>" value="<?php echo stripslashes($menu1[$nbr]) ;?>" />
						</label>		
					</div>
<?php
				$nbr++;
			}
?>
					<input type="submit" value="<?php echo _INSAVEMOD; ?>" />
                </fieldset>
				</form>
			</div>
<?php
		}		
		echo '<div style="text-align: center;margin:10px 0;">[ <a href="index.php?file=Admin&amp;page=theme"><b>' . _BACK . '</b></a> ]</div>';
	}

	function couleur(){
		global $nbr_couleur;
		
		if($_GET['action'] == 'save'){		
			$ecriretexte = '<?php';
			$nbr = 1;
			while ($nbr <= $nbr_couleur){			
				$ecriretexte .= "\n".'$couleur['.$nbr.'] = \''.$_POST['couleur'.$nbr].'\';';				
				$nbr++;
			}

			$fichier = 'themes/Impact_Nk/admin/couleur.php';
			$ecrire = fopen($fichier, "w+");
			fwrite($ecrire, $ecriretexte."\n?>");
			fclose($ecrire);
			
			echo '<div style="text-align: center;margin:20px 0;">'._INMODSUCESS.'</div>';
			redirect ("index.php?file=Admin&page=theme".$iframe, 2);
		}
		else{
			include('themes/Impact_Nk/admin/couleur.php');
?>
			<div style="text-align: center;margin:20px 0;">
				<h3><?php echo _INMANAGECOLOR; ?></h3>
				<form method="post" name="couleur" action="index.php?file=Admin&amp;page=theme&amp;sub=couleur&amp;action=save<?php echo $iframe ;?>">
					<div style="margin:20px 0;">
<?php
			$nbr = 1;
			while ($nbr <= $nbr_couleur){
?>
						<label for="ccouleur<?php echo $nbr; ?>" ><?php echo _INCOLOR; ?> n°<?php echo $nbr ;?>
							<input type="text" id="ccouleur<?php echo $nbr; ?>" name="couleur<?php echo $nbr ;?>" value="<?php echo stripslashes($couleur[$nbr]) ;?>" />
						</label>
<?php
				$nbr++;
			}
?>
					</div>
					<input type="submit" value="<?php echo _INSAVEMOD; ?>" />
				</form>
				</div>
<?php
		}
		echo '<div style="text-align: center;margin:10px 0;">[ <a href="index.php?file=Admin&amp;page=theme"><b>' . _BACK . '</b></a> ]</div>';
	}
	
	function module_gauche(){
		global $theme, $user, $module_aff_unique;

		if($_GET['action'] == 'save'){
			if($_POST['droite'] != ''){
				foreach ($_POST['droite'] as $module2){
					$module_droite .= $module2.'|';
				}
			}
		
			$ecriretexte = '<?php'."\n".'$config_best["affiche-block-unique"] = "'.$module_droite.'"; '."\n".'?>';
			$fichier = 'themes/'.$theme.'/admin/config_best_unique.php';
			$ecrire = fopen($fichier, "w+");
			fwrite($ecrire, $ecriretexte);
			fclose($ecrire);
	
			echo '<div style="text-align: center;margin:20px 0;">'._INWARNINGMODTHEME.'</div>';
			redirect ("index.php?file=Admin&page=theme".$iframe, 2);
		}
		else{
?>
			<script type="text/javascript" language="javascript">
				var buttoncheck = "false";
				function check(field){
					if (buttoncheck == "false"){
						for (i = 0; i < field.length; i++){
							field[i].checked = true;
						}
						buttoncheck = "true";
					}
					else{
						for (i = 0; i < field.length; i++){
							field[i].checked = false;
						}
						buttoncheck = "false";
					}
				}
			</script>
			<div style="text-align: center; margin:20px 0;">
				<h3><?php echo _INMANAGECOLUMNBLOCS; ?></h3>
				<form method="post" name="block_aff" action="index.php?file=Admin&amp;page=theme&amp;sub=module_gauche&amp;action=save">			
					<table style="margin:20px auto;">
						<tr>
							<td></td>
							<td colspan="2"><?php echo _INDISPLAYAT;?></td>
						</tr>
						<tr>
							<td style="text-align: left; width: 150px; padding-left: 20px;">
								<u><?php echo _INBLOCS;?></u>
							</td>
							<td style="width: 100px;">
								unique
							</td>
						</tr>
<?php
			include('themes/Impact_Nk/admin/config_best_unique.php');
			$folder = 'modules/';
			$dossier = opendir($folder);
			while ($Fichier = readdir($dossier)){
				if ($Fichier != "." && $Fichier != ".." AND !preg_match("#^[a-z0-9._-]*\.[a-z0-9._-]{3,}$#", $Fichier)){
					echo '<tr><td>',$Fichier,'</td>
					<td><input type="checkbox" name="droite[]" value="',$Fichier,'" ';
					if($Fichier == $module_aff_unique[$Fichier]) echo 'checked="checked"';
					echo'></td></tr>';
				}
			}
?>
						<tr>
							<td>
								<?php echo _INSELECTAT; ?>
							</td>
							<td>
								<?php echo _INFULLPAGE; ?>
								<br/>
								<a href="javascript:check(document.forms.block_aff.elements['droite[]'])"><?php echo _INALLNONE; ?></a>
							</td>
						</tr>
					</table>
					<input type="submit" value="<?php echo _INSAVEMOD; ?>" /><br/><br/><br/><br/>
				</form>
			</div>
<?php
		}
		echo '<div style="text-align: center;margin:10px 0;">[ <a href="index.php?file=Admin&amp;page=theme"><b>' . _BACK . '</b></a> ]</div>';
	}
	
	function module_complet(){
		global $theme, $user, $complet;

		if($_GET['action'] == 'save'){
			if($_POST['droite'] != ''){
				foreach ($_POST['droite'] as $module2){
					$module_droite .= $module2.'|';
				}
			}
				
			$ecriretexte = '<?php'."\n".'$config_best["complet"] = "'.$module_droite.'"; '."\n".'?>';
			$fichier = 'themes/'.$theme.'/admin/complet.php';
			$ecrire = fopen($fichier, "w+");
			fwrite($ecrire, $ecriretexte);
			fclose($ecrire);
			
			echo '<div style="text-align: center;margin:20px 0;">'._INMODSUCESS.'</div>';
			redirect ("index.php?file=Admin&page=theme".$iframe, 2);
		}
		else{
?>
			<script type="text/javascript" language="javascript">
				var buttoncheck = "false";
				function check(field){
					if (buttoncheck == "false"){
						for (i = 0; i < field.length; i++){
							field[i].checked = true;
						}
						buttoncheck = "true";
					}
					else{
						for (i = 0; i < field.length; i++){
							field[i].checked = false;
						}
						buttoncheck = "false";
					}
				}
			</script>
			<div style="text-align: center;margin:20px 0;">
				<h3><?php echo _INMANAGECOLUMNBLOCS;?></h3>
				<form method="post" name="block_aff" action="index.php?file=Admin&amp;page=theme&amp;sub=module_complet&amp;action=save">			
					<table style="margin: 20px auto;">
						<tr>
							<td></td>
							<td colspan="2">
								<?php echo _INDISPLAYAT;?>
							</td>
						</tr>
						<tr>
							<td style="text-align: left; width: 150px; padding-left: 20px;">
								<u><?php echo _INBLOCS;?></u>
							</td>
							<td style="width: 100px;">
								unique
							</td>
						</tr>
<?php
			include('themes/Impact_Nk/admin/complet.php');
			$folder = "modules/";
			$dossier = opendir($folder);
			
			while ($Fichier = readdir($dossier)){
				if ($Fichier != "." && $Fichier != ".." AND !preg_match("#^[a-z0-9._-]*\.[a-z0-9._-]{3,}$#", $Fichier)){
					echo '<tr><td>'.$Fichier.'</td>
					<td><input type="checkbox" name="droite[]" value="'.$Fichier.'" ';
					if($Fichier == $complet[$Fichier]) echo 'checked="checked"';
					echo'></td></tr>';
				}
			}
?>
						<tr>
							<td>
								<?php echo _INSELECTAT; ?>
							</td>
							<td>
								<?php echo _INFULLPAGE; ?>
								<br/>
								<a href="javascript:check(document.forms.block_aff.elements['droite[]'])"><?php echo _INALLNONE; ?></a>
							</td>
						</tr>
					</table>
					<input type="submit" value="<?php echo _INSAVEMOD; ?>" /><br/><br/><br/><br/>
				</form>
			</div>
<?php
		}
		echo '<div style="text-align: center;margin:10px 0;">[ <a href="index.php?file=Admin&amp;page=theme"><b>' . _BACK . '</b></a> ]</div>';
	}
	
	function logo(){
		global $theme;

		if($_GET['action'] == 'save'){
			$ecriretexte = '<?php';
			$ecriretexte .= "\n".'$logo = "'.$_POST['logo'.$nbr].'";';
			$fichier = 'themes/Impact_Nk/admin/logo.php';
			$ecrire = fopen($fichier, "w+");
			fwrite($ecrire, $ecriretexte."\n?>");
			fclose($ecrire);
			echo '<div style="text-align: center;margin:20px 0;">'._INMODSUCESS.'</div>';
			redirect ("index.php?file=Admin&page=theme".$iframe, 2);
		}
		else{
			include('themes/Impact_Nk/admin/logo.php');
?>
			<div style="text-align: center;margin:20px 0;">
				<h3><?php echo _INMANAGELOGO; ?></h3>
				<p>
					<i><?php echo _INSIZEDESC; ?></i>
				</p>
				<form method="post" name="logo" action="index.php?file=Admin&amp;page=theme&amp;sub=logo&amp;action=save<?php echo $iframe ;?>">
                <fieldset>
					<label for="llogo">
						Url du logo:
						<input type="text" id="llogo" name="logo" value="<?php echo stripslashes($logo) ;?>" />
					</label>
					<input type="submit" value="<?php echo _INSAVEMOD; ?>" />
                </fieldset>
				</form>
			</div>
<?php
		}
		echo '<div style="text-align: center;margin:10px 0;">[ <a href="index.php?file=Admin&amp;page=theme"><b>' . _BACK . '</b></a> ]</div>';
	}
	
	switch ($_REQUEST['sub']){
		case"index":
			index();
			break;
		case"menu":
			menu();
			break;
		case"module_gauche":
			module_gauche();
			break;
		case"module_complet":
			module_complet();
			break;
		case"cat_colonne":
			cat_colonne();
			break;
		case"logo":
			logo();
			break;
		case"couleur":
			couleur();
			break;
		default:
			index();
			break;
	}
}
?>