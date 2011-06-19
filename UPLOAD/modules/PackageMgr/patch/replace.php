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
if (!defined("INDEX_CHECK"))
{
    die ("<div style=\"text-align: center;\">You cannot open this page directly</div>");
} 


class replace {
	private $match = null;
	private $content;
	
	public function __construct($content,SimpleXMLElement $args)
	{
		$this->content = $content;
		foreach ($args as $key => $value)
		{
			$value = $value->__toString();
			if ($key == 'match')
			{
				$this->match = $value;
			}
		}
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
		$eol = $this->nextEOL($str, $to);
		return $this->match === null || preg_match('`' . $this->match . '`i', substr($str, $from, $eol - $from));
	}
	
	public function Patch ($str, $from, $to)
	{
		if ($this->match === null)
		{
			return substr($str, 0, $from) . $this->content
				. '/*//PATCHED:' . substr($str, $from, $to) . "*/\n"
				. substr($str, $to);
		}
		else
		{
			$eol = $this->nextEOL($str, $to);
			$sub = substr($str, $from, $eol - $from);
			$sub = preg_replace('`' . $this->match . '`i', str_replace('§', '$', addcslashes($this->content, '$')), $sub);
			return substr($str, 0, $from) . $sub . substr($str, $eol);
		}
	}
		
	public function TryUnPatch ($str, $from, $to)
	{
		$eol = $this->nextEOL($str, $to);
		$sub = substr($str, $from, $eol - $from);
		if ($this->match === null)
		{
			return preg_match('`/*//PATCHED:(.+)*/`', $sub) == 1;
		}
		else
		{
			return preg_match('`' . addcslashes($this->content, '$.*+()[]|?^{}\\!<>=:') . '`', $sub) == 1;
		}
	}
	
	public function UnPatch ($str, $from, $to)
	{
		$eol = $this->nextEOL($str, $to);
		$sub = substr($str, $from, $eol - $from);
		if ($this->match === null)
		{
			return substr($str, 0, $from)
				. preg_replace('`.*/*//PATCHED:(.+)*/`', '$1', $sub)
				. substr($str, $eol);
		}
		else
		{
			return substr($str, 0, $from)
				. preg_replace('`' . addcslashes($this->content, '$.*+()[]|?^{}\\!<>=:') . '`', $this->match, $sub)
				. substr($str, $eol);
		}
	}
}

?>
