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
/**
 * Property.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Attribute extends UnitTsUsr
{
    const DEFAULT_TAB = 'Attribute';
    
    const DEFAULT_ORDER = 10000000;
    
    use optionsDataList;
    
    public static function section($section) {
        $db = Db::getInstance();
        $section_id = ($section instanceof Section) ? $section->id : intval($section, 10);
        $attr_table = self::config()->table->name;
        $link_table = AttributeSectionLink::config()->table->name; 
        $sql = 'SELECT `at`.*, `lt`.`sort_order` FROM `' . $attr_table . '` AS `at`
                INNER JOIN `' . $link_table . '` AS `lt`
                ON `at`.`id` = `lt`.`attribute_id`
                WHERE `lt`.`container_id` = ' . $db->qt($section_id) . '
                ORDER BY `lt`.`sort_order` ASC';
        return static::populate(Db::getInstance()->query($sql));
    }
    
    protected function getTab($name) {
        if (!array_key_exists($name, $this->data)) return self::DEFAULT_TAB; 
        return $this->data[$name];
    }
    
    protected function issetTab($name) {
        return true;
    }
    
    protected function getOrder($name) {
        if (!array_key_exists($name, $this->data)) return self::DEFAULT_ORDER;
        return $this->data[$name];
    }
    
    protected function issetOrder($name) {
        return true;
    }
}