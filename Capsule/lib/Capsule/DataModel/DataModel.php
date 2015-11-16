<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 28.12.2013 20:10:42 YEKT 2013                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\DataModel;

use Capsule\Tools\Tools;
use ReflectionClass;
use Capsule\Common\Path;
use Capsule\DataModel\Exception;
use Capsule\Json\Error;
use Capsule\Capsule;
use Capsule\DataModel\Config\Storage;
use Capsule\DataModel\Config\Config;
use Capsule\Db\Db;
use Capsule\Core\Fn;
use PHP\Exceptionizer\Exceptionizer;

/**
 * DataModel.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
abstract class DataModel
{
    /**
     * Config filename
     *
     * @var string
     */
    const CONFIG_FILENAME = 'config.json';

    /**
     * config
     *
     * @var string
     */
    const CONFIG = 'this';

    /**
     * Свойства объекта
     *
     * @var array
     */
    protected $data = array();

    /**
     * Общие свойства класса
     *
     * @var array
     */
    protected static $common = array();

    /**
     * Хранилище созданных экземпляров классов
     * Ключ - имя класса, значение - массив объектов, упорядоченный по
     * первичному ключу, либо иначе, на усмотрение модуля
     *
     * @var array
     */
    protected static $cache = array();

    /**
     * constructor
     *
     * @param void
     * @return self
     * @throws Exception
     */
    protected function __construct()
    {
    }

    /**
     * Возвращает значение свойства. Если свойство не определено или
     * отсутствует, то генерируется исключение.
     *
     * @param  string
     * @throws Exception
     * @return mixed
     */
    public function __get($name)
    {
        $getter = self::_getter($name);
        if ($getter) {
            return $this->$getter($name);
        }
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        $properties = $this->config()->get('properties', new \stdClass());
        if (isset($properties->$name)) {
            $msg = 'Undefined property: ';
        } else {
            $msg = 'Unknown property: ';
        }
        $msg .= get_class($this) . '::$' . $name;
        throw new Exception($msg);
    }

    /**
     * Возвращает значение свойства или значение по умолчанию, если свойство
     * не определено.
     *
     * @param  string
     * @return mixed
     */
    public function get($name, $default = null)
    {
        $getter = self::_getter($name);
        if ($getter) {
            return $this->$getter($name);
        }
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        return $default;
    }

    /**
     * Обрабатывает изменение значения свойства.
     *
     * @param  string $name
     * @param  mixed $value
     * @throws Exception
     * @return self
     */
    public function __set($name, $value)
    {
        $properties = $this->config()->get('properties', new \stdClass);
        if (isset($properties->$name)) {
            $property = $this->config()->properties->$name;
            $validator = $property->get('validator', null);
            if ($validator) {
                if ($validator->isValid($value)) {
                    $value = $validator->getClean();
                } else {
                    $msg = 'Invalid value: ' . get_class($this) . '::$' . $name;
                    throw new Exception($msg);
                }
            }
        }
        $setter = self::_setter($name);
        if (method_exists($this, $setter)) {
            return $this->$setter($value, $name);
        }
        $this->data[$name] = $value;
        return $this;
    }

    /**
     * Возвращает имя класса без namespace
     *
     * @param string|null $class
     * @return string
     */
    final protected static function _classname($class = null)
    {
        $class = $class ?: get_called_class();
        if (!isset(self::$common[$class][__FUNCTION__])) {
            $data = explode('\\', $class);
            self::$common[$class][__FUNCTION__] = array_pop($data);
        }
        return self::$common[$class][__FUNCTION__];
    }

    /**
     * Возвращает конфиг модуля
     *
     * @param void
     * @return Config
     */
    final public static function config()
    {
        $c = get_called_class();
        $f = __FUNCTION__;
        if (!isset(self::$common[$c][$f])) {
            self::$common[$c][$f] = self::_loadConfig($c);
        }
        return self::$common[$c][$f];
    }

    /**
     * Возвращает имя класса
     *
     * @param void
     * @return Config
     */
    final public static function _class()
    {
        return get_called_class();
    }

    /**
     * Загружает файл конфигурации
     *
     * @param string|null $class
     * @return array
     */
    protected static function _loadConfig($class = null)
    {
        $class = $class ?: get_called_class();
        if (!Storage::getInstance()->exists($class)) {
            $data = self::_configData();
            /**
             * Post-processing values like __CLASS__, "config.some_value.another_value"
             */
            array_walk_recursive($data, function (& $v) use ($data, $class) {
                if (false !== strpos($v, '__CLASS__')) {
                    $v = str_replace('__CLASS__', $class, $v);
                }
                if (!(strpos($v, '.'))) {
                    return;
                }
                $pcs = explode('.', $v);
                $pcs = array_filter($pcs, 'trim');
                if (sizeof($pcs) < 2) {
                    return;
                }
                if (self::CONFIG !== array_shift($pcs)) {
                    return;
                }
                $tmp = $data;
                foreach ($pcs as $i) {
                    if (!array_key_exists($i, $tmp)) {
                        return;
                    }
                    $tmp = $tmp[$i];
                }
                $v = $tmp;
            });
            Storage::getInstance()->set($class, new Config($data));
        }
        return Storage::getInstance()->get($class);
    }

    /**
     * Возвращает массив данных для создания объекта конфигурации
     *
     * @param string $class
     * @return array
     */
    public static function _configData($class = null)
    {
        $class = $class ?: get_called_class();
        if (!isset(self::$common[$class][__FUNCTION__])) {
            self::$common[$class][__FUNCTION__] = self::_buildConfigData($class);
        }
        return self::$common[$class][__FUNCTION__];
    }

    /**
     * Собирает и возвращает данные конфига с учетом наследования.
     *
     * @param string|null $class
     * @return void
     */
    protected static function _buildConfigData($class = null)
    {
        $class = $class ?: get_called_class();
        $data = self::_configDataFragment($class);
        $parent_data = array();
        $parent_class = get_parent_class($class);
        if ($parent_class) {
            $parent_data = self::_configData($parent_class);
        }
        // Получаем из фрагмента конфига только переопределенные параметры
        $diff = Fn::array_diff_assoc_recursive($data, $parent_data);
        if (Fn::array_diff_assoc_recursive($data, $diff)) {
            self::_saveConfigfragment($diff);
        }
        if (isset($diff['table'])) {
            // Неочевидное поведение:
            // Если в конфиге есть секция table, значит у модуля должна быть
            // своя таблица. Если такой секции нет, то модуль работает с
            // таблицей модуля-предка, если такой есть; Или не может работать с
            // таблицей вообще.
            if (!isset($diff['table']['name'])) {
                // Если имя таблицы не задано вручную, то оно генерируется
                // автоматически на основе полного имени класса.
                $diff['table']['name'] = Inflector::getInstance()->getAssociatedTable($class);
            }
        }
        return array_replace_recursive($parent_data, $diff);
    }

    /**
     * Make full config file for developer
     *
     * @return bool
     * @throws \Capsule\DataModel\Exception
     * @param void
     */
    public static function makeConfig()
    {
        $path = self::_configLocation();
        $data = self::_buildConfigData();
        $opt = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
        $json = json_encode($data, $opt);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new Exception(Error::getLastError());
        }
        self::_createConfigFile();
        if (false === file_put_contents($path, $json, LOCK_EX)) {
            $msg = 'Unable to make configuration file: ' . $path;
            throw new Exception($msg);
        }
        return true;
    }

    /**
     * Возвращает только разницу в конфигурации по сравнению с прямым предком.
     * (Только то, что было переопределено)
     *
     * @param string|null $class
     * @return array
     */
    public static function _configDataFragmentDiff($class = null)
    {
        $class = $class ?: get_called_class();
        $data = self::_configDataFragment($class);
        $parent_data = array();
        $parent_class = get_parent_class($class);
        if ($parent_class) {
            $parent_data = self::_configData($parent_class);
        }
        // Получаем из фрагмента конфига только переопределенные параметры
        return Fn::array_diff_assoc_recursive($data, $parent_data);
    }

    /**
     * Возвращает только разницу в конфигурации по сравнению с прямым предком.
     * (Только то, что было переопределено)
     * В формате JSON
     *
     * @param string|null $class
     * @return string
     */
    public static function _configDataFragmentDiffJson($class = null)
    {
        $class = $class ?: get_called_class();
        $opt = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
        return json_encode(self::_configDataFragmentDiff($class), $opt);
    }

    /**
     * Возвращает данные конфига с учетом наследования в формате json.
     *
     * @param string|null $class
     * @throws Exception
     * @return string
     */
    public static function _configDataJson($class = null)
    {
        $class = $class ?: get_called_class();
        if (!isset(self::$common[$class][__FUNCTION__])) {
            $opt = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
            self::$common[$class][__FUNCTION__] = json_encode(self::_configData($class), $opt);
            if (JSON_ERROR_NONE !== json_last_error()) {
                throw new Exception(Error::getLastError());
            }
        }
        return self::$common[$class][__FUNCTION__];
    }

    /**
     * Сохраняет фрагмент конфигурационного файла
     *
     * @param array $data
     * @throws Exception
     */
    protected static function _saveConfigFragment(array $data)
    {
        $opt = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
        $json = json_encode($data, $opt);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new Exception(Error::getLastError());
        }
        $path = self::_configLocation();
        if (false === file_put_contents($path, $json, LOCK_EX)) {
            $msg = 'Unable to write diff config fragment';
            throw new Exception($msg);
        }
    }

    /**
     * Загружает конфигурационный файл модуля (фрагмент).
     * Возвращает прочтенные данные или пустой массив, если файл отсутствует.
     * WARNING! Файл никуда не кешируется и читается заново при каждом вызове.
     * Используйте _configDataFragment вместо _loadConfigDataFragment везде, где
     * это возможно.
     *
     * @param string|null $class
     * @return array
     * @throws \Capsule\DataModel\Exception
     */
    protected static function _loadConfigDataFragment($class = null)
    {
        $class = $class ?: get_called_class();
        $path = self::_configLocation($class);
        if (!file_exists($path)) {
            self::_createConfigFile();
            return array();
        }
        $content = file_get_contents($path);
        if (false === $content) {
            $msg = 'Unable to read configuration file';
            throw new Exception($msg);
        }
        $json = trim($content);
        if (!strlen($json)) {
            return array();
        }
        $data = json_decode($json, true, 512, JSON_BIGINT_AS_STRING);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new Exception(Error::getLastError() . ' ' . $path);
        }
        if (!is_array($data)) {
            return array();
        }
        return $data;
    }

    /**
     * Возвращает фрагмент конфигурационных данных, переопределенных в текущей
     * модели.
     *
     * @param string|null $class
     * @return array
     */
    public static function _configDataFragment($class = null)
    {
        $class = $class ?: get_called_class();
        if (!isset(self::$common[$class][__FUNCTION__])) {
            self::$common[$class][__FUNCTION__] = self::_loadConfigDataFragment($class);
        }
        return self::$common[$class][__FUNCTION__];
    }

    /**
     * Returns path to module config
     *
     * @param string|null $class
     * @return string
     */
    public static function _configLocation($class = null)
    {
        $c = $class ?: get_called_class();
        $f = __FUNCTION__;
        if (!isset(self::$common[$c][$f])) {
            self::$common[$c][$f] = new Path(Capsule::getInstance()->{Capsule::DIR_CFG}, $c, self::CONFIG_FILENAME);
        }
        return self::$common[$c][$f];
    }

    /**
     * Physically create configuration file if not exists
     *
     * @param void
     * @return boolean
     * @throws \Capsule\DataModel\Exception
     */
    public static function _createConfigFile()
    {
        $path = self::_configLocation();
        if (file_exists($path)) {
            return true;
        }
        $dir = dirname($path);
        if (!is_dir($dir)) {
            $is_dir = mkdir($dir, 0700, true);
            if (!$is_dir || !is_dir($dir)) {
                $msg = 'Unable to create config directory: ' . $dir;
                throw new Exception($msg);
            }
        }
        $opt = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
        $json = json_encode(array(), $opt);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new Exception(Error::getLastError());
        }
        if (false === file_put_contents($path, $json, LOCK_EX)) {
            $msg = 'Unable to create configuration file: ' . $path;
            throw new Exception($msg);
        }
        return true;
    }

    /**
     * isset() overloading
     *
     * @param  string $name
     * @return boolean
     */
    public function __isset($name)
    {
        $method = 'isset' . ucfirst($name);
        if (in_array($method, self::_listMethods())) {
            return $this->$method($name);
        }
        return array_key_exists($name, $this->data);
    }

    /**
     * unset() overloading
     *
     * @param  string $name
     * @return void
     * @throws Exception
     */
    public function __unset($name)
    {
        unset($this->data[$name]);
    }

    /**
     * Возвращает ReflectionClass для класса.
     *
     * @param string|null $class
     * @return ReflectionClass
     */
    final protected static function _reflectionClass($class = null)
    {
        $c = $class ?: get_called_class();
        $f = __FUNCTION__;
        if (!isset(self::$common[$c][$f])) {
            self::$common[$c][$f] = new ReflectionClass($c);
        }
        return self::$common[$c][$f];
    }

    /**
     * Возвращает root directory для класса.
     *
     * @param string|null $class
     * @return string
     */
    final protected static function _rootDir($class = null)
    {
        $c = $class ?: get_called_class();
        $f = __FUNCTION__;
        if (!isset(self::$common[$c][$f])) {
            self::$common[$c][$f] = str_replace('\\', '/', dirname(static::_reflectionClass($c)->getFileName()));
        }
        return self::$common[$c][$f];
    }

    /**
     * Возвращает список методов класса с учетом регистра.
     *
     * @param string $class
     * @return array
     */
    protected static function _listMethods($class = null)
    {
        $c = $class ?: get_called_class();
        $f = __FUNCTION__;
        if (!isset(self::$common[$c][$f])) {
            self::$common[$c][$f] = get_class_methods($c);
        }
        return self::$common[$c][$f];
    }

    /**
     * Возвращает getter с учетом регистра.
     *
     * @param string $name
     * @return string|false
     */
    protected static function _getter($name)
    {
        $class = get_called_class();
        if (!isset(self::$common[$class][__FUNCTION__][$name])) {
            $getter = 'get' . ucfirst($name);
            self::$common[$class][__FUNCTION__][$name] =
                in_array($getter, self::_listMethods()) ? $getter : false;
        }
        return self::$common[$class][__FUNCTION__][$name];
    }

    /**
     * Возвращает setter с учетом регистра.
     *
     * @param string $name
     * @return string|false
     */
    protected static function _setter($name)
    {
        $class = get_called_class();
        if (!isset(self::$common[$class][__FUNCTION__][$name])) {
            $setter = 'set' . ucfirst($name);
            self::$common[$class][__FUNCTION__][$name] =
                in_array($setter, self::_listMethods()) ? $setter : false;
        }
        return self::$common[$class][__FUNCTION__][$name];
    }

    /**
     * Возвращает иерархию родительских классов.
     *
     * @param string $class
     * @return array
     */
    protected static function _supertypeHierarchy($class = null)
    {
        $class = $class ?: get_called_class();
        if (!isset(self::$common[$class][__FUNCTION__])) {
            $tmp = array();
            $tmp[] = $class;
            $parent = get_parent_class($class);
            while ($parent) {
                $tmp[] = $parent;
                $parent = get_parent_class($parent);
            }
            $list = array_reverse($tmp);
            $size = sizeof($list);
            for ($i = 0; $i < $size; $i++) {
                $tmp = $list;
                $next_class = array_pop($list);
                self::$common[$next_class][__FUNCTION__] = $tmp;
            }
        }
        return self::$common[$class][__FUNCTION__];
    }

    /**
     * Возвращает все объекты из связанной таблицы
     *
     * @param void
     * @return self
     */
    public static function all()
    {
        $sql = 'SELECT * FROM `' . self::config()->table->name . '`';
        return static::populate(Db::getInstance()->query($sql));
    }

    /**
     * Возвращает количество объектов из связанной таблицы
     *
     * @param void
     * @return int
     */
    public static function number()
    {
        $sql = 'SELECT COUNT(*) FROM `' . self::config()->table->name . '`';
        return Db::getInstance()->query($sql)->fetch_one();
    }

    /**
     * Returns pages number
     *
     * @param int|number $items_per_page
     * @return array
     */
    public static function pages($items_per_page = 10)
    {
        $c = self::number();
        if (!$c) {
            return array();
        }
        return range(1, ceil($c / $items_per_page));
    }

    /**
     * @param void
     * @return string
     */
    public static function get_called_class()
    {
        return get_called_class();
    }

    /**
     * common key generator
     *
     * @param string $__FUNCTION__
     * @return string
     */
    protected static function ck($__FUNCTION__ = null)
    {
        $c = get_called_class();
        $e = new Exceptionizer;
        if (is_null($__FUNCTION__)) {
            $f = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS ^ DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
            $f = $f[1]['function'];
        } else {
            $f = $__FUNCTION__;
        }
        return Fn::concat_ws_ne('::', $c, $f);
    }
}