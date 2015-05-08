<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 06.06.2014 5:56:29 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Usr\Aplab\Controller\DevLog;

use Capsule\Common\Path;
use Capsule\Common\TplVar;
use App\Website\Structure\Router;
use App\Website\Website;
use App\Website\Controller\UnitController;
use Usr\Aplab\Model\DevLog;
/**
 * Common.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Common extends UnitController
{
    /**
     * Главная страница журнала
     * 
     * (non-PHPdoc)
     * @see \Capsule\App\Website\Controller\UnitController::handle()
     * 
     * @param void
     * @return void
     */
    public function handle() {
        if (Router::getInstance()->hasParameters()) {
            $this->item();
            return;
        }
        $template = new Path($this->tplpath, $this->unit->template['list']);
        TplVar::getInstance()->items = DevLog::loadIndex();
        $indexes = DevLog::possibleIndexes();
        $n = sizeof($indexes);
        if ($n) {
            array_walk($indexes, function(& $v, $k) {
            	$v = array(
            		'page' => $k + 2,
            	    'url' => '/log/index/' . $v
            	);
            });
            array_unshift($indexes, array(
            	'page' => 1,
            	'url' => null // ссылка на эту же страницу не нужна
            ));
        }
        TplVar::getInstance()->index = $indexes;
        include $template;
    }
    
    /**
     * Отдельная статья
     * 
     * @param void
     * @return void
     */
    protected function item() {
        $param = Router::getInstance()->getParameters();
        $item_id = reset($param);
        if (!ctype_digit($item_id)) {
            Website::getInstance()->error404();
        }
        $o = DevLog::id($item_id);
        if (!$o) {
            Website::getInstance()->error404();
        }
        if (!$o->active) {
            Website::getInstance()->error404();
        }
        $prev = $o->earlierItemId();
        $next = $o->laterItemId();
        $nav = array();
        if ($prev) {
            $nav[] = array(
            	'label' => 'Предыдущая',
                'url' => '/log/' . $prev . '/'                
            );
        }
        if ($next) {
            $nav[] = array(
                'label' => 'Следующая',
                'url' => '/log/' . $next . '/'
            );
        }
        $template = new Path($this->tplpath, $this->unit->template['item']);
        TplVar::getInstance()->item = $o;
        TplVar::getInstance()->nav = $nav;
        Website::getInstance()->page->title = $o->title ?: $o->name;
        include $template;
    }
}