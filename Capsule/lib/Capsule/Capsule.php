<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
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
use Capsule\Tools\Tools;
use PHP\Exceptionizer\Exceptionizer;
use \Capsule\Config\Path as ConfigPath;
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
 * @property string $osType
 * @property Config $config
 * @property string $lib
 * @property string $cfg
 * @property string $bin
 * @property string $ext
 * @property string $tmp
 * @property string $var
 */
final class Capsule implements \Serializable
{
    /**
     * Тип операционной системы
     * Windows - Microsoft Windows
     * UNIX - UNIX-like operating system
     * 
     * @var string
     */
    const OS_TYPE_WINDOWS = 'Windows', OS_TYPE_UNIX = 'UNIX';
    
    const DIR_CFG = 'cfg'; // old etc
    const DIR_LIB = 'lib';
    const DIR_BIN = 'bin';
    const DIR_EXT = 'ext'; // olg opt
    const DIR_TMP = 'tmp';
    const DIR_VAR = 'var';
    const DIR_TPL = 'tpl';
    
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
     * Some static data
     * 
     * @var array
     */
    private static $staticData = null;

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
        self::$staticData = array();
        return self::$instance;
    }

    /**
     * @param string $document_root
     * @throws Exception
     */
    private function __construct($document_root) {
        $this->data['startTime'] = $this->microtime;
        $this->data['alreadyRunning'] = false;
        $this->data[self::DIR_LIB] = $this->_normalizePath(dirname(__DIR__));
        $this->data['systemRoot'] = dirname($this->lib);
        $this->data[self::DIR_CFG] = $this->systemRoot . '/' . self::DIR_CFG;
        $this->data[self::DIR_EXT] = $this->systemRoot . '/' . self::DIR_EXT;
        $this->data[self::DIR_TMP] = $this->systemRoot . '/' . self::DIR_TMP;
        $this->data[self::DIR_VAR] = $this->systemRoot . '/' . self::DIR_VAR;
        $this->data[self::DIR_BIN] = $this->systemRoot . '/' . self::DIR_BIN;
        $this->data[self::DIR_TPL] = $this->systemRoot . '/' . self::DIR_TPL;
        include 'Exception.php';
        include $this->{self::DIR_LIB} . '/Capsule/Core/Exception.php';
        if (PHP_MAJOR_VERSION < 5 or PHP_MINOR_VERSION < 4 or PHP_RELEASE_VERSION < 3) {
            $msg = 'Supported php version 5.4.3+';
            throw new Core\Exception($msg);
        }
        include $this->{self::DIR_LIB} . '/Capsule/Core/Singleton.php';
        include $this->{self::DIR_LIB} . '/Capsule/Core/Autoload.php';
        include $this->{self::DIR_LIB} . '/Capsule/Core/global_functions.php';
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
     * Returns operating system type
     * 
     * @param void
     * @return string
     */
    protected function getOsType() {
        $type = String::strtolower(php_uname('s'));
        if (preg_match('/' . String::strtolower(self::OS_TYPE_WINDOWS) . '/', $type) or getenv('COMSPEC')) {
            return self::OS_TYPE_WINDOWS;
        }
        return self::OS_TYPE_UNIX;
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
            $storage = DataStorage::getInstance();
            if ($storage->exists($class)) {
                $this->data[$name] = $storage->get($class);
            } else {
                $path = new ConfigPath($this);
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
    private function _normalizePath($path) {
        return rtrim(preg_replace('|/{2,}|', '/', str_replace('\\', '/', $path)), '/');
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
    
    /**
     * Prevent cloning
     * 
     * @throws \BadFunctionCallException
     * @param void
     * @return void
     */
    public function __clone() {
        throw new \BadFunctionCallException('Clone is not allowed.');
    }
    
    /**
     * Returns host
     * 
     * @param void
     * @return string
     * @throws Exception
     */
    public static function host() {
        if (!is_array(self::$staticData)) {
            $msg = 'Object Capsule not found';
            throw new Exception($msg);
        }
        $k = __FUNCTION__;
        if (!array_key_exists($k, self::$staticData)) {
            self::$staticData[$k] = self::$instance->config->host;
        }
        return self::$staticData[$k];
    }
    
    /**
     * Returns base url without trailing slash
     * 
     * @param void
     * @return string
     */
    public static function baseUrl() {
        return 'http://' . self::host();
    }
}