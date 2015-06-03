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
     * @param void
     * @return array
     */
    public function attrInit() {
        $class = get_class($this);
        if (!array_key_exists($class, self::$attr)) {
            $attr_list = Attribute::product($this);
            self::$attr[$class] = $attr_list;
            $properties = $this::config()->properties;
            foreach ($attr_list as $attr_id => $attr) $properties->inject($attr->property());
        }
        return self::$attr[$class];
    }

    /**
     * Load attribute values from database
     *
     * @param void
     * @return array
     */
    public function attrPull() {
        $values = Value::getInstance()->product($this);
        $attr_list = $this->attrInit();
        foreach ($attr_list as $attr_id => $attr) {
            $property = $attr->property();
            if (array_key_exists($attr_id, $values)) $this->data[$property->name] = $values[$attr_id];
        }
    }

    public function attrPush() {
        $attr_list = $this->attrInit();
        foreach ($attr_list as $attr) {
            $property = $attr->property();
            $property_name = $property->name;
            if (array_key_exists($property_name, $this->data)) {
                Value::getInstance()->set($this, $attr, $this->data[$property_name]);
            }
        }
    }
}