<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2015                                                   |
// +---------------------------------------------------------------------------+
// | 19 мая 2015 г. 1:08:20 YEKT 2015                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Ui\ObjectEditor;

use Capsule\Ui\ObjectEditor\Oe;
use Capsule\DataModel\DataModel;
use App\Cms\Cms;
/**
 * Oe2.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Oe2 extends Oe 
{
    public function __construct(DataModel $object, $instance_name) {
        parent::__construct($object, $instance_name);
        Cms::getInstance()->ui->title->prepend('жажа');
    }
}