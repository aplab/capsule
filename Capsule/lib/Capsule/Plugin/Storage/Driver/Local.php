<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 26.07.2014 18:23:14 YEKT 2014                                              |
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

use Capsule\Plugin\Storage\Driver\Driver;
use Capsule\DataStruct\Config;
use Capsule\Common\Path;
use PHP\Exceptionizer\Exceptionizer;
/**
 * Local.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Local extends Driver
{
    /**
     * Директория для файлов
     * 
     * @var string
     */
    protected $files;
    
    /**
     * Директория для симлинков
     * 
     * @var string
     */
    protected $symlinks;
    
    /**
     * @param Config $config
     * @throws \Exception
     * @return self
     */
    public function __construct(Config $config) {
        parent::__construct($config);
        $this->files = new Path($this->config->files);
        $this->symlinks = new Path($this->config->symlinks);
        if (!is_dir($this->files)) {
            $msg = 'Not found files dir: ' . $this->files;
            throw new \Exception($msg);
        }
        if (!is_dir($this->symlinks)) {
            $msg = 'Not found symlinks dir: ' . $this->symlinks;
            throw new \Exception($msg);
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see \Capsule\Plugin\Storage\Driver\IDriver::readDir()
     * @throws \UnexpectedValueException
     */
    public function readDir($relative_path = null) {
        return new \DirectoryIterator(new Path($this->symlinks, $relative_path));
    }
    
    /**
     * (non-PHPdoc)
     * @see \Capsule\Plugin\Storage\Driver\IDriver::dropLink()
     */
    public function dropLink($relative_path) {
        $path = new Path($this->symlinks, $relative_path);
        $path = realpath($path);
        \Capsule\Tools\Tools::dump($path);
        // При попытке удалить несуществующий файл выдается предупреждение
        // Warning: unlink('non existent path'): No such file or directory
        if (!file_exists($path)) return;
        // clearstatcache() не требуется, функция unlink() очистит данный кэш 
        // автоматически. http://ru2.php.net/manual/ru/function.clearstatcache.php
        unlink($path);
    }
    
    /**
     * (non-PHPdoc)
     * @see \Capsule\Plugin\Storage\Driver\IDriver::addFile()
     */
    public function addFile($source_absolute_path, $link_relative_path, $overwrite = false) {
        $e = new Exceptionizer;
        settype($link_relative_path, 'string');
        $link_relative_path = trim($link_relative_path);
        $link_relative_path = new Path($link_relative_path);
        if (!$link_relative_path->toString()) {
            $msg = 'Empty link not allowed';
            throw new \Exception($msg);
        }
        $path = new Path($source_absolute_path);
        if (!file_exists($path)) {
            $msg = 'Source file not exists: ' . $source_absolute_path;
            throw new \Exception($msg);
        }
        $hash = md5_file($path);
        $file_relative_dir = '/' . join('/', array_slice(str_split($hash, 3), 0, 3));
        $file_absolute_dir = new Path($this->files, $file_relative_dir);
        if (!is_dir($file_absolute_dir)) {
            mkdir($file_absolute_dir, 0755, true);
        }
        if (!is_dir($file_absolute_dir)) {
            $msg = 'Unable to create directory: ' . $file_absolute_dir;
            throw new \Exception($msg);
        }
        $file_relative_path = new Path($file_relative_dir, $hash);
        $file_absolute_path = new Path($this->files, $file_relative_path);
        if (!file_exists($file_absolute_path)) {
            copy($path, $file_absolute_path);
            if (!file_exists($file_absolute_path)) {
                $msg = 'Unable to copy file';
                throw new \Exception($msg);
            }
            if (!chmod($file_absolute_path, 0644)) {
                $msg = 'Unable to changes file mode';
                throw new \Exception($msg);
            }
        } else {
            if (!chmod($file_absolute_path, 0644)) {
                $msg = 'Unable to changes file mode';
                throw new \Exception($msg);
            }
        }
        $link_absolute_path = new Path($this->symlinks, $link_relative_path);
        $link_absolute_dir = dirname($link_absolute_path);
        if (!is_dir($link_absolute_dir)) {
            mkdir($link_absolute_dir, 0755, true);
        }
        if (!is_dir($link_absolute_dir)) {
            $msg = 'Unable to create directory: ' . $link_absolute_dir;
            throw new \Exception($msg);
        }
        if (file_exists($link_absolute_path)) {
            if ($overwrite) {
                unlink($link_absolute_path);
                if (file_exists($link_absolute_path)) {
                    $msg = 'Unable to delete symlink: ' . $link_absolute_path;
                }
            } else {
                $msg = 'Symlink already exists';
                throw new \Exception($msg);
            }
        }
        if (symlink($file_absolute_path, $link_absolute_path)) {
            $f = new \SplFileInfo($link_absolute_path);
            \Capsule\Tools\Tools::dump($f->isFile());
            \Capsule\Tools\Tools::dump($f->getRealPath());
            \Capsule\Tools\Tools::dump($f->getPath());
            \Capsule\Tools\Tools::dump($f->getPathname());
            return array(
            	'file' => $file_absolute_path,
                'link' => $link_absolute_path
            );
        }
        \Capsule\Tools\Tools::dump(readlink($link_absolute_path));
        if (!is_link($link_absolute_path)) {
            $msg = 'Unable to create symlink: ' . $link_absolute_path;
            throw new \Exception($msg);
        }
        return true;
    }
}