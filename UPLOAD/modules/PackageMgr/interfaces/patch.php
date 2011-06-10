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

interface patch {
	public function TryPatch($str, $from, $to);
	public function Patch($str, $from, $to);
	public function TryUnPatch($str, $from, $to);
	public function UnPatch($str, $from, $to);
}