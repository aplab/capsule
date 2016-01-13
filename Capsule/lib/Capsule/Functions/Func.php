<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 27.05.2013 21:59:00 YEKT 2013                                             |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule;

use Capsule\Common\Filter;
/**
 * Func.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Func
{
    /**
     * Объединяет непустые элементы массива в строку
     *
     * @param unknown $glue
     * @param array $pieces
     */
    public static function joinNonempty($glue, array $pieces) {
        $nonempty_pieces = array();
        foreach ($pieces as $item) {
            if ($item) {
                $nonempty_pieces[] = $item;
            }
        }
        return join($glue, $nonempty_pieces);
    }

    /**
     * Возвращает имя свойства к которому осуществляет доступ accessor
     *
     * @param string $getter_or_setter_name
     * @return string
     */
    public static function accessorToName($getter_or_setter_name, $nocheck = true) {
        if ($nocheck) {
            return lcfirst(substr($getter_or_setter_name, 3));
        }
        if (4 > strlen($getter_or_setter_name)) {
            $msg = 'invalid accessor name';
            throw new Exception($msg);
        }
        $prefix = strtolower(substr($getter_or_setter_name, 0, 3));
        if ('get' !== $prefix or 'set' !== $prefix) {
            $msg = 'invalid accessor name';
            throw new Exception($msg);
        }
        return lcfirst(substr($getter_or_setter_name, 3));
    }

    /**
     * Возвращает строку параметров
     *
     * @param array $data
     * @return string
     */
    public static function assocToGet(array $data = array(), $prefix = true) {
        $ret = array();
        foreach ($data as $key => $value) {
            $key = Filter::strtn($key);
            if (!$key) {
                continue;
            }
            $value = Filter::str($value, '');
            $ret[] = $key . '=' . $value;
        }
        if (empty($ret)) {
            return '';
        }
        return ($prefix ? '?' : '') . join('&', $ret);
    }

    /**
     * Возвращает фргмент xml из массива рекурсивно
     *
     * @param array $data
     * @return string
     */
    public static function arrayToXmlFragment(array $data = array(), $ignore_numeric = true) {
        $ret = '';
        foreach ($data as $key => $value) {
            $condition = !is_numeric($key) or !$ignore_numeric;
            if ($condition) {
                $ret.= '<' . $key . '>';
            }
            if (is_array($value)) {
                $ret.= self::arrayToXmlFragment($value);
            } else {
                if ($condition) {
                    $ret.= $value;
                }
            }
            if ($condition) {
                $ret.= '</' . $key . '>';
            }
        }
        return $ret;
    }
}





















