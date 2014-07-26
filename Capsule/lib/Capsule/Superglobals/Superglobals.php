<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.5.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 02.02.2014 1:03:29 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Superglobals;

use Capsule\Core\Singleton;
/**
 * Superglobals.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
abstract class Superglobals extends Singleton
{
    protected $data = array();

    protected function __construct() {}

    public function __get($name) {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        return null;
    }

    public function get($name, $default = null) {
        return array_key_exists($name, $this->data) ? $this->data[$name] : $default;
    }
    
    /**
     * Возвращает значение переменной если она существует и является is_scalar,
     * в противном случае возвращает $default
     *
     * @param unknown $name
     * @param string $default
     * @return string
     */
    public function gets($name, $default = null) {
        return array_key_exists($name, $this->data) ? (is_scalar($this->data[$name]) ? $this->data[$name] : $default) : $default;
    }

    public function __set($name, $value) {
        $this->data[$name] = $value;
    }
    
    public function __isset($name) {
        return array_key_exists($name, $this->data);
    }
}