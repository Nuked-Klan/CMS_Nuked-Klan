<?php
/**
 * french.lang.php
 *
 * French translation file of Contact module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

define('_CONTACT','Formulaire de contact');
define('_CONTACTFORM','Veuillez remplir le formulaire ci-dessous puis cliquer sur Envoyer');
define('_YNICK','Votre Nom');
define('_YMAIL','Votre Email');
define('_YSUBJECT','Objet');
define('_YCOMMENT','Votre message');
define('_NOCONTENT','Vous avez oublié de remplir des champs obligatoires');
//define('_NONICK','Vous n\'avez pas entré votre nom !');
define('_NOSUBJECT','Vous n\'avez pas entré de sujet !');
define('_SENDCMAIL','Votre email a bien été envoyé, nous vous répondrons dans les plus brefs délais.');
define('_FLOODCMAIL','Vous avez déja posté un mail il y\'a moins de ' . $nuked['contact_flood'] . ' minutes,<br />veuillez patienter avant de renvoyer un autre email...');

define('_ADMINCONTACT','Administration Contact');
define('_DELETEMESSAGEFROM','Vous êtes sur le point de supprimer le message de');
define('_LISTMAIL','Liste des messages');
define('_READMESS','Lire');
define('_CFROM','De');
define('_NOMESSINDB','Aucun message dans la base de données');
define('_READTHISMESS','Lire ce message');
define('_DELTHISMESS','Supprimer ce message');
define('_MESSDELETE','Message supprimé avec succès');
define('_EMAILCONTACT','Email de reception');
define('_FLOODCONTACT','Durée en minutes entre 2 messages (flood)');
define('_NOTCON','Vous avez reçu un mail contact');
define('_ACTIONDELCONTACT','a supprimé un mail contact reçu');
define('_ACTIONPREFCONT','a modifié les préférences du module contact');
?>
