<?php
/**
 * english.lang.php
 *
 * English translation file of Download module
 *
 * @version     1.8
 * @link https://nuked-klan.fr Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

define("_NEWSFILE","New");
define("_POPULAR","Popular");
define("_DTOPFILE","Popularity");
define("_SUGGESTFILE","Suggest");
//define("_SEEDECS","View the details");
define("_DOWNLOAD","Downloads");
define("_FILES","Files");

//define("_HITS","Hits");
define("_MAX","Max");

define("_DDOWNFILE","Download this File");
define("_DOWNLOADED","Download");
define("_DSEEN","Hit");
define("_DTIMES","times");
define("_EDITTHE","Edit");
define("_CAPTURE","Screen capture");
define("_COMPATIBLE","Compatibilities");
define("_FILECOMMENT","Comment");
define("_FILEVOTE","Votes");
define("_NODOWNLOADS","No files in this category");
define("_NODOWNLOADINDB","No files in the database");
define("_INDEXDOWNLOAD","Index");
define("_TOPDOWN","Top Downloads");
define("_LASTDOWN","Last Downloads");
define("_PLEASEWAIT","<div style=\"text-align: center;\"><br /><big><b>Please wait a few seconds...</b></big></div><br /><div>If your download does not start, click on a link below :</div>");
define("_LIEN1","Link 1");
define("_LIEN2","Link 2");
define("_LIEN3","Link 3");

define("_DTHXBROKENLINK","Thank you for having informed us of this broken link");
define("_DINDICATELINK","Report broken link");

define("_SITE","Site");
define("_CLICHERE","Click here");
define("_EXT","File type");
define("_DFILENAME","File name");
define("_DMORETOP","more downloads");
define("_DMORELAST","more downloads");
define("_DHOT","<span style=\"color: red;\"><b><i>Hot!</i></b></font>");
define("_DNEW","<span style=\"color: red;\"><b><i>New!</i></b></font>");



define("_ADMINDOWN","Downloads Administration");
define("_DUPFILE","Upload the file onto the server");
define("_DUPIMG","Upload the image onto the server");

define("_DURLFILE","Url 1");
define("_URL2","Url 2");
define("_URL3","Url 3");
define("_ADDTHISFILE","Add this File");
define("_DFILEADD","File was successfully added.");
define("_DFILEDEL","File was successfully removed.");
define("_MODIFFILE","Modify this File");
define("_FILEEDIT","File was successfully modified.");


define("_DADDFILE","Add File");
define("_EDITTHISFILE","Edit this File");
define("_DELTHISFILE","Remove this File");
define("_DBROKENLINKS","Brokens Links");
define("_DERASE","Remove");
define("_DERASEFROMLIST","Remove from list");
define("_FILEERASED","File was successfully removed from the list.");
define("_DERASEALLLIST","You are about to remove all deads links from list, continue ?");
define("_DERASELIST","Clear broken links list");
define("_LISTERASED","Broken links list successfully cleared.");
define("_URLORTITLEFAILDED","Url or/and title missing!");
define("_NUMBERFILE","Number of files per page");
define("_HIDEDESC","Hide the empty fields in the description");

define("_ACTIONADDDL","has added the file");
define("_ACTIONDELDL","has deleted the file");
define("_ACTIONMODIFDL","havs modified the file");
define("_ACTIONALLBROKEDL","has deleted the brokens links list of download module");
define("_ACTION1BROKEDL","has deleted a broken link of download module");
define("_ACTIONADDCATDL","has added the download category");
define("_ACTIONMODIFCATDL","has modified the download category");
define("_ACTIONPOSMODIFCATDL","has modified the position of the download category");
define("_ACTIONDELCATDL","has deleted the download category");
define("_ACTIONMODIFPREFDL","has modified the preference of download module");
define("_DCATERRORPOS","Impossible the position will be lower than zero.");

return array(
    // modules/Download/admin.php
    'UPLOAD_SCREENSHOT_FAILED' => 'Upload image failed !',
    'SCREENSHOT_ALREADY_EXIST' => 'A image with the same name already exists on your ftp',
);

?>
