<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 30.06.2013 0:52:27 YEKT 2013                                             |
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

use Capsule\Core\Singleton;
use Capsule\Json\Error;
use Capsule\Capsule;
use Capsule\Common\Path;

/**
 * DataStructSingleImmutable.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
abstract class SingleImmutable extends Singleton
{
    /**
     * internal data
     *
     * @var array
     */
    protected $data;

    /**
     * Read data from file
     * @return array
     * @throws Exception
     * @param void
     */
    protected function loadData() {
        $class = get_class($this);
        $path = new Path(Capsule::getInstance()->{Capsule::DIR_CFG}, $class . '.json');
        if (!file_exists($path)) {
            $msg = 'File "' . $path . '" not found';
            throw new Exception($msg);
        }
        $json = file_get_contents($path);
        if (!$json) {
            $msg = 'Unable to read file "' . $path . '"';
            throw new Exception($msg);
        }
        $data = json_decode($json, true, 512, JSON_BIGINT_AS_STRING);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new Exception(Error::getLastError());
        }
        if (!is_array($data)) {
            $data = array();
        }
        $this->data = $data;
    }

    /**
     * Возвращает значение свойства или значение по умолчанию
     *
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    public function get($name, $default = null) {
        if (!is_array($this->data)) {
            $this->loadData();
        }
        if (array_key_exists($name, $this->data)) {
            return $this->$name;
        }
        return $default;
    }

    /**
     * Возвращает значение свойства.
     *
     * @param $name
     * @throws Exception
     * @return mixed
     */
    public function __get($name) {
        if (!is_array($this->data)) {
            $this->loadData();
        }
        if (array_key_exists($name, $this->data)) {
            if (is_array($this->data[$name])) {
                $o = new static;
                $o->data = $this->data[$name];
                $this->data[$name] = $o;
            }
            return $this->data[$name];
        }
        $msg = 'Unknown property: ' . get_class($this) . '::$' . $name;
        throw new Exception($msg);
    }

    /**
     * Обработка установки значения свойства.
     *
     * @param string $name
     * @param mixed $value
     * @throws Exception
     */
    public function __set($name, $value) {
        if (array_key_exists($name, $this->data)) {
            $msg = 'Cannot set readonly property: ';
        } else {
            $msg = 'Unknown property: ';
        }
        $msg.= get_class($this) . '::$' . $name;
        throw new Exception($msg);
    }

    /**
     * isset() overloading
     *
     * @param  string $name
     * @return boolean
     */
    public function __isset($name) {
        return array_key_exists($name, $this->data);
    }

    /**
     * unset() overloading
     *
     * @param  string $name
     * @return void
     * @throws Exception
     */
    public function __unset($name) {
        if (array_key_exists($name, $this->data)) {
            $msg = 'Cannot unset readonly property: ';
        } else {
            $msg = 'Unknown property: ';
        }
        $msg.= get_class($this) . '::$' . $name;
        throw new Exception($msg);
    }
}