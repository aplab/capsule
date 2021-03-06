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
 * Index.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Index extends UnitController
{
    /**
     * Страница журнала
     * 
     * (non-PHPdoc)
     * @see \App\Website\Controller\UnitController::handle()
     * 
     * @param void
     * @return void
     */
    public function handle() {
        $param = Router::getInstance()->getParameters();
        $index = reset($param);
        if (!ctype_digit($index)) {
            Website::getInstance()->error404();
        }
        settype($index, 'int');
        $indexes = DevLog::possibleIndexes();
        if (!in_array($index, $indexes, true)) {
            Website::getInstance()->error404();
        }
        $template = new Path($this->tplpath, $this->unit->template);
        TplVar::getInstance()->items = DevLog::loadIndex($index);
        $current_page = null;
        array_walk($indexes, function(& $v, $k) use ($index, & $current_page) {
            $page = $k + 2;
            if ($v === $index) {
                $current_page = $page;
            }
            $v = array(
                'page' => $page,
                'url' => $v === $index ? null : '/log/index/' . $v
            );
        });
        array_unshift($indexes, array(
            'page' => 1,
            'url' => '/log/'
        ));
        TplVar::getInstance()->index = $indexes;
        if ($current_page) {
            Website::getInstance()->page->title = 'Журнал разработки, страница ' . $current_page;
        }
        include $template;
    }
}