<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.5.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2016                                                   |
// +---------------------------------------------------------------------------+
// | 11.03.2016 11:41:22 YEKT 2016                                             |
// |                                                                           |
// +---------------------------------------------------------------------------+
// | Author: Kenik <kenik2006@gmail.com>                                       |
// +---------------------------------------------------------------------------+
//
/**
 * @package Capsule
 */

namespace Cp\CpUser;

use Capsule\Common\String;
use Capsule\Exception;
use Capsule\Db\Db;
use Capsule\Capsule;
use Capsule\Module\Module;
/**
 * User.php
 *
 * @package Capsule
 * @author kenik <kenik2006@gmail.com>
 *
 * @property 
 */
class User extends Module
{
    /**
     * Минимальная длина пароля.
     *
     * @var int
     */
    const PASSWORD_MIN_LENGTH = 6;

    /**
     * Весовой параметр из двух цифр является двоичным логарифмом счетчика
     * итераций низлежащего хэширующего алгоритма, основанного на Blowfish, и
     * должен быть в диапазоне 04-31
     *
     * @var numeric string 04-31
     */
    const PASSWORD_COST = '09';

    protected function setPassword($value, $name) {
        if (!$value && array_key_exists($name, $this->data) &&
                $this->data[$name]) {
            // I do not want to change the password
            return $this;
        }
        $str = './' . join(array_merge(range('a','z'), range('A','Z'), range(0, 9)));
        $salt = substr(str_shuffle($str), 22);
        $pass = $value;
        if (self::PASSWORD_MIN_LENGTH > String::length($pass)) {
            $msg = 'Wrong password length';
            throw new Exception($msg);
        }
        $hash = crypt($pass, '$2a$' . self::PASSWORD_COST . '$' . $salt . '$');
        if (strlen($hash) < strlen($salt)) {
            $msg = 'Crypt error';
            throw new Exception($msg);
        }
        $this->data[$name] = $hash;
        return $this;
    }

    /**
     * Проверка пароля.
     *
     * @param unknown $password
     * @return boolean
     */
    public function password($password) {
        $hash = $this->password;
        return crypt(strval($password), $hash) === $hash;
    }

    /**
     * @param string $login
     * @return self
     */
    public static function getElementByLogin($login) {
        $db = Db::getInstance();
        $table = self::config()->table->name;
        $sql = 'SELECT * FROM `' . $table . '`
                WHERE `login` = ' . $db->qt($login);
        $objects = self::populate($db->query($sql));
        // array_shift returns NULL if array is empty
        return array_shift($objects);
    }

    /**
     * @param string $email
     * @return self
     */
    public static function getElementByEmail($email) {
        $db = Db::getInstance();
        $table = self::config()->table->name;
        $sql = 'SELECT * FROM `' . $table . '`
                WHERE `email` = ' . $db->qt($email);
        $objects = self::populate($db->query($sql));
        // array_shift returns NULL if array is empty
        return array_shift($objects);
    }

    /**
     * @param string $nick
     * @return array
     */
    public static function getElementByNickname($nick) {
        $db = Db::getInstance();
        $table = self::config()->table->name;
        $sql = 'SELECT * FROM `' . $table . '`
                WHERE `nickname` = ' . $db->qt($nick);
        $objects = self::populate($db->query($sql));
        // array_shift returns NULL if array is empty
        return array_shift($objects);
    }
}