<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 20.05.2013 23:18:21 YEKT 2013                                             |
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

use Capsule\Core\Autoload;
use Capsule\Common\String;
use Capsule\Controller\FrontController;
use Capsule\Core\Fn;
use Capsule\DataStorage\DataStorage;
use Capsule\Common\Path;
use Capsule\DataStruct\Loader;
use Capsule\DataStruct\Config;
use PHP\Exceptionizer\Exceptionizer;
/**
 * Capsule.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 * @property string $worktime
 * @property string $memory
 * @property string $microtime
 * @property string $systemRoot
 * @property string $documentRoot
 * @property Config $config
 */
final class Capsule implements \Serializable
{
    /**
     * Developer mode flag
     *
     * @var boolean
     */
    public static $dev = false;

    /**
     * Debug mode flag
     *
     * @var boolean
     */
    public static $debug = false;

    /**
     * Silent mode flag
     *
     * @var boolean
     */
    public static $silent = true;

    /**
     * @var Capsule
     */
    private static $instance;

    /**
     * Internal data
     *
     * @var array
     */
    protected $data = array();

    /**
     * @param string|null $document_root
     * @return self
     */
    public static function getInstance($document_root = null) {
        $class = __CLASS__;
        if (self::$instance instanceof $class) {
            return self::$instance;
        }
        self::$instance = new $class($document_root);
        self::$instance->init();
        return self::$instance;
    }

    /**
     * @param string $document_root
     * @throws Exception
     */
    private function __construct($document_root) {
        $this->data['startTime'] = $this->microtime;
        $this->data['alreadyRunning'] = false;
        $this->data['lib'] = $this->normalizePath(dirname(__DIR__));
        $this->data['systemRoot'] = dirname($this->lib);
        $this->data['etc'] = $this->systemRoot . '/etc';
        $this->data['opt'] = $this->systemRoot . '/opt';
        $this->data['tmp'] = $this->systemRoot . '/tmp';
        $this->data['var'] = $this->systemRoot . '/var';
        include 'Exception.php';
        include $this->lib . '/Capsule/Core/Exception.php';
        if (PHP_MAJOR_VERSION < 5 or PHP_MINOR_VERSION < 4) {
            $msg = 'Supported php version 5.4+';
            throw new Core\Exception($msg);
        }
        include $this->lib . '/Capsule/Core/Singleton.php';
        include $this->lib . '/Capsule/Core/Autoload.php';
        include $this->lib . '/Capsule/Core/global_functions.php';
        if (is_null($document_root)) {
            if (isset($_SERVER['DOCUMENT_ROOT'])) {
                $this->data['documentRoot'] = $_SERVER['DOCUMENT_ROOT'];
            } else {
                $msg = 'Cannot be determined DOCUMENT_ROOT';
                throw new Core\Exception($msg);
            }
        } else {
            $this->data['documentRoot'] = $document_root;
        }
    }

    /**
     * Дополнительные инициализации модулей
     *
     * @param void
     * @return void
     */
    private function init() {
        Autoload::getInstance();
        String::initialize();
        date_default_timezone_set($this->config->timezoneId);
    }

    /**
     * Запуск приложения.
     *
     * @param void
     * @return void
     * @throws \Exception
     */
    public function run() {
        if ($this->alreadyRunning) {
            $message = 'already running';
            throw new Core\Exception($message);
        }
        $this->data['alreadyRunning'] = true;
        if (!@is_dir($this->documentRoot)) {
            $message = 'invalid document root';
            throw new Core\Exception($message);
        }
        #if (function_exists('xdebug_disable')) xdebug_disable();
        $e = new Exceptionizer(E_ALL & ~E_USER_ERROR);
        FrontController::getInstance()->handle();
    }

    /**
     * Возвращает текущее в виде строки
     *
     * @param void
     * @return string
     */
    private function getMicrotime() {
        list($usec, $sec) = explode(' ', microtime());
        return bcadd((string)$usec, (string)$sec, 6);
    }

    /**
     * Возвращает время работы
     *
     * @param void
     * @return void
     */
    private function getWorktime() {
        return bcsub($this->getMicrotime(), $this->data['startTime'], 6);
    }
    
    /**
     * Возвращает объем занятой памяти
     *
     * @param void
     * @return void
     */
    private function getMemory() {
        return memory_get_peak_usage();
    }

    /**
     *
     * @param unknown $name
     */
    protected function getConfig($name) {
        if (!array_key_exists($name, $this->data)) {
            $class = get_class($this);
            $namespace = Fn::get_namespace($this);
            $storage = DataStorage::getInstance();
            if ($storage->exists($class)) {
                $this->data[$name] = $storage->get($class);
            } else {
                $path = new Path(Capsule::getInstance()->etc, $namespace, 'config.json');
                $loader = new Loader();
                $data = $loader->loadJson($path);
                $$name = new Config($data);
                $storage->set($class, $$name);
                $this->data[$name] = $$name;
            }
        }
        return $this->data[$name];
    }

    /**
     * Возвращает значение свойства.
     *
     * @param $name
     * @throws Exception
     * @return mixed
     */
    public function __get($name) {
        $getter = 'get' . ucfirst($name);
        if (in_array($getter, get_class_methods($this))) {
            return $this->$getter($name);
        }
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        $msg = 'Unknown property: ' . get_class($this) . '::$' . $name;
        throw new Core\Exception($msg);
    }

    /**
     * Normalize path
     *
     * @param string $path
     * @return string
     */
    private function normalizePath($path) {
        return rtrim(preg_replace('|/{2,}|', '/', str_replace('\\', '/', trim($path))), '/');
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