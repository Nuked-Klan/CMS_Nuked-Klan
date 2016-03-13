<?php
/**
 * french.lang.php
 *
 * French translation file of Download module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

define("_NEWSFILE","Nouveaux");
define("_POPULAR","Populaires");
define("_DTOPFILE","Popularité");
define("_SUGGESTFILE","Proposer");
//define("_SEEDECS","Voir les détails");
define("_DOWNLOAD","Téléchargements");
define("_FILES","Fichiers");
//define("_HITS","Hits");
define("_MAX","Max");
define("_DDOWNFILE","Télécharger ce fichier");
define("_DOWNLOADED","Téléchargé");
define("_DSEEN","Vu");
define("_DTIMES","fois");
define("_EDITTHE","Modifié le");
define("_CAPTURE","Capture d'écran");
define("_COMPATIBLE","Compatibilité");
define("_FILECOMMENT","Commentaires");
define("_FILEVOTE","Votes");
define("_NODOWNLOADS","Aucun fichier pour cette catégorie");
define("_NODOWNLOADINDB","Aucun fichier dans la base de données");
define("_INDEXDOWNLOAD","Index");
define("_TOPDOWN","Le Top 10");
define("_LASTDOWN","Les nouveautés");
define("_PLEASEWAIT","<div style=\"text-align: center;\"><br /><big><b>Veuillez patientez quelques secondes...</b></big></div><br /><div>Si le téléchargement ne demarre pas après quelques secondes, cliquez sur un des liens ci-dessous :</div>");
define("_LIEN1","Lien 1");
define("_LIEN2","Lien 2");
define("_LIEN3","Lien 3");

define("_DTHXBROKENLINK","Merci de nous avoir signalé ce lien mort.");
define("_DINDICATELINK","Signaler ce lien mort");

define("_SITE","Site");
define("_CLICHERE","Cliquez ici");
define("_EXT","Type de fichier");
define("_DFILENAME","Nom du fichier");
define("_DMORETOP","la suite du classement");
define("_DMORELAST","la suite des nouveautés");
define("_DHOT","<span style=\"color: red;\"><b><i>Hot!</i></b></font>");
define("_DNEW","<span style=\"color: red;\"><b><i>New!</i></b></font>");


define("_ADMINDOWN","Administration Téléchargements");
define("_DUPFILE","Uploader le fichier sur le serveur");
define("_DUPIMG","Uploader l'image sur le serveur");

define("_DURLFILE","Url 1");
define("_URL2","Url 2");
define("_URL3","Url 3");
define("_ADDTHISFILE","Ajouter ce Fichier");
define("_DFILEADD","Fichier ajouté avec succès.");
define("_DFILEDEL","Fichier effacé avec succès.");
define("_MODIFFILE","Modifier ce Fichier");
define("_FILEEDIT","Fichier Modifié avec succès.");


define("_DADDFILE","Ajouter un Fichier");
define("_EDITTHISFILE","Editer ce Fichier");
define("_DELTHISFILE","Supprimer ce Fichier");
define("_DBROKENLINKS","Liens Morts");
define("_DERASE","Effacer");
define("_DERASEFROMLIST","Effacer de la liste");
define("_FILEERASED","Fichier effacé de la liste avec succès.");
define("_DERASEALLLIST","Vous êtes sur le point de vider toute la liste des liens morts, continuer ?");
define("_DERASELIST","Vider la liste des liens morts");
define("_LISTERASED","La liste des liens morts a été vidée avec succès.");
define("_URLORTITLEFAILDED","Vous n'avez pas indiqué d'url et/ou de titre !");
define("_NUMBERFILE","Nombre de fichiers par page");
define("_HIDEDESC","Cacher les champs vides de la description");
define("_ACTIONADDDL","a ajouté le fichier");
define("_ACTIONDELDL","a supprimé le fichier");
define("_ACTIONMODIFDL","a modifié le fichier");
define("_ACTIONALLBROKEDL","a supprimé la liste de lien mort du module téléchargement");
define("_ACTION1BROKEDL","a supprimé 1 lien mort du module téléchargement");
define("_ACTIONADDCATDL","a ajouté la catégorie téléchargement");
define("_ACTIONMODIFCATDL","a modifié la catégorie téléchargement");
define("_ACTIONDELCATDL","a supprimé la catégorie téléchargement");
define("_ACTIONMODIFPREFDL","a modifié les préférences du module téléchargement");
define("_ACTIONPOSMODIFCATDL","a modifié la position de la catégorie téléchargement");
define("_DCATERRORPOS","Impossible la position serait inférieur à zéro.");

return array(
    // modules/Download/admin.php
    'UPLOAD_SCREENSHOT_FAILED' => 'Le Téléchargement de la capture d\'écran a échoué !',
    'SCREENSHOT_ALREADY_EXIST' => 'Une capture d\'écran portant le même nom est déjà présente sur votre ftp',
);

?>
