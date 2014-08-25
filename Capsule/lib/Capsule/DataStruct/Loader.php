<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 18.01.2014 21:04:21 YEKT 2014                                             |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\DataStruct;

use Capsule\Json\Error;
/**
 * Loader.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Loader
{
    /**
     * $prefilter должен принимать строку и возвращать строку valid json
     * 
     * @param string $path
     * @param \Closure $prefilter
     * @throws Exception
     * @return array
     */
    public function loadJson($path, \Closure $prefilter = null) {
        if (!file_exists($path)) {
            $msg = 'File not found: ' . $path;
            throw new Exception($msg);
        }
        $content = file_get_contents($path);
        if (false === $content) {
            $msg = 'Unable to read file';
            throw new Exception($msg);
        }
        $json = trim($content);
        if (!strlen($json)) {
            return array();
        }
        if (!is_null($prefilter)) {
            if (is_callable($prefilter)) {
                $json = $prefilter($json);
            }
        }
        $data = json_decode($json, true, 512, JSON_BIGINT_AS_STRING);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new Exception(Error::getLastError());
        }
        if (!is_array($data)) {
            return array();
        }
        return $data;
    }
}