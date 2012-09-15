<?php
//-------------------------------------------------------------------------//
//  Nuked-KlaN - PHP Portal                                                //
//  http://www.nuked-klan.org                                              //
//-------------------------------------------------------------------------//
//  This program is free software. you can redistribute it and/or modify   //
//  it under the terms of the GNU General Public License as published by   //
//  the Free Software Foundation; either version 2 of the License.         //
//-------------------------------------------------------------------------//
define('INDEX_CHECK', 1);
include ("globals.php");
include ("conf.inc.php");

if (is_file('nuked.php')) include('nuked.php');
else die('<br /><br /><div style=\"text-align: center;\"><b>updateGD.php must be near nuked.php</b></div>');

function style(){
    echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
    . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n"
    . "<head><title>Nuked-KlaN 1.7</title>\n"
    . "<style>\n"
    . "h3{color:#666666;font-size:18px;font-weight:bold;font-family:Arial, verdana, Sans serif;line-height:0px;}\n"
    . "a:link,a:visited,a:hover,a:active{color:#666666;font-size:12px;font-weight:bold;font-family:Arial, verdana, Sans serif;}\n"
    . "body{background-color:#CCCCCC;color:#333333;font-family:arial,verdana, sans serif;}\n"
    . "input{background-color:#999999;color:#FFFFFF;border: solid 1 rgb( 0 0 0);}\n"
    . "select{background-color:#999999;color:#FFFFFF;border: solid 1 rgb( 0 0 0);}\n"
    . ".barre{width:1px;background-color:white;align:left;}\n"
    . "</style>\n"
    . "</head>\n"
    . "<body>\n";
}

function index(){
    global $nuked;

    style();

    if ($nuked['version'] == "SP4.6" || $nuked['version'] == "1.7.9"){
		echo "<br /><br /><div style=\"text-align: center;\"><b>Do you want create GD thumbnail for Gallery Module ?</b><br /><br />\n"
		. "<input type=\"button\" name=\"install\" onclick=\"document.location.href='updateGD.php?op=update';\" value=\"Confirm\" />"
		. "&nbsp;<input type=\"button\" name=\"No\" onclick=\"document.location.href='updateGD.php?op=nan';\" value=\"Cancel\" /></div></body></html>";
    }
    else{
		echo "<br /><br /><div style=\"text-align: center;\"><b>Error : Bad version, only nuked-klan 1.7/SP4 can be updated !</b></div>";
    }
}

function update(){
    global $nuked;

    style();

    $sql = mysql_query("SELECT sid, url, url2 FROM " . $nuked['prefix'] . "_gallery");
    while (list($sid, $url, $url2) = mysql_fetch_array($sql)){
		$img_name = substr(strrchr($url, '/'), 1 );
		$test_url = substr($url, 7);
		if ($url2 == "" && @extension_loaded('gd') && strlen($test_url) != 0){
			$size = getimagesize($url);
			if ($size[0] > 150){
				$f = explode(".", $img_name);
				$end = count($f) - 1;
				$ext = $f[$end];
				$file_name = preg_replace("/\." . $ext."/", "", $img_name);
				if (preg_match("`jpg`i", $ext) || preg_match("`jpeg`i", $ext)) $src = imagecreatefromjpeg($url);
				if (preg_match("`png`i", $ext)) $src = imagecreatefrompng($url);
				if (preg_match("`gif`i", $ext)) $src = imagecreatefromgif($url);
				if (preg_match("`bmp`i", $ext)) $src = imagecreatefromwbmp($url);
		
				$img = imagecreatetruecolor(150, round((150/$size[0])*$size[1]));
				if (!$img) $img = imagecreate(150, round((150/$size[0])*$size[1]));
		
				imagecopyresampled($img, $src, 0, 0, 0, 0, 150, round($size[1]*(150/$size[0])), $size[0], $size[1]);
		
				$temp = "upload/Gallery/" . $file_name . "_tmb." . $ext;
				echo 'temp = '.$temp.'<br/>';
				if (is_file($temp)) $miniature = "upload/Gallery/" . time() . $file_name . "_tmb." . $ext;
				else  $miniature = $temp;
		
				if (preg_match("`jpg`i", $ext) || preg_match("`jpeg`i", $ext)) ImageJPEG($img, $miniature);
				if (preg_match("`png`i", $ext)) ImagePNG($img, $miniature);
				if (preg_match("`bmp`i", $ext)) imagewbmp($img, $miniature);
		
				if (preg_match("`gif`i", $ext) && function_exists("imagegif")) ImageGIF($img, $miniature);
				else ImageJPEG($img, $miniature);
				echo 'miniature = '.$miniature.'<br/>';
				if (is_file($miniature)) $url2 = $miniature;
			}
		}

        $upd = mysql_query("UPDATE " . $nuked['prefix'] . "_gallery SET url2 = '" . $url2 . "' WHERE sid = '" . $sid . "'");
    }

    echo "<br /><br /><div style=\"text-align: center;\"><b>GD thumbnail created successfully.<br />Remove updateGD.php from your FTP.</b></div>";

    if (is_file("updateGD.php")){
		$path="updateGD.php";
		$filesys = str_replace("/", "\\", $path);
		@chmod ($path, 0775);
		@unlink($path);
    }

    redirect("index.php", 3);
}

function nan(){
    style();

    echo "<br /><br /><div style=\"text-align: center;\"><b>Update canceled...</div>";
    redirect("index.php", 3);
}

switch($_REQUEST['op']){
    case"index":
    index();
    break;

    case"update":
    update();
    break;

    case"nan":
    nan();
    break;

    default:
    index();
    break;
}
?>