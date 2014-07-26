<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 07.12.2013 1:37:41 YEKT 2013                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\DataModel\Table;

use Capsule\DataModel\Table\Columns\Columns;
use Capsule\DataModel\Table\Exception;
use Capsule\DataModel\Table\Indexes\Indexes;
use Capsule\Common\Filter;
use Capsule\Validator\DbTableName;
use Capsule\Validator\StringLength;
use Capsule\Core;

/**
 * Table.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 * @property Indexes $indexes Indexes collection
 * @property Columns $columns Columns collection
 * @property string $engine Storage engine
 * @property string $autoIncrement Start auto increment
 * @property string $defaultCharset Character set
 * @property string $comment Table comment
 * @property string $name Table name
 */
class Table
{
    /**
     * Default storage engine
     *
     * @var string
     */
    const DEFAULT_ENGINE = 'InnoDB';

    /**
     * Supported storage engine
     *
     * @var array
     */
    protected $supportedStorageEngine = array(
    	'MyIsam',
        'InnoDB'
    );

    /**
     * Default character set
     *
     * @var string
     */
    const DEFAULT_CHARSET = 'utf8';

    /**
     * Supported character set
     *
     * @var array
     */
    protected $supportedCharset = array(
        'utf8'
    );

    /**
     * Default auto increment initial value
     *
     * @var string
     */
    const DEFAULT_AUTO_INCREMENT = '1';

    /**
     * internal data
     *
     * @var array
     */
    protected $data = array();

    /**
     * Class properties.
     *
     * @var array
     */
    protected static $_common = array();

    /**
     * Indexes collection
     *
     * @var Indexes
     */
    protected $indexes;

    /**
     * Columns collection
     *
     * @var Columns
     */
    protected $columns;

    /**
     * Storage engine
     *
     * @var unknown
     */
    protected $engine = self::DEFAULT_ENGINE;

    /**
     * Auto increment initial value
     *
     * @var int
     */
    protected $autoIncrement = self::DEFAULT_AUTO_INCREMENT;

    /**
     * Default character set
     *
     * @var string
     */
    protected $defaultCharset = self::DEFAULT_CHARSET;

    /**
     * @param void
     * @return self
     */
    public function __construct() {
        $this->indexes = new Indexes();
        $this->columns = new Columns();
        $this->engine = self::DEFAULT_ENGINE;
        $this->autoIncrement = self::DEFAULT_AUTO_INCREMENT;
        $this->defaultCharset = self::DEFAULT_CHARSET;
    }

    /**
     * Возвращает значение свойства или значение по умолчанию
     *
     * @param string $name
     * @param mixed  $default
     * @return mixed
     */
    public function get($name, $default = null) {
        $getter = 'get' . ucfirst($name);
        if (method_exists($this, $getter)) {
            return $this->$getter($name);
        }
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        return $default;
    }

    /**
     * Возвращает значение свойства.
     *
     * @param string $name
     * @throws Exception
     * @return mixed
     */
    public function __get($name) {
        $getter = 'get' . ucfirst($name);
        if (method_exists($this, $getter)) {
            return $this->$getter($name);
        }
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        $msg = 'Undefined property: ' . get_class($this) . '::$' . $name;
        throw new Exception($msg);
    }

    /**
     * Обработка установки значения свойства.
     *
     * @param string $name
     * @param mixed $value
     * @throws Exception
     * @return void
     */
    public function __set($name, $value) {
        $setter = 'set' . ucfirst($name);
        if (method_exists($this, $setter)) {
            return $this->$setter($value, $name);
        }
        $this->data[$name] = $value;
        return $this;
    }

    /**
     * isset() overloading
     *
     * @param  string $name
     * @return boolean
     */
    public function __isset($name) {
        return array_key_exists($name, $this->data) || property_exists($this, $name);
    }

    /**
     * @param Columns $value
     * @param string $name
     * @return \Capsule\Table
     */
    protected function setColumns(Columns $value, $name) {
        $msg = 'Readonly property: ' . get_class($this) . '::$' . $name;
        throw new Exception($msg);
    }

    /**
     * @param Indexes $value
     * @param string $name
     * @return \Capsule\Table
     */
    protected function setIndexes(Indexes $value, $name) {
        $msg = 'Readonly property: ' . get_class($this) . '::$' . $name;
        throw new Exception($msg);
    }

    /**
     * @param string $value
     * @param string $name
     */
    protected function setEngine($value, $name) {
        foreach ($this->supportedStorageEngine as $engine) {
            if (strtolower($value) === strtolower($engine)) {
                $this->data[$name] = $engine;
                return $this;
            }
        }
        $msg = 'Wrong storage engine value';
        throw new Exception($msg);
    }

    /**
     * @param string $value
     * @param string $name
     */
    protected function setDefaultCharset($value, $name) {
        foreach ($this->supportedCharset as $charset) {
            if (strtolower($value) === strtolower($charset)) {
                $this->data[$name] = $charset;
                return $this;
            }
        }
        $msg = 'Wrong storage engine value';
        throw new Exception($msg);
    }

    /**
     * @param int $value
     * @param string $name
     * @throws Exception
     */
    protected function setAutoIncrement($value, $name) {
        $value = Filter::intGtZero($value, null);
        if (is_null($value)) {
            $msg = 'Wrong auto increment value';
            throw new Exception($msg);
        }
        $this->data[$name] = $value;
    }

    /**
     * @param int $value
     * @param string $name
     * @throws Exception
     */
    protected function setName($value, $name) {
        $validator = self::_nameValidator();
        if ($validator->isValid($value)) {
            $this->data[$name] = $validator->getClean();
            return $this;
        }
        $msg = 'Invalid table name. ' . $validator->message;
        throw new Exception($msg);
    }

    /**
     * @param int $value
     * @param string $name
     * @throws Exception
     */
    protected function setComment($value, $name) {
        $validator = self::_commentValidator();
        if ($validator->isValid($value)) {
            $this->data[$name] = $validator->getClean();
            return $this;
        }
        $msg = 'Invalid table comment. ' . $validator->message;
        throw new Exception($msg);
    }

    /**
     * @param void
     * @return DbFieldName
     */
    protected static function _nameValidator() {
        $key = __FUNCTION__;
        if (!array_key_exists($key, self::$_common)) {
            self::$_common[$key] = new DbTableName;
        }
        return self::$_common[$key];
    }

    /**
     * @param void
     * @return String
     */
    protected static function _commentValidator() {
        $key = __FUNCTION__;
        if (!array_key_exists($key, self::$_common)) {
            $validator = new StringLength;
            $validator->max = 100;
            self::$_common[$key] = $validator;
        }
        return self::$_common[$key];
    }

    /**
     * explicit conversion to string
     *
     * @param void
     * @return string
     */
    public function toString() {
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . $this->name . '` (';
        $tmp[] = $this->columns->toString();
        $tmp[] = $this->indexes->toString();
        $sql[] = Core\join_ne(',' . chr(10), $tmp);
        $sql[] = ') ENGINE=' . $this->engine . ' DEFAULT CHARSET='
                . $this->defaultCharset;
        $sql = join(chr(10), $sql);
        if (isset($this->comment) && $this->comment) {
            $sql.= ' COMMENT="' . $this->comment . '"';
        }
        return $sql;
    }

    /**
     * implicit conversion to a string
     *
     * @param void
     * @return string
     */
    public function __toString() {
        return $this->toString();
    }
}