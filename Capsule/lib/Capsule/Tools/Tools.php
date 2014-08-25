<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 20.05.2013 23:30:01 YEKT 2013                                             |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Tools;

/**
 * Tools.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Tools
{
    public static function dump($var, $html = true) {
        ob_start();
        echo"\n";
        echo'[Tools::dump]';
        echo $html ? '<pre>' : '';
        $trace = debug_backtrace(null, 1);
        $trace = array_shift($trace);
        if ($trace) {
            echo'file: ' . $trace['file'] . "\n";
            echo'line: ' . $trace['line'] . "\n";
        }
        var_dump($var);
        echo $html ? '</pre>' : '';
        echo'[/Tools::dump]';
        echo"\n";
        echo ob_get_clean();
    }
    
    public static function export($var, $html = true) {
        echo"\n";
        echo $html ? '<pre>' : '';
        var_export($var);
        echo $html ? '</pre>' : '';
        echo"\n";
    }
}
