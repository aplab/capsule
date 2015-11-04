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
     * @return ReflectionClass
     */
    final protected static function _reflectionClass()
    {
        $class = get_called_class();
        if (!isset(self::$common[$class][__FUNCTION__])) {
            self::$common[$class][__FUNCTION__] = new \ReflectionClass($class);
        }
        return self::$common[$class][__FUNCTION__];
    }

    /**
     * Returns path to module config
     * @return string
     */
    public static function _configLocation()
    {
        $class = get_called_class();
        if (!isset(self::$common[$class][__FUNCTION__])) {
            self::$common[$class][__FUNCTION__] =
                new Path(Capsule::getInstance()->{Capsule::DIR_CFG}, $class);
        }
        return self::$common[$class][__FUNCTION__];
    }
}