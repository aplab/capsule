<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 20.05.2013 23:21:35 YEKT 2013                                             |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Core;

/**
 * Singleton.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
abstract class Singleton implements \Serializable
{
    private static $instances = array();
    
    protected function __construct() {}
    
    public function __clone() {
        $msg = 'Clone is not allowed.';
        throw new Exception($msg);
    }
    
    /**
     * @param string $classname
     * @return $this
     */
    protected static function getInstanceOf($classname, $parameters) {
        if(!isset(self::$instances[$classname])) {
            self::$instances[$classname] = new $classname($parameters);
        }
        return self::$instances[$classname];
    }
    
    public static function instanceExists($classname = null) {
        return array_key_exists($classname ?: get_called_class(), self::$instances);
    }
    
    /**
     * @param void
     * @return $this
     */
    public static function getInstance() {
        return self::getInstanceOf(get_called_class(), func_get_args());
    }
    
    /**
     * @param void
     * @return void
     * @throws \BadFunctionCallException
     */
    public function serialize() {
        throw new \BadFunctionCallException('You cannot serialize this object.');
    }
    
    /**
     * @param void
     * @return void
     * @throws \BadFunctionCallException
     */
    public function unserialize($serialized) {
        throw new \BadFunctionCallException('You cannot unserialize this object.');
    }
}