<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 07.12.2013 1:05:12 YEKT 2013                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\DataModel\Table\Columns;

use Capsule\DataModel\Table\Exception;
use Capsule\Validator\StringLength;
/**
 * Column.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 * @property string $comment
 * @property boolean $nullable
 */
abstract class Column
{
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
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        $msg = 'Unknown property: ' . get_class($this) . '::$' . $name;
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
        return array_key_exists($name, $this->data);
    }
    
    /**
     * unset() overloading
     *
     * @param  string $name
     * @return void
     */
    public function __unset($name) {
        if (array_key_exists($name, $this->data)) {
            unset ($this->data[$name]);
        }
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
     * @param string $name
     * @return string
     * @throws Exception
     */
    protected function getType($name) {
        if (!isset($this->data[$name])) {
            $tmp = explode('\\', get_class($this));
            $this->data[$name] = strtoupper(array_pop($tmp)); 
        }
        return $this->data[$name];
    }
    
    /**
     * @param unknown $value
     * @param unknown $name
     * @throws Exception
     */
    protected function setType($value, $name) {
        $msg = 'Readonly property: ' . get_class($this) . '::$' . $name;
        throw new Exception($msg);
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
    
    /**
     * explicit conversion to string
     *
     * @param void
     * @return string
     */
    abstract public function toString();
}