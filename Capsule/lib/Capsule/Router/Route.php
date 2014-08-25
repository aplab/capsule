<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 03.05.2014 8:30:34 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Router;

use Capsule\Core\Fn;
/**
 * Route.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Route implements \Countable, \Iterator
{
    protected $data = array();
    
    public function __construct() {
    	$this->append(func_get_args());
    }
    
    /**
     * @param mixed
     */
    public function append() {
        foreach (func_get_args() as $param) {
            if (!$param) {
                return;
            }
            if (is_scalar($param)) {
                $param = $this->prepare($param);
                $param = explode('/', $param);
                if (1 === sizeof($param)) {
                    $tmp = reset($param);
                    if ($tmp) {
                        $this->data[] = $tmp;
                    }
                    return;
                }
            }
            if (is_array($param)) {
                foreach ($param as $p) {
                    $this->append($p);
                }
                return;
            }
            if ($param instanceof self) {
                if ($param === $this) {
                    $param = clone($this);
                }
                foreach ($param as $p) {
                    $this->append($p);
                }
                return;
            }
            if ($param instanceof \Traversable) {
                foreach ($param as $p) {
                    $this->append($p);
                }
                return;
            }
        }
    }
    
    /**
     * helper
     *
     * @param  string $part
     * @return string
     */
    private function prepare($part) {
        return preg_replace('|/{2,}|', '/', str_replace('\\', '/', trim($part)));
    }
    
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
        return $this->data[$this->key()];
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
     * Explicit conversion to a string
     *
     * @param void
     * @return string
     */
    public function toString() {
        return $this->prepare('/' . Fn::join_ne('/', $this->data));
    }
    
    /**
     * Implicit conversion to a string
     *
     * @param void
     * @return string
     */
    public function __toString() {
        return $this->toString();
    }
    
    /**
     * Есть ли частичное совпадение начала путей
     *
     * @param mixed
     * @return
     */
    public function overlap() {
        $tmp = new self(func_get_args());
        return !!sizeof(array_intersect_assoc($this->data, $tmp->data));
    }
    
    /**
     * Содержит то что передано в параметре
     *
     * @param multitype
     * @return boolean
     */
    public function contain() {
        $tmp = new self(func_get_args());
        return sizeof(array_intersect_assoc($this->data, $tmp->data)) === sizeof($tmp);
    }
    
    /**
     * Содержится в том что передано в параметре
     *
     * @param multitype
     * @return boolean
     */
    public function containedIn() {
        $tmp = new self(func_get_args());
        return sizeof(array_intersect_assoc($this->data, $tmp->data)) === sizeof($this);
    }
    
    /**
     * Содержится в том что передано в параметре
     *
     * @param multitype
     * @return boolean
     */
    public function match() {
        $tmp = new self(func_get_args());
        return (sizeof($this) === sizeof($tmp)) && sizeof(array_intersect_assoc($this->data, $tmp->data)) === sizeof($this);
    }
}