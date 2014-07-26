<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 23.05.2014 7:31:20 YEKT 2014                                              |
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

use Capsule\App\Website\Website;
use Capsule\Common\Path;
use Capsule\Capsule;
use Capsule\App\Website\Cache;
/**
 * Page.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Page extends Element
{
    /**
     * Some key
     *
     * @var string
     */
    const AREA = 'area';
    
    /**
     * Create one page element
     *
     * @param array $data
     * @throws \Exception
     * @return \Capsule\App\Website\Structure\Page
     */
    final public static function createElement(array $data) {
        if (__CLASS__ === get_called_class()) {
            return new self($data);
        }
        $msg = 'unable to create new page';
        throw new \Exception($msg);
    }
    
    /**
     * (non-PHPdoc)
     * @see \Capsule\App\Website\Structure\Element::_init()
     */
    protected function _init(array $data) {
        if (!array_key_exists(self::AREA, $this->data)) {
            $this->data[self::AREA] = array();
            return;
        }
        if (!is_array($this->data[self::AREA])) {
            $this->data[self::AREA] = array();
            return;
        }
        $array = array(
            self::ID => null,
            'pageId' => $this->id
        );
        foreach ($this->data[self::AREA] as $area_name => $area_data) {
            $array[self::ID] = $area_name;
            if (array_key_exists(self::ID, $area_data)) {
                $msg = 'Cannot redeclare area::id. Check configuration. Remove id definition from configuration.';
                throw new \Exception($msg);
            }
            if (array_key_exists('pageId', $area_data)) {
                $msg = 'Cannot redeclare area::pageId. Check configuration. Remove pageId definition from configuration.';
                throw new \Exception($msg);
            }
            $this->data[self::AREA][$area_name] =
                new Area(array_replace($array, $area_data, $array));
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
            $id = $this->id;
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
        $path = new Path(
            Website::getInstance()->tplpath,
            $this->template);
        ob_start(); // буферизация
        include $path;
        return ob_get_clean();
    }
}