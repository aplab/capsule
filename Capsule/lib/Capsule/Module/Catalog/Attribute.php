<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2015                                                   |
// +---------------------------------------------------------------------------+
// | 11 мая 2015 г. 1:15:01 YEKT 2015                                              |
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

use Capsule\Traits\optionsDataList;
use Capsule\Db\Db;
use Capsule\Unit\UnitTsUsr;
use Capsule\DataModel\Config\Properties\Property;
use Capsule\Module\Catalog\Type\Type;
use Capsule\Core\Fn;
/**
 * Property.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Attribute extends UnitTsUsr
{
    use optionsDataList;
    
    /**
     * Возвращает массив объектов атрибутов, привязанных к данномй разделу
     * 
     * @param Section|int $section
     * @return Attribute[]
     */
    public static function section($section) {
        $db = Db::getInstance();
        $section_id = ($section instanceof Section) ? $section->id : intval($section, 10);
        $attr_table = self::config()->table->name;
        $link_table = AttributeSectionLink::config()->table->name; 
        $sql = 'SELECT `at`.*, `lt`.`sort_order`, `lt`.`tab_name`  
                FROM `' . $attr_table . '` AS `at`
                INNER JOIN `' . $link_table . '` AS `lt`
                ON `at`.`id` = `lt`.`attribute_id`
                WHERE `lt`.`container_id` = ' . $db->qt($section_id) . '
                ORDER BY `lt`.`sort_order` ASC';
        return static::populate(Db::getInstance()->query($sql));
    }
    
    /**
     * Возвращает массив объектов атрибутов, привязанных к данномй продукту
     * 
     * @param Product $product
     * @return array
     */
    public static function product(Product $product) {
        return self::section($product->get('containerId'));
    }
    
    /**
     * Создает свойство. Могут быть дополнительные свойства 
     * sortOrder и tabName у атрибута, если атрибут загружен с привязкой к
     * секции или продукту
     * 
     * @param void
     * @return Property|null
     */
    public function property() {
        $type = Fn::cc(ucfirst($this->type), Type::ns());
        $config = $type::config();
        $config['title'] = $this->name;
        $config['name'] = $this->token ?: 'attr[' . $this->id . ']';
        if (isset($config['formElement'])) {
            foreach ($config['formElement'] as & $f) {
                $f['tab'] = $this->tabName;
                $f['order'] = $this->sortOrder;
            }
        }
        return new Property($config);
    }
    
    /**
     * Привязка к namespace
     * 
     * @param void
     * @return string
     */
    public static function ns() {
        return __NAMESPACE__;
    }
}