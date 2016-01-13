<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 21.05.2013 0:16:16 YEKT 2013                                             |
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
 * JsonErrorRu.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class ErrorRu
{
    public static function getLastError() {
        switch (json_last_error()) {
            case JSON_ERROR_NONE :
                return 'Ошибок нет';
                break;
            case JSON_ERROR_DEPTH :
                return 'Достигнута максимальная глубина стека';
                break;
            case JSON_ERROR_STATE_MISMATCH :
                return 'Неверный или не корректный JSON';
                break;
            case JSON_ERROR_CTRL_CHAR :
                return 'Ошибка управляющего символа, возможно неверная кодировка';
                break;
            case JSON_ERROR_SYNTAX :
                return 'Синтаксическая ошибка';
                break;
            case JSON_ERROR_UTF8 :
                return 'Некорректные символы UTF-8, возможно неверная кодировка';
                break;
            default :
                return 'Неизвестная ошибка';
                break;
        }
    }
}