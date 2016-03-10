<?php
/**
 * nkUpload.php
 *
 * Manage uploaded file.
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
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
 * @param string $fieldName : The name attribute of input file.
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
 *   - allowedExtension : Array of file extension list allowed for upload process.
 * @return array : A numerical indexed array with :
 *         - The path of uploaded file.
 *         - The error message if existing or false.
 *         - The extension file.
 */
function nkUpload_check($fieldName, $params = array(), $fileNumber = null) {
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

    if (! array_key_exists('overwrite', $params))
        $params['overwrite'] = true;

    if (! isset($params['fileRename']))
        $params['fileRename'] = false;

    if (! isset($params['allowedExtension']) || ! is_array($params['allowedExtension']))
        $params['allowedExtension'] = array();

    if (! isset($params['renameExtension']) || ! is_array($params['renameExtension']))
        $params['renameExtension'] = array();

    if (! isset($params['tsKeyDataName'])) {
        if ($params['fileType'] == 'image')
            $params['tsKeyDataName'] = 'IMAGE';
        else
            $params['tsKeyDataName'] = 'FILE';
    }

    if (is_array($_FILES[$fieldName]['error'])) {
        if ($fileNumber !== null && array_key_exists($fileNumber, $_FILES[$fieldName]['error'])) {
            $phpError    = $_FILES[$fieldName]['error'][$fileNumber];
            $filename    = $_FILES[$fieldName]['name'][$fileNumber];
            $tmpFilename = $_FILES[$fieldName]['tmp_name'][$fileNumber];
            $filesize    = $_FILES[$fieldName]['size'][$fileNumber];
        }
        else
            return; // TODO : Error?
    }
    else {
        $phpError    = $_FILES[$fieldName]['error'];
        $filename    = $_FILES[$fieldName]['name'];
        $tmpFilename = $_FILES[$fieldName]['tmp_name'];
        $filesize    = $_FILES[$fieldName]['size'];
    }

    if ($phpError !== UPLOAD_ERR_OK)
        return array('', nkUpload_getTranslatedError($params['tsKeyDataName'], 'UPLOAD_%s_FAILED'), '');

    $filename = trim($filename);

    if ($filename == '.htaccess')
        return array('', __('UPLOAD_HTACCESS_PROHIBITED'), '');

    if (is_int($params['fileSize']) && $params['fileSize'] < $filesize)
        return array('', sprintf(nkUpload_getTranslatedError($params['tsKeyDataName'], 'UPLOAD_%s_TOO_BIG'), $params['fileSize']), '');

    $realFilename = pathinfo($filename, PATHINFO_FILENAME);
    $extension    = pathinfo($filename, PATHINFO_EXTENSION);

    if ($params['allowedExtension'] && ! in_array($extension, $params['allowedExtension']))
        return array('', sprintf(nkUpload_getTranslatedError($params['tsKeyDataName'], 'BAD_%s_EXTENSION'), $extension), '');

    if ($params['renameExtension']) {
        foreach ($params['renameExtension'] as $searchedExt => $replaceExt) {
            if (stripos($extension, $searchedExt) !== false)
                $extension = $replaceExt;
        }
    }

    if ($params['fileRename']) {
        $realFilename = substr(md5(uniqid()), rand(0, 20), 10);
    }
    else {
        $realFilename = nkUpload_cleanFilename($realFilename);

        if (isset($params['strtolowerFilename']) && $params['strtolowerFilename'])
            $realFilename = strtolower($realFilename);
    }

    if ($params['fileType'] == 'image') {
        if (! nkUpload_checkImage($tmpFilename, $extension))
            return array('', nkUpload_getTranslatedError($params['tsKeyDataName'], 'BAD_%s_FORMAT'), '');
    }
    // TODO Rewrite this
    else if ($params['fileType'] == 'no-html-php') {
        if (nkUpload_checkFileType($tmpFilename, $extension))
            return array('', sprintf(nkUpload_getTranslatedError($params['tsKeyDataName'], 'BAD_%s_EXTENSION'), $extension), '');
    }

    $path = rtrim($params['uploadDir'], '/') .'/'. $realFilename;

    if ($extension != '')
        $path .= '.'. $extension;

    if (! $params['overwrite'] && is_file($path))
        return array('', nkUpload_getTranslatedError($params['tsKeyDataName'], '%s_ALREADY_EXIST'), '');

    if (! move_uploaded_file($tmpFilename, $path))
        return array('', nkUpload_getTranslatedError($params['tsKeyDataName'], 'UPLOAD_%s_FAILED'), '');

    @chmod($path, 0644);

    return array($path, false, $extension);
}

/**
 * Return upload error translation of current data.
 *
 * @param string $tsKeyDataName : The translation name of current data.
 * @param string $format : The translation key format.
 * @return string : The upload error translation.
 */
function nkUpload_getTranslatedError($tsKeyDataName, $format) {
    $tsActionKey = sprintf($format, $tsKeyDataName);

    if (translationExist($tsActionKey))
        return __($tsActionKey);
    else
        return __(sprintf($format, 'FILE'));
}


/**
 * Return internal PHP upload error.
 *
 * @param string $fileType : The type of allowed upload.
 * @param int $error : The error value contain in $_FILES[$filename]['error'].
 * @return string : Return internal PHP upload error message.
 * /
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

            $message = sprintf($message, $maxsize);
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
}*/

/**
 * Check a uploaded image.
 *
 * @param string $tmpFilename : The path of uploaded file in temporary directory.
 * @return bool : Return true if uploaded file is a image, false also
 */
function nkUpload_checkImage($tmpFilename, &$ext) {
    $mimeType = @exif_imagetype($tmpFilename);

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
 * @param string $tmpFilename : The path of uploaded file in temporary directory.
 * @param string $fileType : The type of allowed upload.
 * @return bool : Return true if uploaded file isn't a html and php file, false also
 */
function nkUpload_checkFileType($tmpFilename, $ext) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $tmpFilename);
    finfo_close($finfo);

    return in_array($mime, array('text/html', 'text/x-php'))
        // .html .htm
        || strpos($ext, 'htm') !== false
        // .php .phtml .php3 .php4 .php5 .phps
        || strpos($ext, 'php') !== false;
}

/**
 * Clean filename and return it.
 *
 * @param string $filename : The filename from the name attribute of input file.
 * @return string
 */
function nkUpload_cleanFilename($filename) {
    $a = 'ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ';
    $b = 'AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn';

    $filename = strtr($filename, $a, $b);

    return str_replace(array(' ', '\'', '"'), '_', $filename);
}

?>
