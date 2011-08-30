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
if(extension_loaded('zip')){
	require_once 'extractors/zip.php';
}
require_once 'patcher.php';

class PackageException extends Exception
{
	public function __construct($code)
	{
		$this->code = $code;
		switch ($this->code)
		{
			case Package::E_FILENAME:
				$this->message = 'Nom de fichier incorrect';
				break;
			case Package::E_FILEEXIST:
				$this->message = 'Fichier inexistant';
				break;
			case Package::E_NOTINSTALLED:
				$this->message = 'Patch non install&#233;';
				break;
			case Package::E_TMPDIR:
				$this->message = 'Dossier temporaire inexistant';
				break;
			case Package::E_APPLI:
				$this->message = 'Fichier n&#233;cessaire au patch introuvable';
				break;
			case Package::E_ISACTIVE:
				$this->message = 'Ce patch est d&#233;j&agrave; activ&#233;.';
				break;
			case Package::E_XMLLOAD:
				$this->message = 'Impossible de charger la description XML.';
				break;
			case Package::E_NOTACTIVE:
				$this->message = 'Ce patch est d&#233;j&agrave; d&#233;sactiv&#233;';
				break;
			case Package::E_NOCODEC:
				$this->message = 'Format non pris en charge';
				break;
			case Package::E_NOZIP:
				$this->message = 'Votre h&#233;bergement ne permet la gestion des fichiers zip.';
				break;
			default:
				$this->message = 'Erreur inconnue';
				break;
		}
	}
}

class ExtractError extends PackageException
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

class Package
{
	protected $action = array();
	protected $cfiles = array();
	protected $cdirectory = array();
	protected $install = null;
	protected $uninstall = null;
	protected $name = '';
	protected $author = '';
	protected $link = '';
	
	protected $file;
	protected $active;
	protected $extractor;

	public $table;
	public $directory;
	
	const E_FILENAME = 1;
	const E_FILEEXIST = 2;
	const E_NOTINSTALLED = 3;
	const E_TMPDIR = 4;
	const E_XMLLOAD = 5;
	const E_APPLI = 6;
	const E_ISACTIVE = 7;
	const E_NOTACTIVE = 8;
	const E_NOCODEC = 9;
	const E_NOZIP = 10;
	
	public function __construct ($file, $directory = null)
	{
		$this->file = $file;
		if ($directory === null)
			$directory = dirname(__FILE__) . '/../../upload/';
		$this->directory = $directory;
		$this->file = $file;
		$this->table = '`' . $GLOBALS['nuked']['prefix'] . '_packages`';
		if (!file_exists($this->directory) || !is_dir($this->directory))
		{
			throw new PackageException (self::E_TMPDIR);
		}
		elseif (preg_match('`^[a-z0-9_.-]+$`i', $this->file) == 0)
		{
			throw new PackageException (self::E_FILENAME);
		}
		elseif (!file_exists($this->directory . $this->file) || !is_file($this->directory . $this->file))
		{
			throw new PackageException (self::E_FILEEXIST);
		}
		$file = strtolower($this->file);
		$this->extractor = $this->getExtractor();
		echo $this->extractor->Load($this->directory . $this->file);
	}
	
	protected function getExtractor()
	{
		$ext = substr(strtolower(strrchr($this->file, '.')), 1);
		$driver = dirname(__FILE__) . '/extractors/' . $ext . '.php';
		if(!extension_loaded('zip') && $ext == 'zip'){
			throw new PackageException(self::E_NOZIP);
		}
		$class = ucfirst($ext) . 'Extractor';
		if (file_exists($driver) && is_file($driver))
		{
			require_once $driver;
			return new $class();
		}
		else
			throw new PackageException(self::E_NOCODEC);
	}
	
	protected function readHeader ()
	{
		$tmp = $this->directory . 'package.xml';
		@unlink($tmp);
		$this->extractor->ExtractFile('package.xml', $this->directory);
		$xml = simplexml_load_file($tmp);

		$header = $xml->header;
		if ($header->link != null)
			$this->link = $header->link[0];
		if ($header->author != null)
			$this->author = $header->author[0];
		if ($header->name != null)
			$this->name = $header->name[0];
	}
		
	protected function readPack ()
	{
		
		$tmp = $this->directory . 'package.xml';
		@unlink($tmp);
		$this->extractor->ExtractFile('package.xml', $this->directory);
		$xml = simplexml_load_file($tmp);

		$body = $xml->body;
		if (isset($body->files))
		{
			foreach ($body->files->copy as $copy)
			{
				$attr = $copy->attributes();
				if (isset($attr['from']))
				{
					$this->cfiles[] = array('to' => $attr['to']->__toString(), 'from' => $attr['from']->__toString(), 'content' => null);
				}
				else
					$this->cfiles[] = array('to' => $attr['to']->__toString(), 'content' => $copy->__toString());
			}
			
			foreach ($body->files->directory as $dir)
				$this->cdirectory[] = dirname(__FILE__) . '/../../' . $dir->__toString();
			
		}
		if (isset($body->patcher))
		{
			foreach ($body->patcher->file as $file)
			{
				$name = $file->attributes();
				foreach ($file as $patch)
					$this->action[$name['name']->__toString()][] = new Patch($patch);
			}
		}
		if (isset($body->oninstall))
			$this->install = $body->oninstall;
		if (isset($body->onremove))
			$this->uninstall = $body->onremove;
	}
	
	protected function exec($list)
	{
		if ($list !== null)
		{
			foreach ($list->children() as $action => $content)
			{
				$content = $content->__toString();
				switch ($action)
				{
					case 'sql':
						$content = str_replace('{PREFIX}', $GLOBALS['nuked']['prefix'], $content);
						mysql_query($content);
						break;
					case 'eval':
						eval ($content);
						break;
					case 'exec':
						include dirname(__FILE__) . '/../../' . $content;
						break;
				}
			}
		}
	}
	
	public function activate()
	{
		if ($this->isInstalled() && $this->active == 0)
		{
			$this->readPack();
			
			foreach ($this->cfiles as $file)
			{
				if ($file['content'] == null && file_exists($file['to']))
					throw new PackageException(self::E_APPLI);
			}
			
			foreach ($this->action as $file => $patchers)
			{
				$file = dirname(__FILE__) . '/../../' . $file;
				if (!file_exists($file) || !is_file($file))
					throw new PackageException(self::E_APPLI);
				$content = file_get_contents($file);
				foreach ($patchers as $patch)
					$patch->test($content);
			}
			
			/* Start Activation */
			foreach ($this->cdirectory as $dir)
				if (!file_exists($dir))
					mkdir($dir);
			
			foreach ($this->cfiles as $file)
			{
				if ($file['content'] != null)
				{
					file_put_contents($file['to'], $file['content']);
				}
				else
					$this->extractor->ExtractFile($file['from'], dirname($file['to']));
			}
			
			foreach ($this->action as $file => $patchers)
			{
				$file = dirname(__FILE__) . '/../../' . $file;
				$content = file_get_contents($file);
				foreach ($patchers as $patch)
					$content = $patch->doPatch($content);
				file_put_contents($file, $content);
			}
			
			$this->exec($this->install);
			mysql_query('UPDATE ' . $this->table . ' SET active = 1 WHERE file=\'' . $this->file . '\'');
			$this->active == 1;
		}
		else
		{
			throw new PackageException (self::E_ISACTIVE);
		}
	}
	
	public function deactivate()
	{
		if ($this->isInstalled() && $this->active == 1)
		{
			$this->readPack();

			foreach ($this->action as $file => $patchers)
			{
				$file = dirname(__FILE__) . '/../../' . $file;
				if (!file_exists($file) || !is_file($file))
					throw new PackageException(self::E_APPLI);
				$content = file_get_contents($file);
				foreach ($patchers as $patch)
					$patch->testUnpatch($content);
			}
			
			/* Start Activation */
			foreach ($this->cfiles as $file)
			{
				@unlink ($file['to']);
			}
			
			foreach ($this->cdirectory as $dir)
				if (file_exists($dir))
					rmdir($dir);
			
			foreach ($this->action as $file => $patchers)
			{
				$file = dirname(__FILE__) . '/../../' . $file;
				$content = file_get_contents($file);
				foreach ($patchers as $patch)
					$content = $patch->doUnPatch($content);
				file_put_contents($file, $content);
			}
			
			$this->exec($this->uninstall);
			mysql_query('UPDATE ' . $this->table . ' SET active = 0 WHERE file=\'' . $this->file . '\'');
			$this->active == 0;
		}
		else
		{
			throw new PackageException (self::E_NOTACTIVE);
		}
	}
	
	private function isInstalled ($error = true)
	{
		$cnt = mysql_query('SELECT * FROM ' . $this->table . ' WHERE file=\'' . $this->file . '\'');
		if (mysql_num_rows($cnt) == 1)
		{
			$pkg = mysql_fetch_assoc($cnt);
			$this->active = $pkg['active'];
			return true;
		}
		elseif ($error)
		{
			throw new PackageException (self::E_NOTINSTALLED);
		}
		else
		{
			return false;
		}
	}
	
	public function install()
	{
		$this->readHeader();
		mysql_query('INSERT INTO ' . $this->table . ' (file, name, author, link, active) VALUES ('
			. '\'' . $this->file . '\','
			. '\'' . $this->name . '\','
			. '\'' . $this->author . '\','
			. '\'' . $this->link . '\','
			. '\'0\')'
		);
		echo mysql_error();
	}
	
	public function uninstall()
	{
		if ($this->isInstalled())
		{
			if ($this->active == 1)
				$this->deactivate();
			mysql_query('DELETE FROM ' . $this->table . ' WHERE file = \'' . $this->file . '\' LIMIT 1');
			unlink($this->directory . $this->file);
		}
		else
		{
			throw new PackageException (self::E_ISACTIVE);
		}
	}
}