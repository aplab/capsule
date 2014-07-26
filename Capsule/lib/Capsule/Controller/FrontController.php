<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 30.06.2013 0:32:46 YEKT 2013                                             |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Controller;

use Capsule\Url\Path;
use Capsule\App\Cms\Cms;
use Capsule\App\Website\Website;
use Capsule\App\Ajax\Ajax;

/**
 * Controller.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class FrontController extends AbstractController implements IController
{
    /**
     * (non-PHPdoc)
     * @see \Capsule\Controller\AbstractController::handle()
     *
     * @param void
     * @return void
     */
    public function handle() {
        $data = Path::getInstance()->data;
        if ('admin' === reset($data)) {
            Cms::getInstance()->run();
        } elseif ('ajax' === reset($data)) {
            Ajax::getInstance()->run();
        } else {
            Website::getInstance()->run();
        }
    }
}