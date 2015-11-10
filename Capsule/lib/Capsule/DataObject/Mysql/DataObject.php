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
use Capsule\Common\String;
use Capsule\DataObject\Inflector;
use Capsule\DataObject\Mysql\Config\Config;
use Capsule\DataObject\Mysql\Config\Storage;
use Capsule\Db\Db;
use Capsule\Exception;
use Capsule\Tools\Tools;

abstract class DataObject
{
    /**
     * config
     * Внутри конфига можно ссылаться на его-же данные. Ссылка на ссылку не поддерживается.
     *
     * @var string
     */
    const CONFIG = 'this';

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
     * Table name placeholder
     *
     * @var string
     */
    const TABLE_NAME_PLACEHOLDER = '%TABLE_NAME%';

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
        $c = get_called_class();
        $f = __FUNCTION__;
        if (!isset(self::$common[$c][$f])) {
            self::$common[$c][$f] = new \ReflectionClass($c);
        }
        return self::$common[$c][$f];
    }

    /**
     * Returns path to module config
     *
     * @param void
     * @return string
     */
    public static function _configLocation()
    {
        $c = get_called_class();
        $f = __FUNCTION__;
        if (!isset(self::$common[$c][$f])) {
            self::$common[$c][$f] = new Path(Capsule::getInstance()->{Capsule::DIR_CFG}, $c);
        }
        return self::$common[$c][$f];
    }

    /**
     * Returns automatically generated associated table name by class name
     *
     * @param void
     * @return string
     */
    public static function _associatedTableName()
    {
        $c = get_called_class();
        $f = __FUNCTION__;
        if (!isset(self::$common[$c][$f])) {
            self::$common[$c][$f] = Inflector::getInstance()->getAssociatedTable($c);
        }
        return self::$common[$c][$f];
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
        $table = self::_associatedTableName();
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
        $table = self::_associatedTableName();
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
        $table_name = self::_associatedTableName();
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
                AND `TABLE_NAME` = ' . $db->qt($table_name) . '
                ORDER BY `ORDINAL_POSITION` ASC';
        $columns_metadata = $db->query($sql);
        if (!$columns_metadata->num_rows) {
            throw new Exception('Table metadata not found: ' . $default_schema . '.' . $table_name);
        }

        return array(
            'table' => $table_metadata,
            'columns' => $columns_metadata->fetch_object_all()
        );
    }

    /**
     * Create associated table if not exists
     *
     * @param void
     * @return bool
     * @throws Exception
     */
    public static function _createTable()
    {
        if (self::_associatedTableExists()) {
            return true;
        }
        $path = new Path(self::_configLocation(), self::FILENAME_SQL_CREATE_TABLE);
        $data = file_get_contents($path);
        $db = Db::getInstance();
        $sql = $db->splitMultiQuery($data);
        $sql = array_shift($sql);
        $table = self::_associatedTableName();
        $reg = '/^(\\s?CREATE(?:\\s+TEMPORARY)?\\s+TABLE(?:\\s+IF\\s+NOT\\s+EXISTS)?\\s?)([\'"`]?' .
            preg_quote(self::TABLE_NAME_PLACEHOLDER) . '[\'"`]?)(.*)/isu';
        $sql = preg_replace($reg, '$1 `' . $table . '` $3', $sql);
        $db->query($sql);
        if (self::_associatedTableExists()) {
            return true;
        }
        throw new Exception('Unable to create table ' . $table);
    }

    /**
     * Удаляет таблицу. Таблица должна быть пуста.
     *
     * @param void
     * @return bool
     * @throws Exception
     */
    public static function _dropTable()
    {
        if (!self::_associatedTableExists()) {
            return true;
        }
        if (!self::_associatedTableEmpty()) {
            throw new Exception('Trying to drop nonempty table: ' . self::_associatedTableName());
        }
        $db = Db::getInstance();
        $default_schema = $db->config->dbname;
        $sql = 'DROP TABLE IF EXISTS `' . $default_schema . '`.`' . self::_associatedTableName() . '`';
        $db->query($sql);
        if (self::_associatedTableExists()) {

        }
        return true;
    }

    /**
     * Пересоздать заново таблицу. Невозможно пересоздать непустую таблицу.
     *
     * @param void
     * @throws Exception
     */
    public static function _reCreateTable()
    {
        self::_dropTable();
        self::_createTable();
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
            self::$common[$c][$f] = self::_loadConfig();
        }
        return self::$common[$c][$f];
    }

    /**
     * Загружает файл конфигурации из кэша
     *
     * @param void
     * @return array
     */
    protected static function _loadConfig()
    {
        $class = get_called_class();
        if (!Storage::getInstance()->exists($class)) {
            $data = self::_loadConfigData();
            // post processing values like "config.some_value.another_value"
            array_walk_recursive($data, function (& $v, $k) use ($data, $class) {
                $v = str_replace('__CLASS__', $class, $v);
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
     * Загружает файл конфигурации
     *
     * @param void
     * @return array
     * @throws Exception
     */
    protected static function _loadConfigData()
    {
        $path = new Path(self::_configLocation(), self::FILENAME_CONFIG_USER);
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
     * Загружает конфигурационный файл модуля (фрагмент).
     * Возвращает прочтенные данные или пустой массив, если файл отсутствует.
     * WARNING! Файл никуда не кешируется и читается заново при каждом вызове.
     * Используйте _configDataFragment вместо _loadConfigDataFragment везде, где
     * это возможно.
     *
     * @param void
     * @return array
     * @throws Exception
     */
    protected static function _loadConfigDefaultData()
    {
        $class = get_called_class();
        $path = new Path(self::_configLocation(), self::FILENAME_CONFIG_DEFAULT);
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
     * Создание массива данных для конфига
     *
     * @param void
     * @return array
     * @throws Exception
     */
    public static function _buildConfigDefault()
    {
        $inflector = Inflector::getInstance();
        $data = self::_loadInformationSchema();
        $config_data = array(
            'name' => String::ucfirst(preg_replace('/[^a-z0-9]/isu', ' ', $data['table']->TABLE_NAME)),
            'title' => $data['table']->TABLE_COMMENT,
            'description' => null,
            'help' => null,
            'comment' => null,
            'label' => null,
            'properties' => array()
        );
        foreach ($data['columns'] as $column) {
            $property_name = $inflector->getAssociatedProperty($column->COLUMN_NAME);
            $tmp = array(
                'title' => $column->COLUMN_COMMENT,
                'description' => $column->COLUMN_COMMENT,
                'help' => $column->COLUMN_COMMENT,
                'comment' => $column->COLUMN_COMMENT,
                'label' => $column->COLUMN_COMMENT,
            );
            foreach ($column as $k => $v) {
                $tmp[$inflector->getAssociatedProperty(strtolower($k))] = $v;
            }
            $tmp['isNullable'] = ('YES' === strtolower($tmp['isNullable'])) ? true : false;
            $tmp['validator'] = null;
            $tmp['column'] = array(
                'c1' => array(
                    'dataType' => $tmp['dataType'],
                    'order' => 1000 * $tmp['ordinalPosition']
                )
            );
            $tmp['formElement'] = array(
                'c1' => array(
                    'dataType' => $tmp['dataType'],
                    'order' => 1000 * $tmp['ordinalPosition']
                )
            );

            $config_data['properties'][$property_name] = $tmp;
        }
        $config = new Config($config_data);
        $json = $config->toJson();
        $path = self::_createFileConfigDefault();
        $bytes_written = file_put_contents($path, $json, LOCK_EX);
        if (false === $bytes_written) {
            throw new Exception('Unable to write file config default: ' . $path);
        }
        return $json;
    }

    /**
     * Установка таблицы
     *
     * @throws Exception
     * @param void
     */
    public static function _installTable()
    {
        self::_reCreateTable();
        $db = Db::getInstance();
        $default_schema = $db->config->dbname;
        $table_name = self::_associatedTableName();
        $sql = 'SHOW CREATE TABLE `' . $default_schema . '`.`' . $table_name . '`';
        $data = $db->query($sql)->fetch_assoc();
        if (!$data) {
            throw new Exception('Unable to retrieve create table script');
        }
        $script = array_pop($data);
        $reg = '/^(\\s?CREATE(?:\\s+TEMPORARY)?\\s+TABLE(?:\\s+IF\\s+NOT\\s+EXISTS)?\\s?)([\'"`]?' .
            preg_quote($table_name) . '[\'"`]?)(.*)/isu';
        $script = preg_replace($reg, '$1`' . self::TABLE_NAME_PLACEHOLDER . '`$3', $script);
        $path = self::_createFileSqlCreateTableDefault();
        $bytes_written = file_put_contents($path, $script, LOCK_EX);
        if (false === $bytes_written) {
            throw new Exception('Unable to write file sql create table default: ' . $path);
        }
        if (self::_associatedTableEmpty()) {
            self::_initSql();
        }
    }

    /**
     * Выполнить init.sql если таблица пуста
     *
     * @param void
     * @return bool
     * @throws Exception
     */
    public static function _initSql()
    {
        $db = Db::getInstance();
        $default_schema = $db->config->dbname;
        $current_schema = $db->selectSchema();
        if ($default_schema !== $current_schema) {
            throw new Exception('Current schema is not equal default schema');
        }
        $table_name = self::_associatedTableName();
        if (!self::_associatedTableExists()) {
            throw new Exception('Associated table not found: ' . $default_schema . '.' . $table_name);
        }
        if (!self::_associatedTableEmpty()) {
            return false;
        }
        $path = self::_createFileSqlInitScript();
        $sql_code = file_get_contents($path);
        $sql_list = $db->splitMultiQuery($sql_code);
        foreach ($sql_list as $sql) {
            $sql = preg_replace('/[\'"`]?' . preg_quote(self::TABLE_NAME_PLACEHOLDER) . '[\'"`]?/', '`' . $table_name . '`', $sql);
            $db->query($sql);
        }
    }
}