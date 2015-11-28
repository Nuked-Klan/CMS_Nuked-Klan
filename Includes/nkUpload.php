<?php
/**
 * 
 *
 * 
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');


function nkUpload_check($filename, $fileType, $uploadDir) {
    if (nkUpload_checkFileType($filename, $fileType)) {
        $path = $uploadDir .'/'. $_FILES[$filename]['name'];

        if (! is_writable($path))
            return array('', _UPLOADDIRNOWRITEABLE);

        if (! @move_uploaded_file($_FILES[$filename]['tmp_name'], $path))
            return array('', _UPLOADFILEFAILED);

        @chmod($path, 0644);

        return array($path, false);
    }
    else {
        return array('', _NOIMAGEFILE);
    }
}

function nkUpload_checkFileType($filename, $fileType) {
    if ($fileType == 'image') {
        $imgInfo = @getimagesize($_FILES[$filename]['tmp_name']);

        return $imgInfo !== false && in_array($imgInfo[2], array(IMG_JPEG, IMG_GIF, IMG_PNG));
    }
    else {
        //
    }
}

?>