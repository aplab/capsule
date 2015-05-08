<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 12.10.2014 1:43:24 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\App\Cms\Ui\Dialog;

use PHP\Exceptionizer\Exceptionizer;
use Capsule\App\Cms\Ui\Ui;
use Capsule\App\Cms\Ui\Stylesheet;
use Capsule\App\Cms\Ui\Script;
use Capsule\App\Cms\Cms;
/**
 * Dialog.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Dialog
{
    private static $instances;
    
    protected $instanceName;
    
    public function __construct(array $data = array()) {
        $e = new Exceptionizer();
        $this->instanceName = $data['instanceName'];
        settype($this->instanceName, 'string');
        if (!is_array(self::$instances)) {
            Ui::getInstance()->css->append(
                new Stylesheet(Cms::getInstance()->config->ui->dialog->css)
            );
            Ui::getInstance()->js->append(
                new Script(Cms::getInstance()->config->ui->dialog->js)
            );
            self::$instances = array();
        }
        if (isset(self::$instances[$this->instanceName])) {
            $msg = 'Instance already exists: ' . $this->instanceName;
            throw new \Exception($msg);
        }
        Ui::getInstance()->onload->append('new CapsuleUiDialog(' . json_encode($data) . ');');
    }
}