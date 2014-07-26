<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.5.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 29.01.2014 21:49:33 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Sequence;

use Capsule\Core\Singleton;
use Capsule\Filter\Inflector;
use Capsule\Db\Db;
use Capsule\Exception;
/**
 * Sequence.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
abstract class Sequence extends Singleton
{
    /**
     * Содержит наименование связанной таблицы
     *
     * @var string
     */
    protected $table;

    /**
     * Has been initialized
     *
     * @var boolean
     */
    protected $init;

    /**
     * Has been repair
     *
     * @var boolean
     */
    protected $repair;

    /**
     * @param void
     * @return self
     * @throws Exception
     */
    protected function __construct() {
        $this->table = Inflector::getInstance()->getAssociatedTable($this);
        $db = Db::getInstance();
        if (!$db->tableExists($this->table)) {
            $sql = 'CREATE TABLE IF NOT EXISTS `' . $this->table . '` (
                    `sequence` BIGINT UNSIGNED NOT NULL COMMENT "counter")
                    ENGINE = InnoDB COMMENT = "sequence"';
            $db->query($sql);
            if (!$db->tableExists($this->table, true)) {
                $msg = 'Unable to create table ' . $this->table;
                throw new Exception($msg);
            }
        }
    }

    /**
     * Returns next sequence value
     *
     * @param void
     * @return void
     */
    public function next() {
        $db = Db::getInstance();
        $sql = 'UPDATE `' . $this->table . '`
                SET `sequence` = LAST_INSERT_ID(`sequence` + 1)';
        $db->query($sql);
        $rows = $db->affected_rows;
        if (1 === $rows) {
            // В таблице должна быть всегда только одна строка.
            return $db->insert_id;
        }
        if (!$rows) {
            // В таблице нет записи, необходимо инициализировать счетчик.
            if ($this->init) {
                // once
                $msg = 'Unable to initialize sequence.';
                throw new Exception($msg);
            }
            $this->init = true;
            $sql = 'INSERT INTO `' . $this->table . '` (`sequence`)
                    SELECT "0" FROM DUAL
                    WHERE NOT EXISTS (
                        SELECT `sequence`
                        FROM `' . $this->table . '`
                        LIMIT 1)';
            $result = $db->query($sql);
            return $this->next();
        }
        // В таблице более 1 записи, необходимо удалить лишние записи
        if ($this->repair) {
            // once
            $msg = 'Unable to repair sequence.';
            throw new Exception($msg);
        }
        $this->repair = true;
        $sql = 'LOCK TABLE `' . $this->table . '` WRITE';
        $db->query($sql);
        $sql = 'SELECT MAX(`sequence`) FROM `' . $this->table . '`';
        $result = $db->query($sql);
        $value = $result->fetch_one();
        $sql = 'DELETE FROM `' . $this->table . '`';
        $db->query($sql);
        $sql = 'INSERT INTO `' . $this->table . '`
                VALUES (' . $db->qt($value) . ')';
        $db->query($sql);
        $sql = 'UNLOCK TABLE';
        $db->query($sql);
        return $this->next();
    }
}