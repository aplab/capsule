<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 21.05.2013 0:15:43 YEKT 2013                                             |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Json;

/**
 * JsonError.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Error
{
    public static function getLastError() {
        switch (json_last_error()) {
            case JSON_ERROR_NONE :
                return 'No error has occurred';
                break;
            case JSON_ERROR_DEPTH :
                return 'The maximum stack depth has been exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH :
                return 'Invalid or malformed JSON';
                break;
            case JSON_ERROR_CTRL_CHAR :
                return 'Control character error, possibly incorrectly encoded';
                break;
            case JSON_ERROR_SYNTAX :
                return 'Syntax error';
                break;
            case JSON_ERROR_UTF8 :
                return 'Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            default :
                return 'Unknown error';
                break;
        }
    }
}