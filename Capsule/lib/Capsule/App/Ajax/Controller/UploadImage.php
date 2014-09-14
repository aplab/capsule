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
    protected function uploadSingleImage() {
        $path = new Path(array(
            Capsule::getInstance()->systemRoot,
            Cms::getInstance()->config->templates,
            '/ajax/' . __FUNCTION__ . '.php')
        );
        include $path;
    }
    
    private function _uploadSingleImage() {
        $ret = new ReturnValue();
        $ret->error = null;
        if (!isset($_FILES['file'])) {
            $ret->error = I18n::_('no file specified');
            return $ret;
        }
        $file = $_FILES['file'];
        $keys = array(
            'name',
            'type',
            'tmp_name',
            'error',
            'size'
        );
        foreach ($keys as $key) {
            if (!isset($file[$key])) {
                $ret->error = I18n::_('no file specified');
                return $ret;
            }
            if (!is_scalar($file[$key])) {
                $ret->error = I18n::_('no file specified');
                return $ret;
            }
        }
        extract($file);
        if ($error) {
            $ret->error = I18n::_(Msg::msg($error));
            return $ret;
        }
        if (!is_uploaded_file($tmp_name)) {
            if (!is_scalar($file[$key])) {
                $ret->error = I18n::_('unablo to upload file');
                return $ret;
            }
        }
        try {
            $tmp = s::getInstance()->addFile($tmp_name, pathinfo($name, PATHINFO_EXTENSION));
            $ret->pathname = $tmp['pathname'];
            $ret->exists = $tmp['exists'];
            $ret->url = $tmp['url'];
            try {
                $tmp = new ImageInfo($ret->pathname);
                $ret->isImage = true;
                foreach ($tmp->toArray() as $k => $v) {
                    $ret->$k = $v;
                }
            } catch (\Exception $e) {
                $ret->isImage = false;
            }
        } catch (\Exception $e) {
            $ret->error = I18n::_($e->getMessage());
        }
        return $ret;
    }
}