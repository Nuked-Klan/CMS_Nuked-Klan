<?php
define('INDEX_CHECK', 1);

require_once('Includes/nkCaptcha.php');
$nuked['prefix'] = $db_prefix;
require_once('Includes/constants.php');

// CONFIG PHP SESSION
if(ini_get('session.save_handler') == 'files') session_set_save_handler('session_open', 'session_close', 'session_read', 'session_write', 'session_delete', 'session_gc');

if(ini_get('suhosin.session.encrypt') == '1'){
    @ini_set('session.gc_probability', 100);
    @ini_set('session.gc_divisor', 100);
    @ini_set('session.gc_maxlifetime', (1440));
}

session_name('nuked');
session_start();

// Set headers to sent
header("Content-Type: image/jpg");

// Active transparency if available
if(function_exists('imagecreatetruecolor')) {
    $imagecreate = 'imagecreatetruecolor';
} else {
    $imagecreate = 'imagecreate';
}

$image     = $imagecreate(_CT_WIDTH, _CT_HEIGHT);
$tmpImage  = $imagecreate(_CT_WIDTH * 5, _CT_HEIGHT * 5);

$bgRVBColor = colorRGBOutput(_CT_BGCOLOR);
$textRVBColor = colorRGBOutput(_CT_TEXTCOLOR);
$lineRVBColor = colorRGBOutput(_CT_LINECOLOR);
$noiseRVBColor = colorRGBOutput(_CT_NOISECOLOR);

$bgColor = imagecolorallocate($image, $bgRVBColor[0], $bgRVBColor[1], $bgRVBColor[2]);
$textColor = imagecolorallocatealpha($image, $textRVBColor[0], $textRVBColor[1], $textRVBColor[2], 25);
$lineColor = imagecolorallocatealpha($image, $lineRVBColor[0], $lineRVBColor[1], $lineRVBColor[2], 25);
$noiseColor = imagecolorallocatealpha($image, $noiseRVBColor[0], $noiseRVBColor[1], $noiseRVBColor[2], 25);
$errorColor = imagecolorallocate($image, 255, 0, 255);

imagepalettecopy($tmpImage, $image);

imagefilledrectangle($image, 0, 0, _CT_WIDTH, _CT_HEIGHT, $bgColor);
imagefilledrectangle($tmpImage, 0, 0, _CT_WIDTH * 5, _CT_HEIGHT * 5, $bgColor);

// Make noise in background
$points = _CT_WIDTH * _CT_HEIGHT * 5;
$height = _CT_HEIGHT * 5;
$width  = _CT_WIDTH * 5;
for ($i = 0; $i < 250; ++$i) {
    $x = mt_rand(10, $width);
    $y = mt_rand(10, $height);
    $size = mt_rand(7, 10);
    if ($x - $size <= 0 && $y - $size <= 0) continue;
    imagefilledarc($tmpImage, $x, $y, $size, $size, 0, 360, $noiseColor, IMG_ARC_PIE);
}

// Add captcha code
$width2  = _CT_WIDTH * 5;
$height2 = _CT_HEIGHT * 5;
$codeDisplay = captchaGenerator();

if (!is_readable(_CT_TTF_FILE)) {
    imagestring($image, 4, 10, (_CT_HEIGHT / 2) - 2, 'Failed to load TTF font file!', $errorColor);
} else {
    $font_size = $height2 * .4;
    $bb = imageftbbox($font_size, 0, _CT_TTF_FILE, $codeDisplay);
    $tx = $bb[4] - $bb[0];
    $ty = $bb[5] - $bb[1];
    $x  = floor($width2 / 2 - $tx / 2 - $bb[0]);
    $y  = round($height2 / 2 - $ty / 2 - $bb[1]);

    imagettftext($tmpImage, $font_size, 0, $x, $y, $textColor, _CT_TTF_FILE, $codeDisplay);
}

// Make distorsion
for ($i = 0; $i < 3; ++ $i) {
    $px[$i]  = mt_rand(_CT_WIDTH  * 0.2, _CT_WIDTH  * 0.8);
    $py[$i]  = mt_rand(_CT_HEIGHT * 0.2, _CT_HEIGHT * 0.8);
    $rad[$i] = mt_rand(_CT_HEIGHT * 0.2, _CT_HEIGHT * 0.8);
    $tmp     = ((- frand()) * 0.15) - .15;
    $amp[$i] = 0.85 * $tmp;
}

$bgCol = imagecolorat($tmpImage, 0, 0);
$width2 = 5 * _CT_WIDTH;
$height2 = 5 * _CT_HEIGHT;
imagepalettecopy($image, $tmpImage);
for ($ix = 0; $ix < _CT_WIDTH; ++ $ix) {
    for ($iy = 0; $iy < _CT_HEIGHT; ++ $iy) {
        $x = $ix;
        $y = $iy;
        for ($i = 0; $i < 3; ++ $i) {
            $dx = $ix - $px[$i];
            $dy = $iy - $py[$i];
            if ($dx == 0 && $dy == 0) {
                continue;
            }
            $r = sqrt($dx * $dx + $dy * $dy);
            if ($r > $rad[$i]) {
                continue;
            }
            $rscale = $amp[$i] * sin(3.14 * $r / $rad[$i]);
            $x += $dx * $rscale;
            $y += $dy * $rscale;
        }
        $c = $bgCol;
        $x *= 5;
        $y *= 5;
        if ($x >= 0 && $x < $width2 && $y >= 0 && $y < $height2) {
            $c = imagecolorat($tmpImage, $x, $y);
        }
        if ($c != $bgCol) {
            imagesetpixel($image, $ix, $iy, $c);
        }
    }
}

// Draw lines
$num_lines = 3;
for ($line = 0; $line < $num_lines; ++ $line) {
    $x = _CT_WIDTH * (1 + $line) / ($num_lines + 1);
    $x += (0.5 - frand()) * _CT_WIDTH / $num_lines;
    $y = mt_rand(_CT_HEIGHT * 0.1, _CT_HEIGHT * 0.9);

    $theta = (frand() - 0.5) * M_PI * 0.7;
    $w = _CT_WIDTH;
    $len = mt_rand($w * 0.4, $w * 0.7);
    $lwid = mt_rand(0, 2);

    $k = frand() * 0.6 + 0.2;
    $k = $k * $k * 0.5;
    $phi = frand() * 6.28;
    $step = 0.5;
    $dx = $step * cos($theta);
    $dy = $step * sin($theta);
    $n = $len / $step;
    $amp = 1.5 * frand() / ($k + 5.0 / $len);
    $x0 = $x - 0.5 * $len * cos($theta);
    $y0 = $y - 0.5 * $len * sin($theta);

    $ldx = round(- $dy * $lwid);
    $ldy = round($dx * $lwid);

    for ($i = 0; $i < $n; ++ $i) {
        $x = $x0 + $i * $dx + $amp * $dy * sin($k * $i * $step + $phi);
        $y = $y0 + $i * $dy - $amp * $dx * sin($k * $i * $step + $phi);
        imagefilledrectangle($image, $x, $y, $x + $lwid, $y + $lwid, $lineColor);
    }
}

// Generate image
imagejpeg($image, null, 90);

//imagedestroy($image);


function frand()
{
    return 0.0001 * mt_rand(0,9999);
}

// OPEN PHP SESSION
function session_open($path, $name){
    return true;
}

// CLOSE PHP SESSION
function session_close(){
    return true;
}

// READ PHP SESSION
function session_read($id){
    connect();

    $sql = mysql_query('SELECT session_vars FROM ' . TMPSES_TABLE . ' WHERE session_id = "' . $id . '"');
    if(mysql_num_rows($sql) > 0){
        return ($sql === false) ? '' : mysql_result($sql, 0);
    }
}

// WRITE PHP SESSION
function session_write($id, $data){
    $id = mysql_escape_string($id);
    $data = mysql_escape_string($data);

    connect();

    $sql = mysql_query('INSERT INTO ' . TMPSES_TABLE . ' (session_id, session_start, session_vars) VALUES ("' . $id . '", ' . time() . ', \'' . $data . '\')');

    if ($sql === false || mysql_affected_rows() == 0) $sql = mysql_query('UPDATE ' . TMPSES_TABLE . ' SET session_vars = \'' . $data . '\' WHERE session_id = "' . $id . '"');

    return $sql !== false;
}

// DELETE PHP SESSION
function session_delete($id){
    connect();

    $sql = mysql_query('DELETE FROM ' . TMPSES_TABLE . ' WHERE session_id = "' . mysql_escape_string($id) . '"');

    return $sql;
}

// KILL DEAD SESSION
function session_gc($maxlife){
    $time = time() - $maxlife;

    connect();

    mysql_query('DELETE FROM ' . TMPSES_TABLE . ' WHERE session_start < ' . $time);

    return true;
}

// CONNECT TO DB.
function connect(){
    global $global, $db, $language;

    $db = mysql_connect($global['db_host'], $global['db_user'], $global['db_pass']);

    if (!$db){
        echo '<div style="text-align: center;">' . ERROR_QUERY . '</div>';
        exit();
    }

    $connect = mysql_select_db($global['db_name'], $db);
    mysql_query('SET NAMES "latin1"');

    if (!$connect){
        echo '<div style="text-align: center;">' . ERROR_QUERYDB . '</div>';
        exit();
    }
}


?>
