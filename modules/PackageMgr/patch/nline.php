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

class nline {
	private $content;
	
	public function __construct($content,SimpleXMLElement $args)
	{
		$this->content = $content;
	}
	
	private function nextEOL($str, $offset)
	{
		$eoln = strpos($str, "\n", $offset);
		$eolr = strpos($str, "\r", $offset);
		if ($eoln !== false && $eolr !== false)
		{
			return min ($eoln, $eolr);
		}
		elseif ($eoln !== false)
		{
			return $eoln;
		}
		elseif ($eolr !== false)
		{
			return $eolr;
		}
		else
		{
			return $offset;
		}
	}
	
	public function TryPatch ($str, $from, $to)
	{
		return true;
	}
	
	public function Patch ($str, $from, $to)
	{
		$eol = $this->nextEOL($str, $to);
		while ($str[$eol] == "\n" || $str[$eol] == "\r")
			$eol++;
		return substr($str, 0, $eol) . $this->content . substr($str, $eol);
	}
	
	public function TryUnPatch ($str, $from, $to)
	{
		return strpos($str, $this->content, $to) !== false;
	}
	
	public function UnPatch ($str, $from, $to)
	{
		$pos = strpos($str, $this->content, $to);
		return substr($str, 0, $pos) . substr($str, $pos + strlen($this->content));
	}
}