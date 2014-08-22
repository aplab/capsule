<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 14.06.2014 8:15:47 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\App\Ajax\Controller;

use Capsule\App\Ajax\Controller\Controller;
use Capsule\Superglobals\Request;
use Capsule\User\Auth;
use Capsule\Plugin\Storage\Storage as s;
use Capsule\App\Cms\Cms;
use Capsule\Common\Path;
use Capsule\Capsule;
use Capsule\Common\TplVar;
/**
 * Storage.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Storage extends Controller
{
    protected function storageOverview() {
        if (!Auth::getInstance()->currentUser) return;
        $r = Request::getInstance();
        $path = $r->gets('path');
        $instance_name = $r->gets('instance_name');
        $storage_id = $this->getStorageIdByPath($path);
        if (is_null($storage_id)) {
            $this->storageList($instance_name);
            return;
        }
        $this->storageContents($path, $instance_name);
    }
    
    /**
     * @param string $path
     * @return s
     */
    protected function getStorageIdByPath($path) {
        $parts = explode('/', $path);
        $part = array_shift($parts);
        if (isset(s::config()->$part)) {
            return $part;
        }
        return null;
    }
    
    /**
     * @param string $instance_name
     * @return void
     */
    protected function storageList($instance_name) {
        TplVar::getInstance()->instance_name = $instance_name;
        $path = new Path(array(
            Capsule::getInstance()->systemRoot, 
            Cms::getInstance()->config->templates, 
            '/ajax/storage_list.php')
        );
        include $path;
    }
    
    /**
     * @param string $instance_name
     * @return void
     */
    protected function storageContents($path, $instance_name) {
        $parts = explode('/', $path);
        $part = array_shift($parts);
        $storage = s::getInstance($part);
        TplVar::getInstance()->list = $storage->readDir($parts);
        TplVar::getInstance()->instance_name = $instance_name;
        TplVar::getInstance()->storage = $storage;
        TplVar::getInstance()->path = $storage->driver->path();
        \Capsule\Tools\Tools::dump(TplVar::getInstance()->path);
        include new Path(array(
            Capsule::getInstance()->systemRoot,
            Cms::getInstance()->config->templates,
            '/ajax/storage_contents.php')
        );
    }
}