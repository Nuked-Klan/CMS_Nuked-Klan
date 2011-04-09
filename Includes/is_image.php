<?php
function is_image($file)
{
	$sfile = new finfo(FILEINFO_MIME);
	if (strpos(strtolower(strrchr($file, '.')), 'jpg') !== false
		|| strpos(strtolower($sfile->file($file)), 'jpg') !== false)
	{
		unset($sfile);
		return true;
	}
	if (strpos(strtolower(strrchr($file, '.')), 'jpeg') !== false
		|| strpos(strtolower($sfile->file($file)), 'jpeg') !== false)
	{
		unset($sfile);
		return true;
	}
	if (strpos(strtolower(strrchr($file, '.')), 'png') !== false
		|| strpos(strtolower($sfile->file($file)), 'png') !== false)
	{
		unset($sfile);
		return true;
	}
	if (strpos(strtolower(strrchr($file, '.')), 'bmp') !== false
		|| strpos(strtolower($sfile->file($file)), 'bmp') !== false)
	{
		unset($sfile);
		return true;
	}
	if (strpos(strtolower(strrchr($file, '.')), 'gif') !== false
		|| strpos(strtolower($sfile->file($file)), 'gif') !== false)
	{
		unset($sfile);
		return true;
	}
	if (strpos(strtolower(strrchr($file, '.')), 'jpe') !== false
		|| strpos(strtolower($sfile->file($file)), 'jpe') !== false)
	{
		unset($sfile);
		return true;
	}
	unset($sfile);
	return false;
}
?>