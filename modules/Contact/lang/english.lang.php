<?php
defined('INDEX_CHECK') or die ('You can\'t run this file alone.');

define('_CONTACT','Contact Form');
define('_CONTACTFORM','Please fill out the form in order to contact us.');
define('_YNICK','Your name');
define('_YMAIL','Your E-mail');
define('_YSUBJECT','Topic');
define('_YCOMMENT','The message');
define('_SEND','Send');
define('_NOCONTENT','You forgot to fill in required fields');
define('_NONICK','Please enter your name !');
define('_NOSUBJECT','Please enter the topic !');
define('_NOTEXTMAIL','Your message is empty !');
define('_BADMAIL','Your email is incorrect or hasn\'t been filled out !');
define('_SENDCMAIL','Your email was successfully sent, we will answer you as soon as possible.');
define('_FLOODCMAIL','You have already sent an mail less than ' . $nuked['contact_flood'] . ' minutes ago, please wait before you retry...');

define('_NOENTRANCE','Sorry you haven\'t got the permissions to open this page');
define('_ZONEADMIN','This zone is reserved for Admins, sorry...');
define('_NOEXIST','Sorry either this page does not exist or the address that you typed is incorrect');
define('_ADMINCONTACT','Contact the Administration');
define('_HELP','Help');
define('_DELETEMESSAGEFROM','You are about to remove the message from');
define('_LISTMAIL','Message List');
define('_PREFS','Preferences');
define('_TITLE','Title');
define('_NAME','Name');
define('_DATE','Date');
define('_READMESS','Read');
define('_DEL','Remove');
define('_BACK','Back');
define('_FROM','From');
define('_THE','The');
define('_NOMESSINDB','No messages in the database');
define('_READTHISMESS','Read this message');
define('_DELTHISMESS','Remove this message');
define('_MESSDELETE','Message successfully removed.');
define('_PREFUPDATED','Preferences were successfully altered.');
define('_EMAILCONTACT','Contact Email');
define('_FLOODCONTACT','Allowed time between 2 messages from the same person (anti-flood)');
define('_NOTCON','You received a "contact us" message');
define('_ACTIONDELCONTACT','have deleted a contact mail that you received');
define('_ACTIONPREFCONT','modified the preferences of the contact module');
?>