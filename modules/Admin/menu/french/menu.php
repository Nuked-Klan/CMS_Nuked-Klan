<?php 
header('Content-type: text/html; charset=iso-8859-1');
?>
<html><body>
	<li>
		<a class="shortcut-button"  href="modules/Admin/menu/french/aide.php" rel="modal">
			<img src="modules/Admin/images/icons/aide.png" alt="icon" />
			<span>Aides</span>
		</a>
	</li>
	<li>
		<a class="shortcut-button" rel="modal" href="index.php?file=Stats&amp;modal=true">
			<img src="modules/Admin/images/icons/statistiques.png" alt="icon" />
			<span>Statistiques</span>
		</a>
	</li>
	<li>
		<a class="shortcut-button" href="index.php?file=Admin&amp;page=erreursql">
			<img src="modules/Admin/images/icons/erreur.png" alt="icon" />
			<span>Erreurs SQL d&eacute;tect&eacute;es</span>
		</a>
	</li>
	<li>
		<a class="shortcut-button" href="#notification" rel="modal">
			<img src="modules/Admin/images/icons/megaphone.png" alt="icon" />
			<span>Ajouter une notification</span>
		</a>
	</li>
	<li>
		<a class="shortcut-button" href="#messages" rel="modal">
			<img src="modules/Admin/images/icons/comment_48.png" alt="icon" />
			<span>Discussion</span>
		</a>
	</li>
</body></html>