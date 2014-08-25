<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 16.03.2014 21:00:56 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\I18n;

use Capsule\Core\Singleton;
use Capsule\Capsule;
use Capsule\Common\Path;
use Capsule\Core\Fn;
use Capsule\DataStruct\Loader;
use Capsule\DataStruct\Exception;
use Capsule\Common\String;
/**
 * I18n.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class I18n extends Singleton
{
    /**
     * lang definition
     *
     * @var string
     */
    const   RU = 'ru',
            EN = 'en';
    
    /**
     * @var string
     */
    const EXTENSION = '.json';
    
    /**
     * Настройки перевода делать до создания экземпляра
     *
     * @var string
     */
    public static $lang = self::RU;

    /**
     * Internal data
     *
     * @var array
     */
    private $data = array();
    
    /**
     * Protected constructor
     *
     * @param void
     * @return self
     */
    protected function __construct() {
        $path = new Path(Capsule::getInstance()->etc, Fn::get_namespace($this));
        if (!is_scalar(self::$lang)) {
            return;
        }
        $path = new Path($path, self::$lang . self::EXTENSION);
        if (!file_exists($path)) {
            return;
        }
        $loader = new Loader;
        try {
        	$this->data = array_change_key_case($loader->loadJson($path), CASE_LOWER);
        } catch (Exception $e) {
            $this->data = array();
        }
    }
    
    /**
     * Translate
     *
     * @param string $text
     * @return string
     */
    public function __invoke($text) {
        $text = trim($text);
        $u = false;
        if (String::ucfirst($text) === $text) {
            $u = true;
        }
        $tmp = strtolower($text);
        return array_key_exists($tmp, $this->data) ? ($u ? String::ucfirst($this->data[$tmp]) : $this->data[$tmp]) : $text;
    }
    
    /**
     * Translate
     *
     * @param string $text
     * @return string
     */
    public static function t($text) {
        $t = self::getInstance();
        return $t($text);
    }
    
    /**
     * Alias of translate
     *
     * @param string $text
     * @return string
     */
    public static function _($text) {
        $t = self::getInstance();
        return $t($text);
    }
}
