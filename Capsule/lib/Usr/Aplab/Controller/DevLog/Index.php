<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
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
use Capsule\App\Website\Structure\Router;
use Capsule\App\Website\Website;
use Capsule\App\Website\Controller\UnitController;
use Usr\Aplab\Model\DevLog;
/**
 * Index.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Index extends UnitController
{
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
        array_walk($indexes, function(& $v, $k) use ($index) {
            $v = array(
                'page' => $k + 2,
                'url' => $v === $index ? null : '/log/index/' . $v
            );
        });
        array_unshift($indexes, array(
            'page' => 1,
            'url' => '/log/'
        ));
        TplVar::getInstance()->index = $indexes;
        include $template;
    }
}