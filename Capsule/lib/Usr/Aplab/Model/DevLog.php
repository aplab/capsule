<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 12.07.2014 11:01:01 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Usr\Aplab\Model;

use Capsule\Module\DevLog as d;
use Capsule\Db\Db;
/**
 * DevLog.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class DevLog extends d
{
    protected static $itemsPerPage = 10;
    
    /**
     * возвращает массив допустимых индексов
     * 
     * @param void
     * @return array
     */
    public static function possibleIndexes() {
        $n = self::numberActualActive();
        $i = (int)floor($n / self::$itemsPerPage);
        $i = $i > 0 ? --$i : $i;
        return $i ? range($i, 1) : array();
    }
    
    /**
     * Заргужает и возвращает массив объектов по соответствующему индексу
     * 
     * @param int $i
     * @return array
     */
    public static function loadIndex($i = null) {
        $pi = self::possibleIndexes();
        if (is_null($i)) {
            if (empty($pi)) {
                // нет ни одного индекса
                $sql = 'SELECT * FROM `' . self::config()->table->name . '`
                        WHERE `active`
                        AND `datetime` <= NOW()
                        ORDER BY `datetime` DESC, `id` DESC';
                return static::populate(Db::getInstance()->query($sql));
            }
            $n = self::numberActualActive();
            $max_index = reset($pi);
            $limit = $n - $max_index * self::$itemsPerPage;
            $sql = 'SELECT * FROM `' . self::config()->table->name . '`
                        WHERE `active`
                        AND `datetime` <= NOW()
                        ORDER BY `datetime` DESC, `id` DESC 
                        LIMIT ' . $limit;
            return static::populate(Db::getInstance()->query($sql));
        } else {
            if (!in_array($i, $pi)) {
                return array();
            }
            $n = self::numberActualActive();
            $limit = $n - $i * self::$itemsPerPage;
            $sql = 'SELECT * FROM `' . self::config()->table->name . '`
                        WHERE `active`
                        AND `datetime` <= NOW()
                        ORDER BY `datetime` DESC, `id` DESC
                        LIMIT ' . $limit . ', ' . self::$itemsPerPage;
            return static::populate(Db::getInstance()->query($sql));
        }
    }
    
    public function laterItemId() {
        $db = Db::getInstance();
        $sql = 'SELECT `id`
                FROM `' . self::config()->table->name . '`
                WHERE `active` 
                AND (`datetime` > ' . $db->qt($this->datetime) . ' 
                    OR (`datetime` = ' . $db->qt($this->datetime) . ' 
                        AND `id` > ' . $db->qt($this->id) . '))
                ORDER BY `datetime` ASC, `id` ASC
                LIMIT 1';
        return $db->query($sql)->fetch_one();
    }
    
    public function earlierItemId() {
        $db = Db::getInstance();
        $sql = 'SELECT `id`
                FROM `' . self::config()->table->name . '`
                WHERE `active` 
                AND (`datetime` < ' . $db->qt($this->datetime) . ' 
                    OR (`datetime` = ' . $db->qt($this->datetime) . ' 
                        AND `id` < ' . $db->qt($this->id) . '))
                ORDER BY `datetime` DESC, `id` DESC
                LIMIT 1';
        return $db->query($sql)->fetch_one();
    }
}