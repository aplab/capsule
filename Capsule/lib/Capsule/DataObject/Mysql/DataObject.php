<?php
/**
 * Created by Alexander Polyanin polyanin@gmail.com
 * User: polyanin
 * Date: 03.11.2015
 * Time: 1:10
 */
namespace Capsule\DataObject\Mysql;

use Capsule\Capsule;
use Capsule\Common\Path;
use Capsule\DataObject\Inflector;
use Capsule\Db\Db;
use Capsule\Exception;
use Capsule\Tools\Tools;

abstract class DataObject
{
    /**
     * Filename config default (automatically generated)
     *
     * @var string
     */
    const FILENAME_CONFIG_DEFAULT = 'config.default.json';

    /**
     * Filename user config
     *
     * @var string
     */
    const FILENAME_CONFIG_USER = 'config.json';

    /**
     * Filename sql create table if not exists
     *
     * @var string
     */
    const FILENAME_SQL_CREATE_TABLE = 'table.sql';

    /**
     * Filename sql init script for primary initialization
     *
     * @var string
     */
    const FILENAME_SQL_INIT_SCRIPT = 'init.sql';

    /**
     * Object internal data (properties)
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
     * Возвращает ReflectionClass для класса.
     *
     * @param void
     * @return \ReflectionClass
     */
    final protected static function _reflectionClass()
    {
        $class = get_called_class();
        $f = __FUNCTION__;
        if (!isset(self::$common[$class][$f])) {
            self::$common[$class][$f] = new \ReflectionClass($class);
        }
        return self::$common[$class][$f];
    }

    /**
     * Returns path to module config
     *
     * @param void
     * @return string
     */
    public static function _configLocation()
    {
        $class = get_called_class();
        $f = __FUNCTION__;
        if (!isset(self::$common[$class][$f])) {
            self::$common[$class][$f] =
                new Path(Capsule::getInstance()->{Capsule::DIR_CFG}, $class);
        }
        return self::$common[$class][$f];
    }

    /**
     * Returns automatically generated associated table name by class name
     *
     * @param void
     * @return string
     */
    public static function _associatedTable()
    {
        $class = get_called_class();
        $f = __FUNCTION__;
        if (!isset(self::$common[$class][$f])) {
            self::$common[$class][$f] = Inflector::getInstance()->getAssociatedTable($class);
        }
        return self::$common[$class][$f];
    }

    /**
     * Создает config file
     *
     * @param string $filename
     * @return Path
     * @throws Exception
     */
    protected static function _createFileConfig($filename)
    {
        $dir = self::_configLocation();
        $path = new Path($dir, $filename);
        if (file_exists($path)) {
            return $path;
        }
        if (!is_dir($dir)) {
            $is_dir = mkdir($dir, 0700, true);
            if (!$is_dir || !is_dir($dir)) {
                $msg = 'Unable to create config location directory: ' . $dir;
                throw new Exception($msg);
            }
        }
        file_put_contents($path, '');
        if (file_exists($path)) {
            return $path;
        }
        throw new Exception('Unable to create configuration file: ' . $path);
    }

    public static function _createFileConfigDefault()
    {
        return self::_createFileConfig(self::FILENAME_CONFIG_DEFAULT);
    }

    public static function _createFileConfigUser()
    {
        return self::_createFileConfig(self::FILENAME_CONFIG_USER);
    }

    public static function _createFileSqlCreateTable()
    {
        return self::_createFileConfig(self::FILENAME_SQL_CREATE_TABLE);
    }

    public static function _createFileSqlInitScript()
    {
        return self::_createFileConfig(self::FILENAME_SQL_INIT_SCRIPT);
    }

    public static function _createConfigFiles()
    {
        return array(
            self::FILENAME_CONFIG_DEFAULT => self::_createFileConfigDefault(),
            self::FILENAME_CONFIG_USER => self::_createFileConfigUser(),
            self::FILENAME_SQL_CREATE_TABLE => self::_createFileSqlCreateTable(),
            self::FILENAME_SQL_INIT_SCRIPT => self::_createFileSqlInitScript()
        );
    }

    public static function _loadInformationSchema()
    {
        $db = Db::getInstance();
        $default_schema = $db->config->dbname;
        $current_schema = $db->selectSchema();
        if ($de)
            Tools::dump($default_schema);
    }
}