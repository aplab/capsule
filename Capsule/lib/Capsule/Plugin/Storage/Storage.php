<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 26.07.2014 8:21:37 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Plugin\Storage;

use Capsule\DataStruct\Config;
use Capsule\Core\Fn;
use Capsule\DataStorage\DataStorage;
use Capsule\Common\Path;
use Capsule\DataStruct\Loader;
use Capsule\Capsule;
use PHP\Exceptionizer\Exception;
use Capsule\Common\String;
/**
 * Storage.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Storage
{
    /**
     * Default storage, where getInstance() called without param.
     *
     * @var string
     */
    const DEFAULT_STORAGE = 'default';
    
    /**
     * Driver namespace prefix
     * 
     * @var string
     */
    const DRIVER_NS = 'Driver';
    
    /**
     * Storage instances
     *
     * @var array
     */
    protected static $instances = array();
    
    /**
     * Configuration data
     * 
     * @var Config
     */
    protected static $config;
    
    /**
     * Concrete object properties
     *
     * @var array
     */
    private $data = array();
    
    /**
     * Returns configuration data
     *
     * @param void
     * @return Config
     */
    public static function config() {
        $name = __FUNCTION__;
        if (!self::$$name) {
            $class = get_called_class();
            $storage = DataStorage::getInstance();
            if ($storage->exists($class)) {
                self::$$name = $storage->get($class); $storage->get($class);
            } else {
                $path = new Path(Capsule::getInstance()->{Capsule::DIR_CFG}, $class . '.json');
                if (!file_exists($path)) {
                    $dir = dirname($path);
                    if (!is_dir($dir)) {
                        $is_dir = mkdir($dir, 0700, true);
                        if (!$is_dir || !is_dir($dir)) {
                            $msg = 'Unable to create config directory: ' . $dir;
                            throw new \Exception($msg);
                        }
                    }
                    file_put_contents($path, '{}');
                }
                $loader = new Loader();
                $prefilter = function($json) {
                	return strtr($json, array(
                		'%{CAPSULE_SYSTEM_ROOT}' => Capsule::getInstance()->systemRoot,
                	    '%{CAPSULE_DOCUMENT_ROOT}' => Capsule::getInstance()->documentRoot,
                	));
                };
                $data = $loader->loadJson($path, $prefilter);
                $$name = new Config($data);
                $storage->set($class, $$name);
                self::$$name = $$name;
            }
        }
        return self::$$name;
    }
    
    /**
     * Returns required or default instance
     * 
     * @param string $instance_name
     * @return self
     */
    public static function getInstance($instance_name = null) {
        if (is_null($instance_name)) {
            $instance_name = self::DEFAULT_STORAGE;
        }
        if (!isset(self::$instances[$instance_name])) {
            self::$instances[$instance_name] = new self(self::config()->$instance_name);
        }
        return self::$instances[$instance_name];
    }
    
    /**
     * Возвращает имя хранилища
     * 
     * @param self $storage
     * @throws Exception
     * @return string
     */
    public static function getInstanceName(self $storage) {
        foreach (self::$instances as $k => $v) {
            if ($storage === $v) return $k;
        }
        $msg = 'Unknown storage';
        throw new Exception($msg);
    }
    
    /**
     * @param Config $config
     * @return self
     */
    private function __construct(Config $config) {
        $ns = Fn::get_namespace($this);
        $driver_classname = Fn::cc(self::DRIVER_NS . '/' . $config->driver, $this);
        $this->data['driver'] = new $driver_classname($config);
    }
    
    /**
     * Returns property value
     *
     * @param string
     * @return mixed
     * @throws Exception
     */
    public function __get($name) {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        $msg = 'Unknown property: '.get_class($this).'::$'.$name;
        throw new \Exception($msg);
    }
    
    /**
     * Handler property value change.
     *
     * @param string $name
     * @param mixed $value
     * @return void
     * @throws Exception
     */
    public function __set($name, $value) {
        if (array_key_exists($name, $this->data)) {
            $msg = get_class($this) . '::$' . $name . ' is read only';
        } else {
            $msg = 'Unknown property: ' . get_class($this) . '::$' . $name;
        }
        throw new \Exception($msg);
    }
    
    /**
     * Disable cloning
     *
     * @param void
     * @return void
     */
    final public function __clone() {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }
    
    /**
     * Add file 
     * 
     * @param string $source_absolute_path
     * @param String $extension
     * @return string
     */
    public function addFile($source_absolute_path, $extension = null) {
        return $this->driver->addFile($source_absolute_path, $extension);
    }
    
    public function delFile($filename) {
        return $this->driver->delFile($filename);
    }
}