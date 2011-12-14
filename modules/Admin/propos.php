<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
defined('INDEX_CHECK') or die;

global $user, $language;
translate('modules/Admin/lang/' . $language . '.lang.php');
include('modules/Admin/design.php');

$visiteur = $user ? $user[1] : 0;

admintop();

if ($visiteur >= 2) {
    ?>
    <div class="content-box"><!-- Start Content Box -->
        <div class="content-box-header"><h3><?php echo _PROPOS; ?></h3></div>
        <div class="tab-content" id="tab2">
            <div style="margin:20px">
                <h3>Informations générales :</h3>
                Version <?php echo $nuked['version']; ?><br />
                Développée par toute <a href="http://www.nuked-klan.org/index.php?file=Equipe">l'équipe Nuked-KlaN</a><br />

                <h3>Contact Nuked-KlaN :</h3>
                <a href="http://www.nuked-klan.org/index.php?file=Contact">Formulaire de contact</a><br />

                <h3>Remerciements:</h3>
                A toute l'équipe nuked-klan.org, ainsi qu'à sa communauté qui nous a permis de corriger rapidement les principaux bugs<br /><br /><br />

                <h3>Licence GNU:</h3>
                Merci de ne pas supprimer le <acronym title="Le terme copyleft est un double jeu de mots faisant référence au copyright traditionnel">copyleft</acronym> par respect pour le cms et respecter la licence GNU.<br />
            </div>
            <div style="text-align: center"><br />[ <a href="index.php?file=Admin"><b><?php echo _BACK; ?></b></a> ]<br /></div>
        </div>
    </div>
    <?php
}
else if ($visiteur > 1) {
    echo "<div class=\"notification error png_bg\">\n"
    . "<div>\n"
    . "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
    . "</div>\n"
    . "</div>\n";
}
else {
    echo "<div class=\"notification error png_bg\">\n"
    . "<div>\n"
    . "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
    . "</div>\n"
    . "</div>\n";
}
?>