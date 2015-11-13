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

namespace Capsule\DataObject\Mysql\Config;

use Iterator, Countable;
use Capsule\Json\Error;

/**
 * AbstractConfig.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
abstract class AbstractConfig implements Iterator, Countable
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
    public function count()
    {
        return sizeof($this->data);
    }

    /**
     * current(): defined by Iterator interface.
     *
     * @see    Iterator::current()
     * @return mixed
     */
    public function current()
    {
        return current($this->data);
    }

    /**
     * key(): defined by Iterator interface.
     *
     * @see    Iterator::key()
     * @return mixed
     */
    public function key()
    {
        return key($this->data);
    }

    /**
     * next(): defined by Iterator interface.
     *
     * @see    Iterator::next()
     * @return void
     */
    public function next()
    {
        next($this->data);
    }

    /**
     * rewind(): defined by Iterator interface.
     *
     * @see    Iterator::rewind()
     * @return void
     */
    public function rewind()
    {
        reset($this->data);
    }

    /**
     * valid(): defined by Iterator interface.
     *
     * @see    Iterator::valid()
     * @return boolean
     */
    public function valid()
    {
        return ($this->key() !== null);
    }

    /**
     * @param array $data
     * @return self
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Возвращает значение свойства или значение по умолчанию
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        return array_key_exists($name, $this->data) ? $this->data[$name] : $default;
    }

    /**
     * Возвращает значение свойства.
     *
     * @param string $name
     * @throws \Exception
     * @return mixed
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        $msg = 'Undefined property: ' . get_class($this) . '::$' . $name;
        throw new \Exception($msg);
    }

    /**
     * Обработка установки значения свойства.
     *
     * @param string $name
     * @param mixed $value
     * @throws \Exception
     * @return void
     */
    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->data)) {
            $msg = 'Readonly property: ' . get_class($this) . '::$' . $name;
        } else {
            $msg = 'Unknown property: ' . get_class($this) . '::$' . $name;
        }
        throw new \Exception($msg);
    }

    /**
     * isset() overloading
     *
     * @param  string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return array_key_exists($name, $this->data) || property_exists($this, $name);
    }

    /**
     * implicit conversion to a string
     *
     * @param void
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * explicit conversion to string
     *
     * @param void
     * @return string
     */
    abstract public function toString();

    /**
     * Return an associative array of the stored data.
     *
     * @param void
     * @return array
     */
    public function toArray()
    {
        $ret = array();
        $data = $this->data;
        foreach ($data as $key => $value) {
            if ($value instanceof self) {
                $ret[$key] = $value->toArray();
            } elseif (is_array($value)) {
                $ret[$key] = $this->_extractArray($value);
            } else {
                $ret[$key] = $value;
            }
        }
        return $ret;
    }

    /**
     * toArray helper
     *
     * @param array $array
     * @return array
     */
    protected function _extractArray(array $array)
    {
        $ret = array();
        foreach ($array as $key => $value) {
            if ($value instanceof self) {
                $ret[$key] = $value->toArray();
            } elseif (is_array($value)) {
                $ret[$key] = $this->_extractArray($value);
            } else {
                $ret[$key] = $value;
            }
        }
        return $ret;
    }

    /**
     * Return an json of the stored data.
     *
     * @param void
     * @return array
     * @throws \Exception
     */
    public function toJson($options = null)
    {
        if (is_null($options)) {
            $options = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
        }
        $json = json_encode($this->toArray(), $options);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \Exception(Error::getLastError());
        }
        return $json;
    }
}