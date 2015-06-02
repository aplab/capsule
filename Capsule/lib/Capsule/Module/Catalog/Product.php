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
     * Initialize dynamic properties and returns all binding attributes.
     *
     * @throws \Exception
     * @return array
     */
    public function attr() {
        $class = get_class($this);
        if (!array_key_exists($class, self::$attr)) {
            $attr_list = Attribute::product($this);
            self::$attr[$class] = $attr_list;
            $properties = $this::config()->properties;
            foreach ($attr_list as $attr_id => $attr) $properties->inject($attr->property());
        }
        $values = Value::getInstance()->product($this);
        $attr_list = self::$attr[$class];
        foreach ($attr_list as $attr_id => $attr) {
            $property = $attr->property();
            if (array_key_exists($attr_id, $values)) $this->data[$property->name] = $values[$attr_id];
        }
        return self::$attr[$class];
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
        $attr_list = $this->attr();
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
        foreach ($attr_list as $i) {
            if ($name === $i->property()->name) return isset($values[$i->id]) ? $values[$i->id] : null;
        }
        $this->data[$name] = $value;
        return $this;
    }

}