<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 31.05.2014 8:17:59 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\App\Website\Controller;

use Capsule\App\Website\Structure\Unit;
use Capsule\Common\Path;
use Capsule\App\Website\Website;
/**
 * UnitController.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
abstract class UnitController
{
    /**
     * Unit
     *
     * @var Unit
     */
    protected $unit;
    
    /**
     * Путь к шаблонам
     *
     * @var Path
     */
    protected $tplpath;
    
    /**
     * @param void
     * @return void
     */
    abstract public function handle();
    
    /**
     * @param Unit $unit
     * @return self
     */
    public function __construct(Unit $unit) {
        $this->unit = $unit;
        $this->tplpath = Website::getInstance()->tplpath;
    }
}