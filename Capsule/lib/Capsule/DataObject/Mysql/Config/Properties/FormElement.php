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

namespace Capsule\DataObject\Mysql\Config\Properties;

use Capsule\DataObject\Mysql\Config\AbstractConfig;
use Capsule\Exception;


/**
 * Column.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 * @property int $type Тип элемента
 * @property int $order Порядок элемента в форме
 * @property string $tab Название вкладки
 */
class FormElement extends AbstractConfig
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
     * Explicit conversion to string
     * @return string
     * @throws Exception
     * @internal param $void
     */
    public function toString()
    {
        throw new Exception('Cannot be convert to string');
    }

    /**
     * Обработка установки значения свойства.
     *
     * @param string $name
     * @param mixed $value
     * @throws Exception
     * @return void
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
        return $this;
    }

    /**
     * getter
     * (non-PHPdoc)
     * @see \Capsule\DataModel\Config\AbstractConfig::__get()
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return array_key_exists($name, $this->data) ? $this->data[$name] : null;
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
                    'type' => 'Rtext',
                    'tab' => 'General'
                );
            case 'smallint' :
                return array(
                    'type' => 'Rtext',
                    'tab' => 'General'
                );
            case 'mediumint' :
                return array(
                    'type' => 'Rtext',
                    'tab' => 'General'
                );
            case 'int' :
                return array(
                    'type' => 'Rtext',
                    'tab' => 'General'
                );
            case 'integer' :
                return array(
                    'type' => 'Rtext',
                    'tab' => 'General'
                );
            case 'bigint' :
                return array(
                    'type' => 'Rtext',
                    'tab' => 'General'
                );
            case 'char' :
                return array(
                    'type' => 'Text',
                    'tab' => 'General'
                );
            case 'varchar' :
                return array(
                    'type' => 'Text',
                    'tab' => 'General'
                );
            case 'tinytext' :
                return array(
                    'type' => 'Textarea',
                    'tab' => 'General'
                );
            case 'text' :
                return array(
                    'type' => 'Textarea',
                    'tab' => 'General'
                );
            case 'mediumtext' :
                return array(
                    'type' => 'Textarea',
                    'tab' => 'General'
                );
            case 'longtext' :
                return array(
                    'type' => 'Textarea',
                    'tab' => 'General'
                );
            default :
                throw new Exception('Unsupported data type');
        }
    }
}