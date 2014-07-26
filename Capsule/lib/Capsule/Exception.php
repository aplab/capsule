<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 20.05.2013 23:22:21 YEKT 2013                                             |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule;

/**
 * Exception.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Exception extends \Exception
{
    /**
     * Предыдущее исключение
     *
     * @var null|Exception
     */
    private $_previous = null;

    /**
     * Конструктор
     *
     * @param  string $msg
     * @param  int $code
     * @param  Exception $previous
     */
    public function __construct($msg = '', $code = 0, Exception $previous = null) {
        if (version_compare(PHP_VERSION, '5.3.0', '<')) {
            parent::__construct($msg, (int) $code);
            $this->_previous = $previous;
        } else {
            parent::__construct($msg, (int) $code, $previous);
        }
    }

    /**
     * Overloading
     *
     * For PHP < 5.3.0, provides access to the getPrevious() method.
     *
     * @param  string $method
     * @param  array $args
     * @return mixed
     */
    public function __call($method, array $args) {
        if ('getprevious' == strtolower($method)) {
            return $this->_getPrevious();
        }
        return null;
    }

    /**
     * Неявное преобразование в строку
     *
     * @param void
     * @return string
     */
    public function __toString() {
        if (version_compare(PHP_VERSION, '5.3.0', '<')) {
            if (null !== ($e = $this->getPrevious())) {
                return $e->__toString() . "\n\nNext " . parent::__toString();
            }
        }
        return parent::__toString();
    }

    /**
     * Возвращает предыдущее исключение
     *
     * @param void
     * @return Exception|null
     */
    protected function _getPrevious() {
        return $this->_previous;
    }
}