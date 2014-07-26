<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 04.12.2013 0:55:13 YEKT 2013                                              |
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

use Capsule\DataModel\Table\Indexes\Fields\Fields;
use Capsule\DataModel\Table\Exception;
/**
 * Index.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 * @property Fields $fields
 */
class Index 
{
    /**
     * @var string
     */
    protected $definition = 'KEY `%s` ';
    
    /**
     * Fields collection
     *
     * @var Fields
     */
    protected $fields;
    
    /**
     * @param void
     * @return self
     */
    public function __construct() {
        $this->fields = new Fields;
    }
    
    /**
     * Возвращает значение свойства.
     *
     * @param string $name
     * @throws Exception
     * @return mixed
     */
    public function __get($name) {
        if (property_exists($this, $name)) {
            return $this->$name;
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
        $msg = 'Unknown property: ' . get_class($this) . '::$' . $name;
        throw new Exception($msg);
    }
    
    /**
     * @param string $value
     * @param string $name
     * @throws Exception
     * @return \Capsule\DataModel\Table\Index\Index
     */
    protected function setFields(Fields $value, $name) {
        $msg = 'Readonly property: ' . get_class($this) . '::$' . $name;
        throw new Exception($msg);
    }
    
    /**
     * explicit conversion to string
     *
     * @param void
     * @return string
     */
    public function toString() {
        if (sizeof($this->fields)) {
            return $this->definition . '(' . $this->fields->toString() . ')';
        }
        return '';
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