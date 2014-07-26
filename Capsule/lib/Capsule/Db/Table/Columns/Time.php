<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 10.12.2013 0:10:33 YEKT 2013                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\DataModel\Table\Columns;

/**
 * Time.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 * @property int $length
 * @property boolean $nullable
 * @property int $default
 */
class Time extends Column
{
    public function toString() {
        $tmp = array($this->type);
        if (!$this->get('nullable')) {
            $tmp[] = 'NOT NULL';
        }
        if (isset($this->default)) {
            $tmp[] = 'DEFAULT';
            if (is_null($this->default)) {
                $tmp[] = 'NULL';
            } else {
                $tmp[] = $this->default;
            }
        }
        if ($this->get('comment')) {
            $tmp[] = 'COMMENT "' . $this->comment . '"';
        }
        return join(' ', $tmp);
    }
}