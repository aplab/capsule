<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 24.08.2014 10:49:33 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Tools\Seo;

use Capsule\Capsule;
use Capsule\Tools\Sysinfo;
/**
 * Seo.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Seo
{
    private static $data = array();
    
    public static function nofollow($html) {
        return preg_replace_callback('/<a[^>]+/', function($matches) {
            $link = $matches[0];
            $site_link = self::baseUrl();
            if (strpos($link, 'rel') === false) {
                $link = preg_replace('%(href=\\S(?!$site_link))%i', 'rel="nofollow" $1', $link);
            } elseif (preg_match('%href=\\S(?!$site_link)%i', $link)) {
                $link = preg_replace('/rel=\\S(?!nofollow)\\S*/i', 'rel="nofollow"', $link);
            }
            return $link;
        }, $html);
    }
    
    public static function baseUrl() {
        $k = __FUNCTION__;
        if (!isset(self::$data[$k])) {
            self::$data[$k] = Sysinfo::baseUrl();
        }
        return self::$data[$k];
    }
    
    public static function absolutize($html) {
        return preg_replace_callback('/<a[^>]+/', function($matches) {
            $link = $matches[0];
            $site_link = self::baseUrl();
            if (strpos($link, 'rel') === false) {
                $link = preg_replace('%(href=\\S(?!$site_link))%i', 'rel="nofollow" $1', $link);
            } elseif (preg_match('%href=\\S(?!$site_link)%i', $link)) {
                $link = preg_replace('/rel=\\S(?!nofollow)\\S*/i', 'rel="nofollow"', $link);
            }
            return $link;
        }, $html);
    } 
    
    private static function absolutize_url($url) {
        $url = 
        $data = parse_str($url);
    }
}