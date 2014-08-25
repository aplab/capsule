<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 14.12.2013 0:08:35 YEKT 2013                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\DataStruct;

use Iterator, Countable;
use Capsule\Exception;

/**
 * Config.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Config implements Iterator, Countable
{
    /**
     * Internal data
     *
     * @var array
     */
    protected $data = array();

    /**
     * count(): defined by Countable interface.
     *
     * @see    Countable::count()
     * @return integer
     */
    public function count() {
        return sizeof($this->data);
    }

    /**
     * current(): defined by Iterator interface.
     *
     * @see    Iterator::current()
     * @return mixed
     */
    public function current() {
        return current($this->data);
    }

    /**
     * key(): defined by Iterator interface.
     *
     * @see    Iterator::key()
     * @return mixed
     */
    public function key() {
        return key($this->data);
    }

    /**
     * next(): defined by Iterator interface.
     *
     * @see    Iterator::next()
     * @return void
     */
    public function next() {
        next($this->data);
    }

    /**
     * rewind(): defined by Iterator interface.
     *
     * @see    Iterator::rewind()
     * @return void
     */
    public function rewind() {
        reset($this->data);
    }

    /**
     * valid(): defined by Iterator interface.
     *
     * @see    Iterator::valid()
     * @return boolean
     */
    public function valid() {
        return ($this->key() !== null);
    }

    /**
     * @param array $data
     * @return self
     */
    public function __construct(array $data = array()) {
        foreach ($data as $property_name => $property_value) {
            if (is_array($property_value)) {
                $this->data[$property_name] = new self($property_value);
            } else {
                $this->data[$property_name] = $property_value;
            }
        }
    }

    /**
     * Возвращает значение свойства или значение по умолчанию
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get($name, $default = null) {
        return array_key_exists($name, $this->data) ? $this->data[$name] : $default;
    }

    /**
     * Возвращает значение свойства.
     *
     * @param string $name
     * @throws Exception
     * @return mixed
     */
    public function __get($name) {
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
        if (array_key_exists($name, $this->data)) {
            $msg = 'Readonly property: ' . get_class($this) . '::$' . $name;
        } else {
            $msg = 'Unknown property: ' . get_class($this) . '::$' . $name;
        }
        throw new Exception($msg);
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
     * implicit conversion to a string
     *
     * @param void
     * @return string
     */
    public function __toString() {
        return $this->toString();
    }

    /**
     * Return an associative array of the stored data.
     *
     * @param void
     * @return array
     */
    public function toArray() {
        $ret = array();
        foreach ($this->data as $key => $value) {
            if ($value instanceof self) {
                $ret[$key] = $value->toArray();
            } else {
                $ret[$key] = $value;
            }
        }
        return $ret;
    }
}