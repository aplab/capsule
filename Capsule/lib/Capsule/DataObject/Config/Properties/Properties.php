<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 14.12.2013 21:47:05 YEKT 2013                                              |
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

/**
 * Properties.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Properties extends AbstractConfig
{
    /**
     * Special property
     *
     * @var string
     */
    const PROPERTY_NAME = 'name';

    /**
     * Constructor
     *
     * @param array $data
     * @throws \Exception
     */
    public function __construct(array $data)
    {
        foreach ($data as $property_name => $property_data) {
            if (array_key_exists(self::PROPERTY_NAME, $property_data)) {
                $msg = 'Cannot redeclare automatically generated property: property::' . self::PROPERTY_NAME
                    . '. Check configuration. Remove property::' . self::PROPERTY_NAME . ' definition from configuration.';
                throw new \Exception($msg);
            }
            // Автоматически устанавливаем объекту свойство name
            $property_data = array_replace(array(self::PROPERTY_NAME => $property_name), $property_data);
            $this->data[$property_name] = new Property($property_data);
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
        return __CLASS__;
    }

    /**
     * Внедрить свойство, которого нет в конфиге, динамически.
     *
     * @param Property $property
     * @return boolean
     */
    public function inject(Property $property)
    {
        $name = $property->name;
        if (array_key_exists($name, $this->data)) {
            return false;
        }
        $this->data[$name] = $property;
        return true;
    }
}