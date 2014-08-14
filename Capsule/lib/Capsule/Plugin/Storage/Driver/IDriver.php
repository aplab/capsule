<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 26.07.2014 17:43:36 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Plugin\Storage\Driver;

/**
 * IDriver.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
interface IDriver
{
    /**
     * Возвращает список симлинков в директории 
     * 
     * @param string $relative_path
     * @return array
     */
    function readDir($relative_path = null);
    
    /**
     * Удаляет симлинк на файл. Сам файл остается и может быть удален сборщиком 
     * мусора, если количество ссылок на него станет равным нулю.
     * 
     * @param string $relative_path
     * @return boolean
     */
    function dropLink($relative_path);
    
    /**
     * Добавляет файл в хранилище.
     * $source_absolute_path - место, откуда взять файл.
     * $link_relative_path - ссылка на файл, по которой он будет доступен. 
     * Расширение файла указать тут надо, потому что сам файл физически хранится 
     * без расширения.
     * 
     * @param string $source_absolute_path
     * @param string $link_relative_path
     * @return array
     */
    function addFile($source_absolute_path, $link_relative_path);
}