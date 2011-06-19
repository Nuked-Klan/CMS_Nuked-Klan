<?php
/***********************************************
 * PckageMgr - Gestionnaire de patch
 * ---------------------------------------------
 * Auteur : Bontiv <prog.bontiv@gmail.com>
 * Site web : http://remi.bonnetchangai.free.fr/
 * ---------------------------------------------
 * Ce fichier fait parti d'un module libre. Toutefois
 * je vous demanderez de respecter mon travail en ne
 * supprimant pas mon pseudo.
 ***********************************************/
if (!defined("INDEX_CHECK"))
{
    die ("<div style=\"text-align: center;\">You cannot open this page directly</div>");
} 

require_once 'package.php';
class Manager {
	
	public function __construct($op) {
		switch ($op) {
			case 'ins':
				$this->Installation();
				break;
            case 'del':
				$this->Desinstallation();
				break;
			case 'act':
				$this->Active();
				break;
			case 'des':
				$this->Desactive();
				break;
			case 'index':
			default:
				$this->Packlist();
		}
	}
	
	protected function Active()
	{
		try {
			$package = new Package ($_REQUEST['f']);
			$package->activate();
			echo '<p>Activation termin&#233;e !</p><p><a href="?a=index&file=PackageMgr&page=admin">Retour</a></p>';
		} catch (PackageException $error)
		{
			echo '<p>Oups ! Je n\'ai pas r&#233;ussi à activer ce patch...</p>'
				. '<p>' . $error->getMessage() . '</p>';
			echo '<br /><p><a href="?a=index&file=PackageMgr&page=admin">Retour</a></p>';
		}
	}

	protected function Desactive()
	{
		try {
			$package = new Package ($_REQUEST['f']);
			$package->deactivate();
			echo '<p>D&#233;sactivation termin&#233;e !</p><p><a href="?a=index&file=PackageMgr&page=admin">Retour</a></p>';
		} catch (PackageException $error)
		{
			echo '<p>Oups ! Je n\'ai pas r&#233;ussi à d&#233;sactiver ce patch...</p>'
				. '<p>' . $error->getMessage() . '</p>';
			echo '<br /><p><a href="?a=index&file=PackageMgr&page=admin">Retour</a></p>';
		}
	}
	
	protected function Installation() {
		if (!isset($_FILES['package']))
		{
			echo '<p>Installation d\'un patch. Envoyez le fichier de votre patch :</p>'
            . '<form method="POST" action="?a=ins&file=PackageMgr&page=admin" enctype="multipart/form-data"><input type="file" name="package" />'
            . '<br /><input type="submit" value="Installer" /></form>';
			echo '<br /><p><a href="?a=index&file=PackageMgr&page=admin">Retour</a></p>';
		}
		else
		{
			move_uploaded_file($_FILES["package"]["tmp_name"], 'upload/' . $_FILES["package"]['name']);
			try {
				$package = new Package ($_FILES["package"]['name']);
				$package->install();
				echo '<p>Installation termin&#233;e !</p><p><a href="?a=index&file=PackageMgr&page=admin">Retour</a></p>';
			} catch (PackageException $error) {
				echo '<p>Oups ! Je n\'ai pas r&#233;ussi à installer ce patch...</p>'
					. '<p>' . $error->getMessage() . '</p>';
				echo '<br /><p><a href="?a=index&file=PackageMgr&page=admin">Retour</a></p>';
			}
		}
	}
	
	protected function Desinstallation()
	{
		try {
			$package = new Package ($_REQUEST['f']);
			$package->uninstall();
			echo '<p>D&#233;sinstallation termin&#233; !</p><p><a href="?a=index&file=PackageMgr&page=admin">Retour</a></p>';
		} catch (PackageException $error) {
			echo '<p>Oups ! Je n\'ai pas r&#233;ussi à installer ce patch...</p>'
				. '<p>' . $error->getMessage() . '</p>';
			echo '<br /><p><a href="?a=index&file=PackageMgr&page=admin">Retour</a></p>';
		}
	}
	
	protected function Packlist(){
		global $nuked;
		echo '<p><a href="?a=ins&file=PackageMgr&page=admin">Installer un paquet</a></p><br/><p>Liste des paquets install&#233;s:</p><div>';
		$packages = mysql_query('SELECT * FROM `' . $nuked['prefix'] . '_packages`');
		if (mysql_num_rows($packages) == 0) {
			echo 'You don\'t have any packages installed';
		}
		else
		{
			echo '<table>';
			echo "<thead><tr><td>Patch</td><td>Site officiel</td><td>Auteur</td><td>Actions</td></tr></thead><tbody>";
			while ($package = mysql_fetch_assoc($packages)) {
				$package['url'] = htmlentities($package['link']);
				$package['author'] = preg_replace('`\b([a-zA-Z0-9._-]@[a-zA-Z0-9._-].[a-zA-Z])`', '<a href="mailto:$1">s</a>', $package['author']);
				$package['option'] = $package['active'] == 0 ? '<a href="?a=act&f=' . $package['file'] . '&file=PackageMgr&page=admin">Activer</a>' : '<a href="?a=des&f=' . $package['file'] . '&file=PackageMgr&page=admin">D&#233;sactiver</a>';
				echo "<tr><td>$package[name]</td><td><a href=\"$package[link]\" target=\"_blank\">$package[url]</a></td><td>$package[author]</td><td>$package[option] <a href=\"?a=del&f=$package[file]&file=PackageMgr&page=admin\">Supprimer</a></td></tr>";
			}
			echo '</tbody></table>';
		}
	}
}
?>