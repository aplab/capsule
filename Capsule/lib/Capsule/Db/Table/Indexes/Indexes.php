<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 05.12.2013 0:16:48 YEKT 2013                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\DataModel\Table\Indexes;

use Capsule\DataModel\Table\Exception;
use Iterator, Countable;
/**
 * Indexes.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Indexes implements Iterator, Countable
{
    /**
     * Indexes items
     * 
     * @var array
     */
    protected $data = array();
    
    /**
     * Multiple primary keys cannot be defined.
     * 
     * @var boolean
     */
    protected $primaryKeyExists = false;
    
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
     * Возвращает значение свойства или значение по умолчанию
     *
     * @param string $name
     * @param mixed  $default
     * @return mixed
     */
    public function get($name, $default = null) {
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
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        $msg = 'Unknown index: ' . get_class($this) . '::$' . $name;
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
    public function __set($name, Index $value) {
        if ($value instanceof PrimaryKey) {
            if ($this->primaryKeyExists) {
                $msg = 'Multiple primary keys cannot be defined.';
                throw new Exception($msg);
            }
            $this->primaryKeyExists = true;
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
            if ($this->data[$name] instanceof PrimaryKey) {
                $this->primaryKeyExists = false;
            }
            unset ($this->data[$name]);
        }
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
     * explicit conversion to string
     *
     * @param void
     * @return string
     */
    public function toString() {
        $ret = array();
        foreach ($this->data as $name => $index) {
            $ret[] = sprintf($index->toString(), $name);
        }
        return join(', ' . chr(10), $ret);
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