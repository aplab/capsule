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

use Capsule\Common\Filter;
use Capsule\DataModel\Table\Exception;
/**
 * Year.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 * @property int $length
 * @property boolean $nullable
 * @property int $default
 */
class Year extends Column
{
    public function toString() {
        $tmp = array($this->type);
        if (isset($this->length)) {
            $tmp[] = '(' . $this->length . ')';
        }
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
    
    /**
     * @param int $value
     * @param string $name
     * @throws Exception
     * @return \Capsule\DataModel\Table\Index\Fields\Field
     */
    protected function setLength($value, $name) {
        $value = Filter::intGtZero($value, null);
        if (is_null($value)) {
            $msg = 'Wrong length. Int 2 or 4 required.';
            throw new Exception($msg);
        }
        $value = strval($value);
        if (('2' === $value) || ('4' === $value)) {
            $this->data[$name] = $value;
            return $this;
        }
        $msg = 'Wrong length. Int 2 or 4 required.';
        throw new Exception($msg);
    }
}