<?php
/**
 * english.lang.php
 *
 * English translation file of Contact module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

define('_CONTACT','Contact Form');
define('_CONTACTFORM','Please fill out the form in order to contact us.');
define('_YNICK','Your name');
define('_YMAIL','Your E-mail');
define('_YSUBJECT','Topic');
define('_YCOMMENT','The message');
define('_NOCONTENT','You forgot to fill in required fields');
//define('_NONICK','Please enter your name !');
define('_NOSUBJECT','Please enter the topic !');
define('_NOTEXTMAIL','Your message is empty !');
define('_SENDCMAIL','Your email was successfully sent, we will answer you as soon as possible.');
define('_FLOODCMAIL','You have already sent an mail less than ' . $nuked['contact_flood'] . ' minutes ago, please wait before you retry...');

define('_ADMINCONTACT','Contact the Administration');
define('_DELETEMESSAGEFROM','You are about to remove the message from');
define('_LISTMAIL','Message List');
define('_READMESS','Read');
define('_CFROM','From');
define('_NOMESSINDB','No messages in the database');
define('_READTHISMESS','Read this message');
define('_DELTHISMESS','Remove this message');
define('_MESSDELETE','Message successfully removed.');
define('_EMAILCONTACT','Contact Email');
define('_FLOODCONTACT','Allowed time between 2 messages from the same person (anti-flood)');
define('_NOTCON','You received a "contact us" message');
define('_ACTIONDELCONTACT','have deleted a contact mail that you received');
define('_ACTIONPREFCONT','modified the preferences of the contact module');
?>
