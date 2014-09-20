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
use Capsule\I18n\I18n;
use Capsule\File\Upload\Msg;
use Capsule\Plugin\Storage\Storage;

/**
 * UploadImage.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class UploadImage extends Controller
{
    protected $result;
    
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
        $this->result = new ReturnValue();
        $this->result->error = null;
        $this->retrieveImage();
        return $this->result;
    }
    
    /**
     * Возвращает изображение
     * 
     * @param void
     * @return resource
     */
    private function retrieveImage() {
        $image = $this->getPastedImage();
        if (!is_array($image)) {
            if ($this->result->error) {
                return false;
            }
            $image = $this->getUploadedImage();
            if (!is_array($image)) {
                if (!$this->result->error) {
                    $this->result->error = I18n::_('Unable to upload file');
                }
                return false;
            }
        }
        $image = $this->resize($image);
        $image = $this->crop($image);
        $tmp_handle = tmpfile();
        $meta = stream_get_meta_data($tmp_handle);
        $path = $meta['uri'];
        switch ($image['extension']) {
            case 'jpg':
                $result = imagejpeg($image['image'], $path);
                break;
            case 'gif':
                $result = imagegif($image['image'], $path);
                break;
            default:
                $result = imagepng($image['image'], $path);
                break;
        }
        if (!$result) {
            $this->result->error = I18n::_('Unable to save image');
            return false;
        }
        try {
            $result = Storage::getInstance()->addFile($path, $image['extension']);
        } catch (\Exception $e) {
            $this->result->error = I18n::_('Unable to save file');
            return false;
        }
        $image = array_replace($image, $result); 
        imagedestroy($image['image']);
        unset($image['image']);
        $this->result->image = $image;
    }
    
    /**
     * Возвращает изображение, вставленное из буфера обмена в строку.
     * 
     *  @param void
     *  @return array|false
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
            $this->result->error = I18n::_('Unable to decode image string');
            return false;
        }
        /**
         * Сначала создаем изображение поддерживаеиого типа а потом уже
         * выполняем getimagesize.
         * см. http://habrahabr.ru/post/224351/
         */
        $image = imagecreatefromstring($image_string);
        if (!is_resource($image)) {
            $this->result->error = I18n::_('Unable to load image');
            return false;
        }
        $img_info = getimagesizefromstring($image_string);
        if (false === $img_info) {
            $this->result->error = I18n::_('Unable to read image');
            return false;
        }
        $width = imagesx($image);
        $height = imagesy($image);
        if ($width !== $img_info[0]) {
            $this->result->error = I18n::_('Image is corrupted');
            return false;
        }
        if ($height !== $img_info[1]) {
            $this->result->error = I18n::_('Image is corrupted');
            return false;
        }
        if (!$width) {
            $this->result->error = I18n::_('Image data is empty');
            return false;
        }
        if (!$height) {
            $this->result->error = I18n::_('Image data is empty');
            return false;
        }
        if (image_type_to_mime_type(IMAGETYPE_PNG) !== $img_info['mime']) {
            $this->result->error = I18n::_('Image type unsupported error');
            return false;
        }
        return array(
            'image' => $image,
            'width' => $width,
            'height' => $height,
            'mime' => $img_info['mime'],
            'extension' => 'png',
            'name' => 'clipboard.png'
        );
    }
    
    /**
     * Возвращает изображение, загруженное из файла.
     *
     *  @param void
     *  @return array|false
     */
    private function getUploadedImage() {
        if (!isset($_FILES['file'])) {
            return false;
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
                $this->result->error = I18n::_('No file specified');
                return false;
            }
            if (!is_scalar($file[$key])) {
                $this->result->error = I18n::_('No file specified');
                return false;
            }
        }
        $name = $file['name'];
        $type = $file['type'];
        $tmp_name = $file['tmp_name'];
        $error = $file['error'];
        $size = $file['size'];
        if ($error) {
            $this->result->error = I18n::_(Msg::msg($error));
            return false;
        }
        if (!is_uploaded_file($tmp_name)) {
            $ret->error = I18n::_('Unable to upload file');
            return false;
        }
        $extension = pathinfo($name, PATHINFO_EXTENSION);
        if (!$extension) {
            $ret->error = I18n::_('File without extension');
            return false;
        }
        $extension = strtolower($extension);
        $type_to_extension = array(
            'jpg'  => 'jpg',
            'jpeg' => 'jpg',
            'png'  => 'png',
            'gif'  => 'gif',
        );
        if (!in_array($extension, $type_to_extension)) {
            $ret->error = I18n::_('Unsupported extension');
            return false;
        }
        $image = null;
        /**
         * Сначала создаем изображение поддерживаеиого типа а потом уже
         * выполняем getimagesize.
         * см. http://habrahabr.ru/post/224351/
         */
        switch ($extension) {
            case 'jpg':
                $image = @imagecreatefromjpeg($tmp_name); 
                break;
            case 'png':
                $image = @imagecreatefrompng($tmp_name);
                break;
            case 'gif':
                $image = @imagecreatefromgif($tmp_name);
                break;
            default:
                $ret->error = I18n::_('Unsupported extension');
                return false;
                break;
        }
        if (!is_resource($image)) {
            $this->result->error = I18n::_('Unable to load image');
            return false;
        }
        $img_info = getimagesize($tmp_name);
        if (false === $img_info) {
            $this->result->error = I18n::_('Unable to read image');
            return false;
        }
        $width = imagesx($image);
        $height = imagesy($image);
        if ($width !== $img_info[0]) {
            $this->result->error = I18n::_('Image is corrupted');
            return false;
        }
        if ($height !== $img_info[1]) {
            $this->result->error = I18n::_('Image is corrupted');
            return false;
        }
        if (!$width) {
            $this->result->error = I18n::_('Image data is empty');
            return false;
        }
        if (!$height) {
            $this->result->error = I18n::_('Image data is empty');
            return false;
        }
        if (image_type_to_mime_type(IMAGETYPE_PNG) !== $img_info['mime'] &&
            image_type_to_mime_type(IMAGETYPE_JPEG) !== $img_info['mime'] &&
            image_type_to_mime_type(IMAGETYPE_GIF) !== $img_info['mime']) {
            $this->result->error = I18n::_('Image type unsupported error');
            return false;
        }
        return array(
            'image' => $image,
            'width' => $width,
            'height' => $height,
            'mime' => $img_info['mime'],
            'extension' => $extension,
            'name' => $name
        );
    }
    
    /**
     * Resize image
     * 
     * @param array $image
     * @return array
     */
    private function resize(array $image) {
        $keys = array('width', 'height');
        $post = Post::getInstance();
        foreach ($keys as $key) {
            $$key = $post->$key;
            if (!is_scalar($$key)) return $image;
            if (!ctype_digit($$key)) return $image;
            $$key = intval($$key);
            if (!$$key) return $image;
        }
        $width = ${'width'};
        $height = ${'height'};
        if ($width === $image['width'] && $height === $image['height']) return $image;
        $dst_image = imagecreatetruecolor($width, $height);
        if (false === imagecopyresampled($dst_image, $image['image'], 0, 0, 0, 0, $width, $height, $image['width'], $image['height'])) {
            imagedestroy($dst_image);
            return $image;
        }
        if ($width > $image['width']) {
            imagefilter($dst_image, IMG_FILTER_GAUSSIAN_BLUR, 7);
        }
        imagedestroy($image['image']);
        $image['image'] = $dst_image;
        $image['width'] = $width;
        $image['height'] = $height;
        return $image;
    }
    
    /**
     * Crop image
     *
     * @param array $image
     * @return array
     */
    private function crop(array $image) {
        $keys = array('x1', 'y1', 'x2', 'y2');
        $post = Post::getInstance();
        foreach ($keys as $key) {
            $$key = $post->$key;
            if (!is_scalar($$key)) return $image;
            if (!ctype_digit($$key)) return $image;
            $$key = intval($$key);
            if (!$$key) return $image;
        }
        $x1 = ${'x1'};
        $y1 = ${'y1'};
        $x2 = ${'x2'};
        $y2 = ${'y2'};
        $src_width = $image['width'];
        $src_height = $image['height'];
        if ($x1 >= $src_width) return $image;
        if ($x2 >= $src_width) return $image;
        if ($y1 >= $src_height) return $image;
        if ($y2 >= $src_height) return $image;
        $dst_width = abs($x2 - $x1) + 1;
        $dst_height = abs($y2 - $y1) + 1;
        if ($x1 + $dst_width > $src_width) return $image;
        if ($y1 + $dst_height > $src_height) return $image;
        $dst_image = imagecreatetruecolor($dst_width, $dst_height);
        if (false === imagecopyresampled($dst_image, $image['image'], 0, 0, $x1, $y1, $dst_width, $dst_height, $dst_width, $dst_height)) {
            imagedestroy($dst_image);
            return $image;
        }
        imagedestroy($image['image']);
        $image['image'] = $dst_image;
        $image['width'] = $dst_width;
        $image['height'] = $dst_height;
        return $image;
    }
    
    
    
    
    
    
    
}