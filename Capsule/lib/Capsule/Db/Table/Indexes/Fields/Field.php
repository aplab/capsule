<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 04.12.2013 1:23:29 YEKT 2013                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\DataModel\Table\Indexes\Fields;

use Capsule\DataModel\Table\Exception;
use Capsule\Common\Filter;

/**
 * Field.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 * @property boolean $order Сортировка индекса
 * @property int $length Длина индекса
 * @property int $position Порядок поля внутри индекса
 */
class Field 
{
    /**
     * Константы сортировки поля в индексе
     * 
     * @var int
     */
    const ORDER_ASC = 'ASC';
    const ORDER_DESC = 'DESC';
    
    /**
     * internal data
     *
     * @var array
     */
    protected $data = array();
    
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
     * @return \Capsule\DataModel\Table\Index\Fields\Field
     */
    protected function setPosition($value, $name) {
        $value = Filter::int($value, null);
        if (is_null($value)) {
            $msg = 'Wrong index field position. Int required. Negative number allowed.';
            throw new Exception($msg);
        }
        $this->data[$name] = $value;
        return $this;
    }
    
    /**
     * @param int $value
     * @param string $name
     * @throws Exception
     * @return \Capsule\DataModel\Table\Index\Fields\Field
     */
    protected function setLength($value, $name) {
        $value = Filter::intGtZero($value, null);
        if (is_null($value)) {
            $msg = 'Wrong index length. Int greater than 0 required.';
            throw new Exception($msg);
        }
        $this->data[$name] = $value;
        return $this;
    }
    
    /**
     * @param string $value
     * @param string $name
     * @throws Exception
     * @return \Capsule\DataModel\Table\Index\Fields\Field
     */
    protected function setOrder($value, $name) {
        if (self::ORDER_ASC === $value or self::ORDER_DESC === $value) {
            $this->data[$name] = $value;
            return $this;
        }
        $msg = 'Wrong index field order. ASC or DESC required.';
        throw new Exception($msg);
        
    }
    
    /**
     * explicit conversion to string
     * 
     * @param void
     * @return string
     */
    public function toString() {
        $parts = array();
        if (isset($this->length)) {
            $parts[] = '(' . $this->length . ')';
        }
        if (isset($this->order)) {
            $parts[] = $this->order;
        }
        return join(' ', $parts);
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