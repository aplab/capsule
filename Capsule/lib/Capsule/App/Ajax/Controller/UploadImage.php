<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
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
use Capsule\Plugin\Storage\UploadImage as s;
use Capsule\App\Cms\Cms;
use Capsule\Common\Path;
use Capsule\Capsule;
use Capsule\DataStruct\ReturnValue;
use Capsule\I18n\I18n;
use Capsule\File\Upload\Msg;
use Capsule\File\Image\ImageInfo;

/**
 * UploadImage.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class UploadImage extends Controller
{
    protected $errorMessage;
    
    protected function uploadSingleImage() {
        $path = new Path(array(
            Capsule::getInstance()->systemRoot,
            Cms::getInstance()->config->templates,
            '/ajax/' . __FUNCTION__ . '.php')
        );
        include $path;
    }
    
    private function uploadSingleImageHandler() {
        $ret = new ReturnValue();
        $ret->error = null;
        
        return $ret;
    }
}