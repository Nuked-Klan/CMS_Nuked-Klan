<?php
/**
 * nkUpload.php
 *
 * Manage uploaded file.
 *
 * @version     1.8
 * @link https://nuked-klan.fr Clan Management System for Gamers
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
 * NOTE : Upload a .htaccess file is not allowed.
 *
 * @param string $fieldName : The name attribute of input file.
 * @param array $params : The list of upload parameters
 *   - uploadDir (string) : The path where the uploaded file is moved.
 *   - fileSize (int) : The maximum size allowed for a upload file. (in byte)
 *   - overwrite (bool) : If true and file exist, ovewrite it, return error message also.
 *   - fileRename (bool) : If true, rename the file with a random hash.
 *                         If false, the filename is cleaning.
 *   - allowedExtension (array) : List of file extension allowed for upload process.
 *   - disallowedExtension (array) : List of file extension disallowed for upload process.
 *                                   NOTE : php and html files have a MIME type check only.
 *   - renameExtension (bool) : If true, generate a random filename.
 *   - tsKeyDataName (string) : The translation name of current data.
 *   - strtolowerFilename (bool) : If true, apply strtolower on filename.
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

    if (! array_key_exists('fileSize', $params))
        $params['fileSize'] = null;

    if (! array_key_exists('overwrite', $params))
        $params['overwrite'] = true;

    if (! isset($params['fileRename']))
        $params['fileRename'] = false;

    if (! isset($params['allowedExtension']) || ! is_array($params['allowedExtension']))
        $params['allowedExtension'] = array();

    if (! isset($params['disallowedExtension']) || ! is_array($params['disallowedExtension']))
        $params['disallowedExtension'] = array();

    if ($params['allowedExtension'] && ! array_diff($params['allowedExtension'], array('jpg', 'png', 'gif', 'bmp')))
        $uploadImage = true;
    else
        $uploadImage = false;

    if (! isset($params['renameExtension']) || ! is_array($params['renameExtension']))
        $params['renameExtension'] = array();

    if (! isset($params['tsKeyDataName'])) {
        if ($uploadImage)
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

    if ($params['allowedExtension'] && ! in_array($extension, $params['allowedExtension'])) {
        $extDisplay = ($extension) ? '('. $extension .')' : '';
        return array('', sprintf(nkUpload_getTranslatedError($params['tsKeyDataName'], 'BAD_%s_EXTENSION'), $extDisplay), '');
    }

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

    if ($uploadImage) {
        if (! nkUpload_checkImage($tmpFilename, $extension))
            return array('', nkUpload_getTranslatedError($params['tsKeyDataName'], 'BAD_%s_FORMAT'), '');
    }
    else {
        if ($params['disallowedExtension']) {
            $mime = nkUpload_getMimeType($tmpFilename);
            $extensionError = false;

            // Check and protect against dangerous extension if disallowed :
            // .php .phtml .php3 .php4 .php5 .phps & .html .htm
            foreach (array('php', 'htm') as $badExtension) {
                foreach ($params['disallowedExtension'] as $disallowedExtension) {
                    if (stripos($disallowedExtension, $badExtension) !== false
                        && stripos($extension, $badExtension) !== false
                        && stripos($mime, $badExtension) !== false
                    ) {
                        $extensionError = true;
                    }
                }
            }

            if ($extensionError || in_array($extension, $params['disallowedExtension'])) {
                $extDisplay = ($extension) ? '('. $extension .')' : '';
                return array('', sprintf(nkUpload_getTranslatedError($params['tsKeyDataName'], 'BAD_%s_EXTENSION'), $extDisplay), '');
            }
        }
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
 * Get MIME type of uploaded file.
 *
 * @param string $tmpFilename : The path of uploaded file in temporary directory.
 * @return string
 */
function nkUpload_getMimeType($tmpFilename) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $tmpFilename);
    finfo_close($finfo);

    return $mime;
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
