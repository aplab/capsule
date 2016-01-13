<?php
/**
 * Created by Alexander Polyanin polyanin@gmail.com.
 * User: polyanin
 * Date: 14.01.2016
 * Time: 1:30
 */

namespace Capsule\Config;


use Capsule\Capsule;
use Capsule\Common\Path as CommonPath;

class Path
{
    const CONFIG_EXTENSION = '.json';

    /**
     * @var CommonPath
     */
    protected $path;

    /**
     * Path constructor.
     *
     * @param $class
     */
    public function __construct($class)
    {
        $r = new \ReflectionClass($class);
        $name = $r->getName() . self::CONFIG_EXTENSION;
        $this->path = new CommonPath(Capsule::getInstance()->cfg, $name);
    }

    /**
     * Implicit conversion to a string
     *
     * @param void
     * @return string
     */
    public function toString()
    {
        return $this->path->toString();
    }

    /**
     * Explicit conversion to a string
     *
     * @param void
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
}