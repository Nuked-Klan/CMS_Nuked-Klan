<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.eu                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
defined('INDEX_CHECK') or die ('You can\'t run this file alone.');

global $user, $language;
translate('modules/Admin/lang/' . $language . '.lang.php');
include('modules/Admin/design.php');

$visiteur = $user ? $user[1] : 0;

if ($visiteur >= 2)
{
    admintop();
    ?>
    <div class="content-box"><!-- Start Content Box -->
    <div class="content-box-header"><h3><?php echo _PROPOS; ?></h3></div>
    <div class="tab-content" id="tab2"><div style="margin:20px">
    <h3>Informations g�n�rales :</h3>
    Version <?php echo $nuked['version']; ?><br />
    D�velopp�e par toute l'�quipe Nuked-KlaN<br />
    D�veloppement g�r� par : fce.<br />
    D�veloppeurs : bontiv, fce, xpLosIve, oeildefeu, Akred, Samoth, Sekuline, PooG, H@D3S, BaHaMuT.<br />
    Responsables Qualit� : Lypso21, Zdav<br /><br /><br />
    
    <h3>Contact Nuked-KlaN :</h3>
    <a href="http://www.nuked-klan.eu/index.php?file=Contact">Formulaire de contact</a><br />
    Administrateur : fce@nuked-klan.eu<br />
    Community Manager : bankai et perperpervers<br />
    Responsable : oobahamutoo@nuked-klan.eu<br /><br /><br />
    
    <h3>Remerciements:</h3>
    A toute l'�quipe nuked-klan.eu, ainsi qu'� sa communaut� qui nous a permis de corriger rapidement les principaux bugs<br /><br /><br />
    
    <h3>Licence GNU:</h3>
    Merci de ne pas supprimer le copyleft par respect pour le cms et respecter la licence GNU.<br />
    </div>
    <div style="text-align: center"><br />[ <a href="index.php?file=Admin"><b><?php echo _BACK; ?></b></a> ]</div><br /></div></div>
    <?php
    adminfoot();

}
else if ($visiteur > 1)
{
    admintop();
    echo "<div class=\"notification error png_bg\">\n"
    . "<div>\n"
    . "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
    . "</div>\n"
    . "</div>\n";
    adminfoot();
}
else
{
    admintop();
    echo "<div class=\"notification error png_bg\">\n"
    . "<div>\n"
    . "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
    . "</div>\n"
    . "</div>\n";
    adminfoot();
}
?>