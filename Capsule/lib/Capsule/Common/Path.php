<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 19.11.2013 0:55:07 YEKT 2013                                             |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Common;

/**
 * Path.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Path
{
    /**
     * @var string
     */
    protected $path;
    
    /**
     * @param string
     * @return self
     */
    public function __construct() {
        $args = func_get_args();
        $parts = array();
        foreach ($args as $arg) {
            $tmp = $this->prepare($arg);
            if ($tmp) {
                $parts[] = $tmp;
            }
        }
        $this->path = $this->prepare(join('/', $parts));
    }
    
    /**
     * Prepare part
     * 
     * @param string $part
     * @return string
     */
    private function prepare($part) {
        return rtrim(preg_replace('|/{2,}|', '/', str_replace('\\', '/', trim($part))), '/');
    }
    
    /**
     * Implicit conversion to a string
     * 
     * @param void
     * @return string
     */
    public function toString() {
        return $this->path;
    }
    
    /**
     * Explicit conversion to a string
     *
     * @param void
     * @return string
     */
    public function __toString() {
        return $this->toString();
    }
    
    /**
     * @see global_functions
     */
    public function normalize() {
        $this->path = normalize_path($this->path);
    }
    
    public function absolutize() {
        $this->path = absolute_path($this->path);
    }
}
