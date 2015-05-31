<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2015                                                   |
// +---------------------------------------------------------------------------+
// | 11 мая 2015 г. 1:16:09 YEKT 2015                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Module\Catalog;

use Capsule\Unit\Nested\NamedItem;
/**
 * Product.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Product extends NamedItem
{
    /**
     * Attribute cache
     * 
     * @var array
     */
    protected static $attr = array();
    
    /**
     * 
     * @throws \Exception
     * @return array|Ambigous <mixed, NULL>
     */
    public function attr() {
        $n = func_num_args();
        if (!$n) {
            $class = get_class($this);
            if (!array_key_exists($class, self::$attr)) {
                $tmp = Attribute::product($this);
                self::$attr[$class] = $tmp;
                $prp = $this::config()->properties;
                foreach ($tmp as $i) $prp->inject($i->property());
            }
            return self::$attr[$class];
        }
        if (1 === $n) return $this->_attr_get_value(func_get_arg(0));
        if (2 === $n) return $this->_attr_set_value(func_get_arg(0), func_get_arg(1));
        $msg = 'Wrong parameters';
        throw new \Exception($msg);
    }
    
    /**
     * Возвращает значение атрибута или null
     * 
     * @param string $attr
     * @return mixed|null
     */
    protected function _attr_get_value($attr) {
        // Получаем все атрибуты
        $attr_list = $this->attr();
        // Получаем все значения
        $values = Value::getInstance()->product($this);
        if (array_key_exists($attr, $values)) return $values[$attr];
        foreach ($attr_list as $i) {
            if ($attr === $i->property()->name) return isset($values[$i->id]) ? $values[$i->id] : null;
        }
        return null;
    }
    
    /**
     * Возвращает значение атрибута или null
     *
     * @param string $name
     * @return mixed|null
     */
    protected function _attr_set_value($name, $value) {
        // Получаем все атрибуты
        $attr_list = $this->attr();
        $attr = null;
        $attr_id = null;
        if (array_key_exists($name, $attr_list)) {
            $attr = $attr_list[$name]; 
        } else {
            foreach ($attr_list as $i) {
                if ($name === $i->property()->name) $attr = $i;
            }
        }
        if ($attr instanceof Attribute) {
            $property = $attr->property();
            $validator = $property->get('validator');
            if ($validator) {
                if ($validator->isValid($value)) {
                    $value = $validator->getClean();
                } else {
                    $msg = 'Invalid value: ' . get_class($this) . '::$' . $name;
                    throw new \Exception($msg);
                }
            }
            Value::getInstance()->set($this, $attr, $value);
        }
        return $this;
    }
    
    /**
     * Возвращает значение свойства. Если свойство не определено или
     * отсутствует, то генерируется исключение.
     *
     * @param  string
     * @throws Exception
     * @return mixed
     */
    public function __get($name) {
        $getter = self::_getter($name);
        if ($getter) {
            return $this->$getter($name);
        }
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        $properties = $this->config()->get('properties', new \stdClass());
        if (!isset($properties->$name)) {
            $msg = 'Undefined property: ';
            $msg.= get_class($this) . '::$' . $name;
            throw new \Exception($msg);
        }
        $method = Catalog::ATTRIBUTE_TOKEN_PREFIX;
        if (!method_exists($this, $method)) {
            $msg = 'Unknown property: ';
            $msg.= get_class($this) . '::$' . $name;
            throw new \Exception($msg);
        }
        return $this->$method($name);
    }
    
    /**
     * isset() overloading
     *
     * @param  string $name
     * @return boolean
     */
    public function __isset($name) {
        $method = 'isset' . ucfirst($name);
        if (in_array($method, self::_listMethods())) {
            return $this->$method($name);
        }
        if (array_key_exists($name, $this->data)) return true;
        $method = Catalog::ATTRIBUTE_TOKEN_PREFIX;
        if (!method_exists($this, $method)) {
            return false;
        }
        return !is_null($this->$method($name));
    }
    
    /**
     * Обрабатывает изменение значения свойства.
     *
     * @param  string $name
     * @param  mixed $value
     * @throws Exception
     * @return self
     */
    public function __set($name, $value) {
        $properties = $this->config()->get('properties', new \stdClass);
        if (isset($properties->$name)) {
            $property = $this->config()->properties->$name;
            $validator = $property->get('validator', null);
            if ($validator) {
                if ($validator->isValid($value)) {
                    $value = $validator->getClean();
                } else {
                    $msg = 'Invalid value: ' . get_class($this) . '::$' . $name;
                    throw new \Exception($msg);
                }
            }
        }
        $setter = self::_setter($name);
        if (method_exists($this, $setter)) {
            return $this->$setter($value, $name);
        }
        $method = Catalog::ATTRIBUTE_TOKEN_PREFIX;
        if (!method_exists($this, $method)) {
            $this->data[$name] = $value;
        } else {
            $this->$method($name, $value);
        }
        return $this;
    }
    
}