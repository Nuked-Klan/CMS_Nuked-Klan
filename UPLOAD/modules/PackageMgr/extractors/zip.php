<?php
if (!defined("INDEX_CHECK"))
{
    die ("<div style=\"text-align: center;\">You cannot open this page directly</div>");
} 

//require_once '../interfaces/extractor.php'; 
class ZipExtractor extends ZipArchive /*implements extractor*/ {

	public function Load($archive) {
		switch ($this->open($archive)) {
			case ZIPARCHIVE::ER_NOZIP:
				throw new ExtractError('This is not a zip file');
			case ZIPARCHIVE::ER_READ:
				throw new ExtractError('Error, we can\'t read the file');
			case ZIPARCHIVE::ER_OPEN:
				throw new ExtractError('Error, we can\' open the file');
			default:
				return true;
		}
	}
	public function ExtractFile($file, $to) {
		return $this->extractTo($to, $file);
	}	
}
?>