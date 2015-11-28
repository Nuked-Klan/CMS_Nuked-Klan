<?php
if (!defined("INDEX_CHECK"))
{
	exit('You can\'t run this file alone.');
}


define("_NOTEXT","Please enter a text!");
// define("_NONICKNAME","Please enter your nickname!");
//Resctriction to logged users
define("_NONICKNAME","Log in to post a message !");
// End
define("_PSEUDOEXIST","This Nick is reserved!");
define("_BANNEDNICK","This Nick is banned");
define("_NICKNAME","Nickname");
define("_YOURMESS","");

define("_REFRESH","Refresh");
define("_SEEARCHIVES","See archives");

define("_SHOUTSUCCES","Message was successfully posted.");
define("_NOFLOOD","No flood! Please wait a moment...");
define("_NOMESS","There are yet no messages");


define("_THEREIS","There are");
define("_SHOUTINDB","messages in the database");
define("_SMILEY","Add a smilies");
define("_LISTSMILIES","Smilies List");


define("_ADMINSHOUTBOX","Shoutbox Administration");

define("_IP","IP Address");


define("_MODIF","Modify");
define("_SHOUT","Message");
define("_DELETETEXT","You are about to remove the message of");
define("_DELETEALLTEXT","You are about to remove all messages, continue?");

define("_DELALLMESS","Remove all messages");
define("_DELTHISMESS","Remove this message");
define("_EDITTHISMESS","Edit this message");
define("_MESSDEL","Message was successfully removed.");
define("_MESSEDIT","Message was successfully modified.");
define("_ALLMESSDEL","All messages were removed.");
define("_NUMBERSHOUT","Number of messages per page");


define("_ACTIONMODIFSHO","has modified a message of the textbox");
define("_ACTIONDELSHO","has deleted a message of the textbox");
define("_ACTIONALLDELSHO","has deleted all message of the textbox");
define("_ACTIONCONFSHO","has modified the preference of the textbox");

define("_FRANCE", "France");
define("_BELGIUM", "Belgium");
define("_SPAIN", "Spain");
define("_UNITED-KINGDOM", "United-Kingdom");
define("_GREECE", "Greece");
define("_TUNISIA", "Tunisia");
define("_MOROCCO", "Morocco");

define("_LOADINPLSWAIT", "Loading ...");
define("_PLEASEWAITTXTBOX","Please wait ...");
define("_THANKSFORPOST","Thank you for your participation!");
define("_LOADINGERRORS","Unable to load the block!");

define("_DISPLAY_AVATAR","Display user pseudo");
define("_NOTIF_INFOS_DISPLAY","When the display of the avatar is disabled, the appearance of the textbox is that of a basic chat, date is not displayed. However it is possible to know the post date flying over the pseudo poster with the mouse.");
?>