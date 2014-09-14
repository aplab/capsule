<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 31.08.2014 15:47:28 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\File\Upload;

/**
 * Msg.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Msg
{
    protected static $msg = array(
        0=> 'There is no error, the file uploaded with success',
        1=> 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        2=> 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        3=> 'The uploaded file was only partially uploaded',
        4=> 'No file was uploaded',
        6=> 'Missing a temporary folder'
    );
    
    public static function msg($code) {
        settype($code, 'int');
        return isset(self::$msg[$code]) ? self::$msg[$code] : 'Unknown error'; 
    }
}