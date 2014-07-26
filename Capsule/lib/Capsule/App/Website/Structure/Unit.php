<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 23.05.2014 8:01:30 YEKT 2014                                              |
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

use Capsule\Core\Fn;
use Capsule\App\Website\Cache;
use Capsule\App\Website\Website;
/**
 * Unit.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Unit extends Element
{
	public function toString() {
	   if ($this->cache) {
	        $id = Fn::concat_ws('=>#', $this->pageId, $this->areaId, $this->id);
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
	    $namespace = Website::getInstance()->config->controller->defaultNamespace;
	    $controller_classname = Fn::create_classname($this->controller, $namespace);
	    $controller = new $controller_classname($this);
	    ob_start(); // буферизация
	    $controller->handle();
	    return ob_get_clean();
	}
}