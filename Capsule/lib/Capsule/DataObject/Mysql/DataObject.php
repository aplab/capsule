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
     * Filename sql create table if not exists
     *
     * @var string
     */
    const FILENAME_SQL_CREATE_TABLE_DEFAULT = 'table.default.sql';

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

    /**
     * Create config file default
     *
     * @param void
     * @return Path
     * @throws Exception
     */
    public static function _createFileConfigDefault()
    {
        return self::_createFileConfig(self::FILENAME_CONFIG_DEFAULT);
    }

    /**
     * Create config file user
     *
     * @param void
     * @return Path
     * @throws Exception
     */
    public static function _createFileConfigUser()
    {
        return self::_createFileConfig(self::FILENAME_CONFIG_USER);
    }

    /**
     * Create config file sql create table
     *
     * @param void
     * @return Path
     * @throws Exception
     */
    public static function _createFileSqlCreateTable()
    {
        return self::_createFileConfig(self::FILENAME_SQL_CREATE_TABLE);
    }

    /**
     * Create config file sql create table default
     *
     * @param void
     * @return Path
     * @throws Exception
     */
    public static function _createFileSqlCreateTableDefault()
    {
        return self::_createFileConfig(self::FILENAME_SQL_CREATE_TABLE_DEFAULT);
    }

    /**
     * Create config file sql init script
     *
     * @param void
     * @return Path
     * @throws Exception
     */
    public static function _createFileSqlInitScript()
    {
        return self::_createFileConfig(self::FILENAME_SQL_INIT_SCRIPT);
    }

    /**
     * Create all config files
     *
     * @param void
     * @return array
     */
    public static function _createConfigFiles()
    {
        return array(
            self::FILENAME_CONFIG_DEFAULT => self::_createFileConfigDefault(),
            self::FILENAME_CONFIG_USER => self::_createFileConfigUser(),
            self::FILENAME_SQL_CREATE_TABLE => self::_createFileSqlCreateTable(),
            self::FILENAME_SQL_CREATE_TABLE_DEFAULT => self::_createFileSqlCreateTableDefault(),
            self::FILENAME_SQL_INIT_SCRIPT => self::_createFileSqlInitScript()
        );
    }

    /**
     * Returns true if associated table exists, false otherwise
     *
     * @param void
     * @return bool
     */
    public static function _associatedTableExists()
    {
        $db = Db::getInstance();
        $default_schema = $db->config->dbname;
        $table = self::_associatedTable();
        $sql = 'SHOW TABLES FROM `' . $default_schema . '` LIKE ' . $db->qt($table);
        return !!$db->query($sql)->num_rows;
    }

    /**
     * Returns true if associated table is empty, false otherwise
     *
     * @param void
     * @return bool
     */
    public static function _associatedTableEmpty()
    {
        $db = Db::getInstance();
        $default_schema = $db->config->dbname;
        $table = self::_associatedTable();
        $sql = 'SELECT 1 FROM `' . $default_schema . '`.`' . $table . '`';
        return !$db->query($sql)->num_rows;
    }

    /**
     * Returns table and columns metadata for associated table
     *
     * @param void
     * @return array
     * @throws Exception
     */
    public static function _loadInformationSchema()
    {
        $db = Db::getInstance();
        $default_schema = $db->config->dbname;
        $table_name = self::_associatedTable();
        $sql = 'SELECT
                    `TABLE_CATALOG`,
                    `TABLE_SCHEMA`,
                    `TABLE_NAME`,
                    `TABLE_TYPE`,
                    `ENGINE`,
                    `VERSION`,
                    `ROW_FORMAT`,
                    `TABLE_ROWS`,
                    `AVG_ROW_LENGTH`,
                    `DATA_LENGTH`,
                    `MAX_DATA_LENGTH`,
                    `INDEX_LENGTH`,
                    `DATA_FREE`,
                    `AUTO_INCREMENT`,
                    `CREATE_TIME`,
                    `UPDATE_TIME`,
                    `CHECK_TIME`,
                    `TABLE_COLLATION`,
                    `CHECKSUM`,
                    `CREATE_OPTIONS`,
                    `TABLE_COMMENT`
                FROM `information_schema`.`TABLES`
                WHERE `TABLE_SCHEMA` = ' . $db->qt($default_schema) . '
                AND `TABLE_NAME` = ' . $db->qt($table_name);
        $table_metadata = $db->query($sql)->fetch_object();
        if (!$table_metadata) {
            throw new Exception('Unable to load table metadata: ' . $default_schema . '.' . $table_name);
        }

        $sql = 'SELECT
                    `TABLE_CATALOG`,
                    `TABLE_SCHEMA`,
                    `TABLE_NAME`,
                    `COLUMN_NAME`,
                    `ORDINAL_POSITION`,
                    `COLUMN_DEFAULT`,
                    `IS_NULLABLE`,
                    `DATA_TYPE`,
                    `CHARACTER_MAXIMUM_LENGTH`,
                    `CHARACTER_OCTET_LENGTH`,
                    `NUMERIC_PRECISION`,
                    `NUMERIC_SCALE`,
                    `DATETIME_PRECISION`,
                    `CHARACTER_SET_NAME`,
                    `COLLATION_NAME`,
                    `COLUMN_TYPE`,
                    `COLUMN_KEY`,
                    `EXTRA`,
                    `PRIVILEGES`,
                    `COLUMN_COMMENT`
                FROM `information_schema`.`COLUMNS`
                WHERE `TABLE_SCHEMA` = ' . $db->qt($default_schema) . '
                AND `TABLE_NAME` = ' . $db->qt($table_name);
        $columns_metadata = $db->query($sql);
        if (!$columns_metadata->num_rows) {
            throw new Exception('Table metadata not found: ' . $default_schema . '.' . $table_name);
        }

        return array(
            'table' => $table_metadata,
            'columns' => $columns_metadata
        );
    }
}