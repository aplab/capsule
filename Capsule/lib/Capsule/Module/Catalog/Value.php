<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2015                                                   |
// +---------------------------------------------------------------------------+
// | 19 мая 2015 г. 22:24:15 YEKT 2015                                              |
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

use Capsule\Core\Singleton;
use Capsule\DataModel\Inflector;
use Capsule\Db\Db;
/**
 * Value.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Value extends Singleton
{
    private $table;
    
    /**
     * Защищенный конструктор
     *
     * @param void
     * @return self
     * @throws Exception
     */
    protected function __construct() {
        $this->table = Inflector::getInstance()->getAssociatedTable($this);
        $db = Db::getInstance();
        if (!$db->tableExists($this->table)) {
            $sql = 'CREATE TABLE IF NOT EXISTS `' . $this->table . '` (
                    
                    `product_id` BIGINT UNSIGNED NOT NULL COMMENT "идентификатор товара",
                    `attribute_id` BIGINT UNSIGNED NOT NULL COMMENT "идентификатор атрибута",
                    
                    `integer` BIGINT NOT NULL DEFAULT 0 COMMENT "Value if type is signed integer",
                    `unsigned_integer` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT "Value if type is unsigned integer",
                    `string` VARCHAR(255) NOT NULL DEFAULT "" COMMENT "Value if type is string",
                    `text` TEXT COMMENT "Value if type is text",
    
                    PRIMARY KEY (`product_id`, `attribute_id`))
                    ENGINE = InnoDB COMMENT = ' . $db->qt(__CLASS__);
            $db->query($sql);
            if (!$db->tableExists($this->table, true)) {
                $msg = 'Unable to create table ' . $this->table;
                throw new \Exception($msg);
            }
        }
    }
    
    /**
     * Возвращает значения атрибутов продукта.
     * 
     * @param Product|int $product
     */
    public function product($product) {
        $product_id = ($product instanceof Product) ? $product->id : intval($product, 10);
        $db = Db::getInstance();
        $table_attr = Attribute::config()->table->name;
        $sql = 'SELECT `tv`.*, `ta`.`type`
                FROM `' . $this->table . '` AS `tv` 
                INNER JOIN `' . $table_attr . '` AS `ta`
                ON `tv`.`attribute_id` = `ta`.`id`
                WHERE `tv`.`product_id` = ' . $db->qt($product_id);
        $data = $db->query($sql);
        $ret = array();
        foreach ($data as $i) if (array_key_exists($i['type'], $i)) $ret[$i['attribute_id']] = $i[$i['type']];
        return $ret;
    }
}