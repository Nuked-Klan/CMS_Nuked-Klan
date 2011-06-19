<?php
if (!defined("INDEX_CHECK"))
{
    die ("<div style=\"text-align: center;\">You cannot open this page directly</div>");
} 

class GzExtractor /*implements extractor*/ {
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
			$from = gzopen($this->file, 'r');
			if ($from === false)
				throw new ExtractError('File unreadeble.');
			$to = fopen($to . '/' . $file, 'w');
			if ($to === false)
			{
				throw new ExtractError('File not writable: ' . $file . '/' . $to);
			}
			fwrite($to, gzread($from, filesize($this->file)));
			fclose($to);
			gzclose($from);
		}
	}	
}

?>
