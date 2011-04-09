<?php
/************************************************
*	Thème Impact_Nk pour Nuked Klan	*
*	Design :  djGrim (http://www.impact-design.fr/)	*
*	Codage : fce & huge (http://www.impact-design.fr/)			*
************************************************/
defined("INDEX_CHECK") or die ("<div style=\"text-align: center;\">Accès interdit</div>");

include('themes/Impact_Nk/block-best.php');
if ($user[1] < 9)
{
	echo "<br /><br /><br /><div style=\"text-align: center;\">Seuls les administrateurs suprême peuvent modifier le thème.</div><br /><br />";
}
else 
{
function index() {
?><div style="text-align: center;">
<br /><br /><br />
<a href="index.php?file=Admin&page=theme&sub=menu"><img src="themes/Impact_Nk/admin/images/menu.png" alt="menu"/></a><br />
<a href="index.php?file=Admin&page=theme&sub=cat_colonne"><img src="themes/Impact_Nk/admin/images/colonne.png" alt="colonne"/></a><br />
<a href="index.php?file=Admin&page=theme&sub=logo"><img src="themes/Impact_Nk/admin/images/logo.png" alt="logo"/></a><br />
<a href="index.php?file=Admin&page=theme&sub=couleur"><img src="themes/Impact_Nk/admin/images/couleur.png" alt="logo"/></a><br /><br /><br />
</div>
<?php
}
function cat_colonne() {
?>
<div style="width:80%; margin:auto;">
<div class="notification attention png_bg">
	<div>
	<?php echo _ALERTENOT; ?>. <?php echo _CHOOSEADMIN;?>
	</div>
</div></div>
<div style="text-align: center;">
<br /><br /><br />
<a href="index.php?file=Admin&page=theme&sub=module_complet" style=" font-size: 16px;">Module en 100%</a><br />
<a href="index.php?file=Admin&page=theme&sub=module_gauche" style="font-size: 16px;">Module en 75%</a><br /><br /><br /></div>

<?php
echo "<br /><div style=\"text-align: center;\">[ <a href=\"index.php?file=Admin&page=theme\"><b>" . _BACK . "</b></a> ]</div><br/>";
}
function menu() {
		Global $nbr_menu;

If($_GET['action'] == 'save') {

$ecriretexte = '<?php
';
$nbr = 1;
while ($nbr <= $nbr_menu) {

$ecriretexte .= '$menu['.$nbr.'] = "'.$_POST['menu'.$nbr].'";
$menu1['.$nbr.'] = "'.$_POST['menu1'.$nbr].'";
';

$nbr++;
}



$fichier = "themes/Impact_Nk/admin/menu.php";
$ecrire = fopen($fichier, "w+");
fwrite($ecrire, $ecriretexte."?>");
fclose($ecrire);

echo "<br /><div style='text-align: center;'>Modifications enregistrées avec succès !</div><br />";
redirect ("index.php?file=Admin&page=theme".$iframe, 2);

}

Else {

include('themes/Impact_Nk/admin/menu.php');


?>
<br/><div style="text-align: center;"><h3>Gestion du Thème<br/>Gestion du menu</h3>
<br/>
<form method="post" name="menu" action="index.php?file=Admin&amp;page=theme&amp;sub=menu&amp;action=save<?php echo $iframe ;?>">

<?php
$nbr = 1;
while ($nbr <= $nbr_menu) {
?>

<span style="font-weight: bold; text-decoration: underline;">Menu n°<?php echo $nbr ;?></span>
<table style="margin: auto;" cellspacing="0" cellpadding="0" border="0">

Titre: <input type="text" name="menu<?php echo $nbr ;?>" value="<?php echo stripslashes($menu[$nbr]) ;?>" /><br />
Url: <input type="text" name="menu1<?php echo $nbr ;?>" value="<?php echo stripslashes($menu1[$nbr]) ;?>" />


</table>
<br/>

<?php
$nbr++;
}
?>
<input type="submit" value="Enregistrer les modifications" />
</form>
</div>
<br/>
<?php
}
echo "<br /><div style=\"text-align: center;\">[ <a href=\"index.php?file=Admin&page=theme\"><b>" . _BACK . "</b></a> ]</div>";
}
function couleur() {
		Global $nbr_couleur;

If($_GET['action'] == 'save') {

$ecriretexte = '<?php
';
$nbr = 1;
while ($nbr <= $nbr_couleur) {

$ecriretexte .= '$couleur['.$nbr.'] = "'.$_POST['couleur'.$nbr].'";
';

$nbr++;
}



$fichier = "themes/Impact_Nk/admin/couleur.php";
$ecrire = fopen($fichier, "w+");
fwrite($ecrire, $ecriretexte."?>");
fclose($ecrire);

echo "<br /><div style='text-align: center;'>Modifications enregistrées avec succès !</div><br />";
redirect ("index.php?file=Admin&page=theme".$iframe, 2);

}

Else {

include('themes/Impact_Nk/admin/couleur.php');


?>
<br/><div style="text-align: center;"><h3>Gestion du Thème<br/>Gestion du couleur</h3>
<br/>
<form method="post" name="couleur" action="index.php?file=Admin&amp;page=theme&amp;sub=couleur&amp;action=save<?php echo $iframe ;?>">

<?php
$nbr = 1;
while ($nbr <= $nbr_couleur) {
?>

<span style="font-weight: bold; text-decoration: underline;">Couleur n°<?php echo $nbr ;?></span>
<table style="margin: auto;" cellspacing="0" cellpadding="0" border="0">

Couleur: <input type="text" name="couleur<?php echo $nbr ;?>" value="<?php echo stripslashes($couleur[$nbr]) ;?>" /><br />


</table>
<br/>

<?php
$nbr++;
}
?>
<input type="submit" value="Enregistrer les modifications" />
</form>
</div>
<br/>
<?php
}
echo "<br /><div style=\"text-align: center;\">[ <a href=\"index.php?file=Admin&page=theme\"><b>" . _BACK . "</b></a> ]</div><br/>";
}
function module_gauche() {

	global $theme, $user, $module_aff_unique;

If($_GET['action'] == 'save') {

If($_POST['droite'] != '') {
foreach ($_POST['droite'] as $module2){
	$module_droite .= $module2.'|';
}
}


$ecriretexte = '<?php
$config_best["affiche-block-unique"] = "'.$module_droite.'";
?>';
$fichier = "themes/".$theme."/admin/config_best_unique.php";
$ecrire = fopen($fichier, "w+");
fwrite($ecrire, $ecriretexte);
fclose($ecrire);


echo "<br /><div style='text-align: center;'>Modifications enregistrées avec succès !</div><br />";



redirect ("index.php?file=Admin&page=theme".$iframe, 2);

}

else {
?>
<script type="text/javascript" language="javascript">
var buttoncheck = "false";
function check(field) {
if (buttoncheck == "false") {
for (i = 0; i < field.length; i++) {
field[i].checked = true;}
buttoncheck = "true"; }
else {
for (i = 0; i < field.length; i++) {
field[i].checked = false; }
buttoncheck = "false"; }
}
</script>

<br/><div style="text-align: center;"><h3>Gestion du Thème<br/>Gestion des Colonnes de blocks</h3>
<br/><b></b><br/>
<br/><br/>
<form method="post" name="block_aff" action="index.php?file=Admin&amp;page=theme&amp;sub=module_gauche&amp;action=save">

<table style="margin: auto;">
<tr><td></td><td colspan="2">Afficher à :</td></tr>
<tr><td style="text-align: left; width: 150px; padding-left: 20px;"><u>Blocs</u></td><td style="width: 100px;">unique</td></tr>
<?php
include('themes/Impact_Nk/admin/config_best_unique.php');
$folder = "modules/";
$dossier = opendir($folder);
while ($Fichier = readdir($dossier))
{
	if ($Fichier != "." && $Fichier != ".." AND !preg_match("#^[a-z0-9._-]*\.[a-z0-9._-]{3,}$#", $Fichier))
	{
		echo '<tr><td>'.$Fichier.'</td>
		<td><input type="checkbox" name="droite[]" value="'.$Fichier.'" ';
		If($Fichier == $module_aff_unique[$Fichier]) echo 'checked="checked"';
		echo'></td></tr>';
	}
}
?>
<tr><td>Selectionner à : </td>
<td>pleine page<br/>
<a href="javascript:check(document.forms.block_aff.elements['droite[]'])">Tous - Aucun</a></td></tr>
</table>
<br/><input type="submit" value="Enregistrer les modifications" /><br/><br/><br/><br/>
</form>
</div>
<br/>

<?php
}
echo "<br /><div style=\"text-align: center;\">[ <a href=\"index.php?file=Admin&page=theme&sub=cat_colonne\"><b>" . _BACK . "</b></a> ]</div><br/>";
}
function module_complet() {

	global $theme, $user, $complet;

If($_GET['action'] == 'save') {

If($_POST['droite'] != '') {
foreach ($_POST['droite'] as $module2){
	$module_droite .= $module2.'|';
}
}


$ecriretexte = '<?php
$config_best["complet"] = "'.$module_droite.'";
?>';
$fichier = "themes/".$theme."/admin/complet.php";
$ecrire = fopen($fichier, "w+");
fwrite($ecrire, $ecriretexte);
fclose($ecrire);


echo "<br /><div style='text-align: center;'>Modifications enregistrées avec succès !</div><br />";



redirect ("index.php?file=Admin&page=theme".$iframe, 2);

}

else {
?>
<script type="text/javascript" language="javascript">
var buttoncheck = "false";
function check(field) {
if (buttoncheck == "false") {
for (i = 0; i < field.length; i++) {
field[i].checked = true;}
buttoncheck = "true"; }
else {
for (i = 0; i < field.length; i++) {
field[i].checked = false; }
buttoncheck = "false"; }
}
</script>

<br/><div style="text-align: center;"><h3>Gestion du Thème<br/>Gestion des Colonnes de blocks</h3>
<br/><b></b><br/>
<br/><br/>
<form method="post" name="block_aff" action="index.php?file=Admin&amp;page=theme&amp;sub=module_complet&amp;action=save">

<table style="margin: auto;">
<tr><td></td><td colspan="2">Afficher à :</td></tr>
<tr><td style="text-align: left; width: 150px; padding-left: 20px;"><u>Blocs</u></td><td style="width: 100px;">unique</td></tr>
<?php
include('themes/Impact_Nk/admin/complet.php');
$folder = "modules/";
$dossier = opendir($folder);
while ($Fichier = readdir($dossier))
{
	if ($Fichier != "." && $Fichier != ".." AND !preg_match("#^[a-z0-9._-]*\.[a-z0-9._-]{3,}$#", $Fichier))
	{
		echo '<tr><td>'.$Fichier.'</td>
		<td><input type="checkbox" name="droite[]" value="'.$Fichier.'" ';
		If($Fichier == $complet[$Fichier]) echo 'checked="checked"';
		echo'></td></tr>';
	}
}
?>
<tr><td>Selectionner à : </td>
<td>pleine page<br/>
<a href="javascript:check(document.forms.block_aff.elements['droite[]'])">Tous - Aucun</a></td></tr>
</table>
<br/><input type="submit" value="Enregistrer les modifications" /><br/><br/><br/><br/>
</form>
</div>
<br/>

<?php
}
echo "<br /><div style=\"text-align: center;\">[ <a href=\"index.php?file=Admin&page=theme&sub=cat_colonne\"><b>" . _BACK . "</b></a> ]</div><br/>";
}
function logo() {
		global $theme;

If($_GET['action'] == 'save') {

$ecriretexte = '<?php
';

$ecriretexte .= '$logo = "'.$_POST['logo'.$nbr].'";
';


$fichier = "themes/Impact_Nk/admin/logo.php";
$ecrire = fopen($fichier, "w+");
fwrite($ecrire, $ecriretexte."?>");
fclose($ecrire);

echo "<br /><div style='text-align: center;'>Modifications enregistrées avec succès !</div><br />";
redirect ("index.php?file=Admin&page=theme".$iframe, 2);


}
else {

include('themes/Impact_Nk/admin/logo.php');


?>
<br/><div style="text-align: center;"><h3>Gestion du Thème<br/>Gestion du logo</h3>
<br/>
<i>L'image du logo doit avoir 121px de largeur exactement, la hauteur est de 108px.</i>
<form method="post" name="logo" action="index.php?file=Admin&amp;page=theme&amp;sub=logo&amp;action=save<?php echo $iframe ;?>">

<table style="margin: auto;" cellspacing="0" cellpadding="0" border="0">

Url du logo: <input type="text" name="logo" value="<?php echo stripslashes($logo) ;?>" />


</table>
<br/>


<input type="submit" value="Enregistrer les modifications" />
</form>
</div>
<br/>

<?php
}
echo "<br /><div style=\"text-align: center;\">[ <a href=\"index.php?file=Admin&page=theme\"><b>" . _BACK . "</b></a> ]</div><br/>";
}
switch ($_REQUEST['sub'])
{
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
