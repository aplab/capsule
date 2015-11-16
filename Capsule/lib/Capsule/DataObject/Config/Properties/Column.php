<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 04.12.2013 1:23:29 YEKT 2013                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\DataObject\Config\Properties;

use Capsule\DataObject\Config\AbstractConfig;
use Capsule\Exception;
use Capsule\Tools\Tools;


/**
 * Column.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 * @property int $width Ширина колонки
 * @property int $order Порядок колонки
 * @property string $type Тип колонки необязательное значение, ставится по умолчанию значение Text
 */
class Column extends AbstractConfig
{
    /**
     * Special property
     *
     * @param string
     */
    const DATA_TYPE = 'dataType';

    /**
     * @param array $data
     * @throws \Exception
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
        // Если передано свойство dataType, то ищем готовый набор для этого типа данных
        if (array_key_exists(self::DATA_TYPE, $this->data)) {
            $this->data = array_replace($this->initDataType($this->data[self::DATA_TYPE]), $this->data);
            unset($this->data[self::DATA_TYPE]);
        }
    }

    /**
     * explicit conversion to string
     *
     * @param void
     * @return string
     */
    public function toString()
    {
        return $this->width;
    }

    /**
     * Getter
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return array_key_exists($name, $this->data) ? $this->data[$name] : null;
    }

    /**
     * Setter
     *
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function __set($name, $value)
    {
        $setter = 'set' . ucfirst($name);
        if (in_array($setter, get_class_methods($this))) {
            $this->$setter($value, $name);
        } else {
            $this->data[$name] = $value;
        }
        return $this;
    }

    /**
     * Set width
     *
     * @param int $value
     * @param string $name
     * @throws \InvalidArgumentException
     * @return \Capsule\DataModel\Config\Properties\Column
     */
    protected function setWidth($value, $name)
    {
        if (!$value) {
            $this->data[$name] = 0;
            return $this;
        }
        if (ctype_digit((string)$value)) {
            $this->data[$name] = $value;
            return $this;
        }
        $msg = 'Wrong width value';
        throw new \InvalidArgumentException($msg);
    }

    /**
     * @param string $type
     * @return array
     * @throws Exception
     */
    protected function initDataType($type)
    {
        switch ($type) {
            case 'tinyint' :
                return array(
                    'width' => 60,
                    'type' => 'Rtext'
                );
            case 'smallint' :
                return array(
                    'width' => 80,
                    'type' => 'Rtext'
                );
            case 'mediumint' :
                return array(
                    'width' => 100,
                    'type' => 'Rtext'
                );
            case 'int' :
                return array(
                    'width' => 120,
                    'type' => 'Rtext'
                );
            case 'integer' :
                return array(
                    'width' => 120,
                    'type' => 'Rtext'
                );
            case 'bigint' :
                return array(
                    'width' => 180,
                    'type' => 'Rtext'
                );
            case 'char' :
                return array(
                    'width' => 200,
                    'type' => 'Text'
                );
            case 'varchar' :
                return array(
                    'width' => 200,
                    'type' => 'Text'
                );
            default :
                throw new Exception('Unsupported data type');
        }
    }
}