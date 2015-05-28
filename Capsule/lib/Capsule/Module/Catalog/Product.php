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
    public function attr() {
        $n = func_num_args();
        if (!$n) return Attribute::product($this); 
        if (1 === $n) return $this->_attr_get_value(func_get_arg(0));
        if (2 === $n) return $this->_attr_set_value(func_get_arg(0), func_get_arg(1));
        $msg = 'Wrong parameters';
        throw new \Exception($msg);
    }
    
    protected function _attr_get_value($attr) {
        // Получаем все атрибуты
        $attr_list = $this->attr();
        // Получаем все значения
        $values = Value::getInstance()->product($this);
        if (array_key_exists($attr, $values)) return $values[$attr];
        foreach ($attr_list as $i) {
            if ($attr === $i->property()->name) return $values[$i->id];
        }
        return null;
    }
}