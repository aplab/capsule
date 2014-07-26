<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 02.05.2014 15:04:49 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Ui\DataGrid\Cell;

use Capsule\Core\Fn;
/**
 * Bind.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Bind extends Cell
{
    protected static $cache;
    
    protected function options() {
        if (!self::$cache) {
            $class = Fn::create_classname($this->col->property->bind);
            self::$cache = $class::optionsDataList();
        }
        return self::$cache;
    }
    
    public function getValue($id) {
        $options = $this->options();
        return array_key_exists($id, $options) ? $options[$id] : null;
    }
}