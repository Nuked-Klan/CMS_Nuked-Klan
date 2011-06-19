<?php
if (!defined("INDEX_CHECK"))
{
    die ("<div style=\"text-align: center;\">You cannot open this page directly</div>");
} 

class XmlExtractor /*implements extractor*/ {
	protected $file;
	
	public function Load($file) {
		$this->file = $file;
	}
	public function ExtractFile($file, $to) {
		if ($file != 'package.xml')
		{
			throw new ExtractError('File not found');
		}
		else
		{
			if (copy($this->file, $to . '/' . $file) === false)
				throw new ExtractError('File not writable: ' . $file . '/' . $to);
		}
	}	
}
