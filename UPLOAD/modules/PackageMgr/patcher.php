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

class PatchError extends PackageException
{
	public function __construct($message, $code = null)
	{
		if ($code === null)
		{
			$this->message = $message;
		}
		else
			switch ($code)
			{
				default:
					$this->message = $message;
			}
	}
}

class Patch
{
	protected $find;
	protected $actions;
	
	public function __construct(SimpleXMLElement $Patch)
	{
		foreach ($Patch as $name => $action)
		{
			if ($name == 'find')
			{
				$this->find = $this->mkPattern($action[0]->__toString());
			}
			elseif (!file_exists(dirname(__FILE__) . '/patch/' . $name . '.php'))
			{
				throw new PatchError ('A necessary path isn\'t installed: '.$name);
			}
			else
			{
				require_once 'patch/' . $name . '.php';
				$this->actions[] = new $name($action[0]->__toString(), $action->attributes());
			}
		}
	}
	
	public function test($str)
	{
		if (preg_match($this->find, $str, $m, PREG_OFFSET_CAPTURE) == 0)
		{
			throw new PatchError('No find match.');
		}
		$from = $m[0][1];
		$to = $m[0][1] + strlen($m[0][0]);
		foreach ($this->actions as $patch)
			if (!$patch->TryPatch($str, $from, $to))
			{
				throw new PatchError('We can\'t applie a patch, so the action is aborted.');
			}
	}

	public function testUnpatch($str)
	{
		if (preg_match($this->find, $str, $m, PREG_OFFSET_CAPTURE) == 0)
		{
			throw new PatchError('No find match.');
		}
		$from = $m[0][1];
		$to = $m[0][1] + strlen($m[0][0]);
		foreach ($this->actions as $patch)
			if (!$patch->TryUnPatch($str, $from, $to))
			{
				throw new PatchError('We can\'t applie a patch, so the action is aborted.');
			}
	}
	
	public function doUnPatch ($str)
	{
		preg_match($this->find, $str, $m, PREG_OFFSET_CAPTURE);
		$from = $m[0][1];
		$to = $m[0][1] + strlen($m[0][0]);
		foreach ($this->actions as $patch)
			$str = $patch->UnPatch($str, $from, $to);
		return $str;
	}
	
	public function doPatch($str)
	{
		preg_match($this->find, $str, $m, PREG_OFFSET_CAPTURE);
		$from = $m[0][1];
		$to = $m[0][1] + strlen($m[0][0]);
		foreach ($this->actions as $patch)
			$str = $patch->Patch($str, $from, $to);
		return $str;
	}
	
	protected function mkPattern($pattern)
	{
		$pattern = str_replace(array("\n", "\r", "\t"), ' ', $pattern);
		$pattern = trim(addcslashes($pattern, "$.*+()[]|?^{}\\!<>=:"));
		$pile = array();
		$pattern = str_replace(' ', '[\\t\\r\\n ]+', $pattern);
		return '`' . $pattern . '`i';
	}
}