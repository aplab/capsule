<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
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
            $namespace = Fn::get_namespace($class);
            $storage = DataStorage::getInstance();
            if ($storage->exists($class)) {
                self::$$name = $storage->get($class); $storage->get($class);
            } else {
                $path = new Path(Capsule::getInstance()->etc, $namespace, 'config.json');
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
                \Capsule\Tools\Tools::dump($data);
                $$name = new Config($data);
                $storage->set($class, $$name);
                self::$$name = $$name;
            }
        }
        return self::$$name;
    }
}