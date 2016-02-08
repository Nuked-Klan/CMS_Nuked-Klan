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
 * Initialisation of nkUpload global vars
 * /
$GLOBALS['nkUpload'] = array(
    'error'     => '',
    'extension' => ''
);

/**
 * Check a uploaded file.
 *
 * @param string $filename : The filename from the name attribute of input file.
 * @param array $params : The list of upload parameters
 *   - fileType : The type of allowed upload.
 *        `image` to allow upload image (jpg, jpeg, png & gif)
 *        `no-html-php` to allow all upload file whithout html and php files.
 *        `all` to allow all upload files.
 *        Note : Upload a .htaccess file isn't allowed.
 *   - uploadDir : The path where the uploaded file is moved.
 *   - fileSize : The maximum size allowed for a upload file. (in byte)
 *   - fileRename : If true, rename the file with a random hash.
 *        If false, the filename is cleaning.
 *   - allowedExt : Array of file extension list allowed for upload process.
 * @return array : A numerical indexed array with :
 *         - The path of uploaded file.
 *         - The error message if existing or false.
 *         - The extension file.
 */
function nkUpload_check($filename, $params = array()) {
    if (! array_key_exists('uploadDir', $params))
        trigger_error('You must defined uploadDir key in $params argument of nkUpload_check function !', E_USER_ERROR);

    if (! is_dir($params['uploadDir']))
        return array('', __('UPLOAD_DIRECTORY_NO_EXIST'), '');

    if (! is_writable($params['uploadDir']))
        return array('', __('UPLOAD_DIRECTORY_NO_WRITEABLE'), '');

    if (! isset($params['fileType']) || ! in_array($params['fileType'], array('image', 'no-html-php', 'all')))
        $params['fileType'] = 'no-html-php';

    if (! array_key_exists('fileSize', $params))
        $params['fileSize'] = null;

    if (! isset($params['fileRename']))
        $params['fileRename'] = false;

    if (! isset($params['allowedExt']) || ! is_array($params['allowedExt']) || empty($params['allowedExt']))
        $params['allowedExt'] = null;

    if ($_FILES[$filename]['error'] !== UPLOAD_ERR_OK)
        return array('', nkUpload_getPhpError($params['fileType'], $_FILES[$filename]['error']), '');

    $_FILES[$filename]['name'] = trim($_FILES[$filename]['name']);

    if ($_FILES[$filename]['name'] == '.htaccess')
        return array('', __('NO_UPLOADABLE_FILE'), '');

    if (is_int($params['fileSize']) && $params['fileSize'] < $_FILES[$filename]['size'] / 1000) {
        if ($params['fileType'] == 'image')
            $error = __('UPLOAD_IMAGE_TOO_BIG');
        else
            $error = __('UPLOAD_FILE_TOO_BIG');

        return array('', sprintf($error, $params['fileSize']), '');
    }

    $filenameInfo = pathinfo($_FILES[$filename]['name']);

    if ($params['allowedExt'] !== null && ! in_array($filenameInfo['extension'], $params['allowedExt']))
        return array('', __('NO_UPLOADABLE_FILE'), '');

    if ($params['fileRename'])
        $filenameInfo['filename'] = substr(md5(uniqid()), rand(0, 20), 10);
    else
        nkUpload_cleanFilename($filenameInfo['filename']);

    if ($params['fileType'] == 'image') {
        if (! nkUpload_checkImage($filename, $filenameInfo['extension']))
            return array('', __('BAD_IMAGE_FORMAT'), '');
    }
    else if ($params['fileType'] != 'no-html-php') {
        if (! nkUpload_checkFileType($filename, $filenameInfo['extension']))
            return array('', __('NO_UPLOADABLE_FILE'), '');
    }

    $path = $uploadDir .'/'. $filenameInfo['filename'] .'.'. $filenameInfo['extension'];

    if (! @move_uploaded_file($_FILES[$filename]['tmp_name'], $path))
        return array('', __('UPLOAD_FILE_FAILED'), '');

    @chmod($path, 0644);

    return array($path, false, $filenameInfo['extension']);
}

/**
 * Return internal PHP upload error.
 *
 * @param string $fileType : The type of allowed upload.
 * @param int $error : The error value contain in $_FILES[$filename]['error'].
 * @return string : Return internal PHP upload error message.
 */
// TODO : Translate error message.
function nkUpload_getPhpError($fileType, $error) {
    switch ($error) {
        case UPLOAD_ERR_INI_SIZE :
            if ($fileType == 'image')
                $message = __('UPLOAD_IMAGE_TOO_BIG');
            else
                $message = __('UPLOAD_FILE_TOO_BIG');

            // http://stackoverflow.com/questions/13076480/php-get-actual-maximum-upload-size
            $maxsize  = ini_get('upload_max_filesize');

            $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
            $size = preg_replace('/[^0-9\.]/', '', $size);

            if ($unit) {
                // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
                $maxsize = round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
            }
            else {
                $maxsize = round($size);
            }

            $message = sprintf($message, $maxsize), '');
            break;
        case UPLOAD_ERR_FORM_SIZE :
            $message = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
            break;
        case UPLOAD_ERR_PARTIAL :
            $message = 'The uploaded file was only partially uploaded';
            break;
        case UPLOAD_ERR_NO_FILE :
            $message = 'No file was uploaded';
            break;
        case UPLOAD_ERR_NO_TMP_DIR :
            $message = 'Missing a temporary folder';
            break;
        case UPLOAD_ERR_CANT_WRITE :
            $message = 'Failed to write file to disk';
            break;
        case UPLOAD_ERR_EXTENSION :
            $message = 'File upload stopped by extension';
            break;

        default :
            $message = 'Unknown upload error';
            break;
    }

    return $message; 
}

/**
 * Check a uploaded image.
 *
 * @param string $filename : The filename from the name attribute of input file.
 * @return bool : Return true if uploaded file is a image, false also
 */
function nkUpload_checkImage($filename, &$ext) {
    $mimeType = exif_imagetype($_FILES[$filename]['tmp_name']);

    if ($mimeType == IMAGETYPE_JPEG) {
        if (! in_array($ext, array('jpg', 'jpeg'))) $ext = 'jpg';

        return true;
    }

    if ($mimeType == IMAGETYPE_GIF) {
        if ($ext != 'gif') $ext = 'gif';

        return true;
    }

    if ($mimeType == IMAGETYPE_PNG) {
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
    $a = 'ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ';
    $b = 'AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn';

    $filename = strtr($filename, $a, $b);
    $filename = str_replace(array(' ', '\'', '"'), '_', $filename);
}

?>