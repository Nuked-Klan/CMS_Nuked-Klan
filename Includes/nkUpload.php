<?php
/**
 * nkUpload.php
 *
 * Manage uploaded file.
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');


/**
 * Check a uploaded file.
 *
 * @param string $filename : The filename from the name attribute of input file.
 * @param string $fileType : The type of allowed upload.
 *        `image` to allow upload image (jpg, jpeg, png & gif)
 *        `no-html-php` to allow all upload file whithout html and php files.
 *        `all` to allow all upload files.
 *        Note : Upload a .htaccess file isn't allowed.
 * @param string $uploadDir : The path where the uploaded file is moved.
 * @param int $maxsize : The maximum size allowed for a upload file. (in byte)
 * @param bool $rename : If true, rename the file with a random hash.
 *        If false, the filename is cleaning.
 * @return array : A numerical indexed array with :
 *         - The path of uploaded file.
 *         - The error message if existing or false.
 *         - The extension file.
 */
function nkUpload_check($filename, $fileType, $uploadDir, $maxsize = null, $rename = false) {
    $_FILES[$filename]['name'] = trim($_FILES[$filename]['name']);

    if ($filename == '.htaccess')
        return array('', _NOUPLOADABLEFILE, '');

    if (is_int($maxsize) && $maxsize < $_FILES[$filename]['size'] / 1000)
        return array('', _UPLOADFILETOOBIG, '');

    $filenameInfo = pathinfo($_FILES[$filename]['name']);

    if ($rename)
        $filenameInfo['filename'] = substr(md5(uniqid()), rand(0, 20), 10);
    else
        nkUpload_cleanFilename($filenameInfo['filename']);

    if ($fileType == 'image') {
        if (! nkUpload_checkImage($filename, $filenameInfo['extension']))
            return array('', _NOIMAGEFILE, '');
    }
    else if ($fileType != 'no-html-php') {
        if (! nkUpload_checkFileType($filename, $filenameInfo['extension']))
            return array('', _NOUPLOADABLEFILE, '');
    }

    $path = $uploadDir .'/'. $filenameInfo['filename'] .'.'. $filenameInfo['extension'];

    if (! is_writable($uploadDir))
        return array('', _UPLOADDIRNOWRITEABLE, '');

    if (! @move_uploaded_file($_FILES[$filename]['tmp_name'], $path))
        return array('', _UPLOADFILEFAILED, '');

    @chmod($path, 0644);

    return array($path, false, $filenameInfo['extension']);
}

/**
 * Check a uploaded image.
 *
 * @param string $filename : The filename from the name attribute of input file.
 * @return bool : Return true if uploaded file is a image, false also
 */
function nkUpload_checkImage($filename, &$ext) {
    $mimeType = exif_imagetype($_FILES[$filename]['tmp_name']);

    if ($mimeType == IMG_JPEG) {
        if (! in_array($ext, array('jpg', 'jpeg'))) $ext = 'jpg';

        return true;
    }

    if ($mimeType == IMG_GIF) {
        if ($ext != 'gif') $ext = 'gif';

        return true;
    }

    if ($mimeType == IMG_PNG) {
        if ($ext != 'png') $ext = 'png';

        return true;
    }

    return false;
}

/**
 * Check a uploaded file.
 *
 * @param string $filename : The filename from the name attribute of input file.
 * @param string $fileType : The type of allowed upload.
 * @return bool : Return true if uploaded file isn't a html and php file, false also
 */
function nkUpload_checkFileType($filename, $ext) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $_FILES[$filename]['tmp_name']);
    finfo_close($finfo);

    return in_array($mime, array('text/html', 'text/x-php'))
        // .html .htm
        || strpos($ext, 'htm') !== false
        // .php .phtml .php3 .php4 .php5 .phps
        || strpos($ext, 'php') !== false;
}

/**
 * Clean filename.
 *
 * @param string $filename : The filename from the name attribute of input file.
 * @return void
 */
function nkUpload_cleanFilename(&$filename) {
    $a = 'ְֱֲֳִֵאבגדהוׂ׃װױײ״עףפץצרָֹÊֻטיךכַחּֽ־ֿלםמןÙÚÛÜשתûüÿׁס';
    $b = 'AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn';

    $filename = strtr($filename, $a, $b);
    $filename = str_replace(array(' ', '\'', '"'), '_', $filename);
}

?>