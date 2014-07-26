<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 23.05.2014 7:58:49 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\App\Website\Structure;

use Capsule\App\Website\Cache;
use Capsule\Core\Fn;
/**
 * Area.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Area extends Element
{
    /**
     * Some key
     *
     * @var string
     */
    const UNIT = 'unit';
    
    /**
     * (non-PHPdoc)
     * @see \Capsule\App\Website\Structure\Element::_init()
     */
    protected function _init(array $data) {
        if (!array_key_exists(self::UNIT, $this->data)) {
            $this->data[self::UNIT] = array();
            return;
        }
        if (!is_array($this->data[self::UNIT])) {
            $this->data[self::UNIT] = array();
            return;
        }
        $array = array(
            self::ID => null,
            'areaId' => $this->id,
            'pageId' => $this->pageId,
        );
        foreach ($this->data[self::UNIT] as $unit_id => $unit_data) {
            $array[self::ID] = $unit_id;
            if (array_key_exists(self::ID, $unit_data)) {
                $msg = 'Cannot redeclare unit::id. Check configuration. Remove id definition from configuration.';
                throw new \Exception($msg);
            }
            if (array_key_exists('areaId', $unit_data)) {
                $msg = 'Cannot redeclare unit::areaId. Check configuration. Remove areaId definition from configuration.';
                throw new \Exception($msg);
            }
            if (array_key_exists('pageId', $unit_data)) {
                $msg = 'Cannot redeclare unit::pageId. Check configuration. Remove pageId definition from configuration.';
                throw new \Exception($msg);
            }
            $this->data[self::UNIT][$unit_id] = new Unit(array_replace($array, $unit_data, $array));
        }
    }
    
    /**
     * Explicit conversion to string
     *
     * @param void
     * @return string
     */
    public function toString() {
        if ($this->cache) {
            $id = Fn::concat_ws('=>#', $this->pageId, $this->id);
            $cache = Cache::getInstance();
            $content = $cache->get($id);
            if (is_null($content)) {
                $content = $this->build();
                $cache->set($id, $content, $this->cache);
            }
            return $content;
        } else {
            return $this->build();
        }
    }
    
    /**
     * Собирает и возвращает контент страницы
     *
     * @param void
     * @return string
     */
    protected function build() {
        $content = '';// инициализация
        foreach ($this->unit as $unit) {
            $content .= $unit->toString();
        }
        return $content;
    }
}