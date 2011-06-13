<?php
if (!defined("INDEX_CHECK")) exit('You can\'t run this file alone.');

/**
 * Securised hash for NK
 * @param string pass
 * @return string hashed pass
 **/
function nk_hash($pass, $decal = null){
	$bulder = '';
	$decal = $decal === null?rand(0, 15):$decal;
	$pass = sha1($pass);
	for ($i = 0; $i < strlen($pass) * 2; $i++){
		if ($i % 2 == 0){
			$builder .= $pass[$i / 2];
		}
		else{
			$builder .= substr(HASHKEY, ($i / 2 + $decal) % 20, 1);
		}
	}
	return '#'.dechex($decal).md5($builder);
}

/**
 * Check if a pass is agree with a hash
 * @param string pass
 * @param string NK_Hash
 **/
function Check_Hash($pass, $hash){
	if (substr($hash, 0, 1) == '%')
		$pass = md5($pass);

	if (substr($hash, 0, 1) == '%' || substr($hash, 0, 1) == '#'){
		$decal = hexdec(substr($hash, 1, 1));
		return substr($hash, 1) == substr(nk_hash($pass, $decal), 1);
	}
	else
		die('Have you run update.php ? Old password was dissallowed !');
}

function FileList($dir){
	$dirs = scandir($dir);
	$dirs = array_diff($dirs, array_intersect($dirs, array('.', '..')));
	$return = array();
	$size = count($dirs);
	for($i=0; $i<$size; $i++){
		if (!is_dir($dirs[$i])) {
			$return[] = $dir . '/' . $dirs[$i];
		}
		else{
			$return = array_merge($return, FileList($dir . '/' . $dirs[$i]));
		}
	}
	return $return;
}
?>