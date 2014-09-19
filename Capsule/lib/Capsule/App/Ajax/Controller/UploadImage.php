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
use Capsule\App\Cms\Cms;
use Capsule\Common\Path;
use Capsule\Capsule;
use Capsule\DataStruct\ReturnValue;
use Capsule\Superglobals\Post;

/**
 * UploadImage.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class UploadImage extends Controller
{
    protected $errorMessage;
    
    /**
     * Загрузить одно изображение
     * 
     * @param void
     * @return void
     */
    protected function uploadSingleImage() {
        $path = new Path(array(
            Capsule::getInstance()->systemRoot,
            Cms::getInstance()->config->templates,
            '/ajax/' . __FUNCTION__ . '.php')
        );
        include $path;
    }
    
    /**
     * Обработчик загрузки изображений вызываемый из ajax ответа шаблона
     * 
     * @param void
     * @return \Capsule\DataStruct\ReturnValue
     */
    private function uploadSingleImageHandler() {
        $ret = new ReturnValue();
        $ret->error = null;
        $this->retrieveImage();
        return $ret;
    }
    
    /**
     * Возвращает изображение
     * 
     * @param void
     * @return resource
     */
    private function retrieveImage() {
        $image = $this->getPastedImage();
    }
    
    /**
     * Возвращает изображение, вставленное из буфера обмена в строку.
     * 
     *  @param void
     *  @return resource
     */
    private function getPastedImage() {
        $image_string = Post::getInstance()->get('imageString');
        if (is_null($image_string)) {
            return false;
        }
        if (!is_scalar($image_string)) {
            return false;
        }
        if (!$image_string) {
            return false;
        }
        $image_string = base64_decode($image_string);
        if (!$image_string) {
            return false;
        }
        $image = imagecreatefromstring($image_string);
        if (!is_resource($image)) {
            return false;
        }
        $img_info = getimagesizefromstring($image_string);
        $width = imagesx($image);
        $height = imagesy($image);
        \Capsule\Tools\Tools::dump($width);
        \Capsule\Tools\Tools::dump($height);
        \Capsule\Tools\Tools::dump($img_info);
        
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
}