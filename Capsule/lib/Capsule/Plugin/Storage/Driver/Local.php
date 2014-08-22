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
use Capsule\Capsule;
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
     * @var Path
     */
    protected $files;
    
    /**
     * Директория для симлинков
     * 
     * @var Path
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
        $this->files->absolutize();
        $this->symlinks = new Path($this->config->symlinks);
        $this->symlinks->absolutize();
        if (!$this->files->isDir()) {
            $msg = 'Not found files dir: ' . $this->files;
            throw new \Exception($msg);
        }
        if (!$this->symlinks->isDir()) {
            $msg = 'Not found symlinks dir: ' . $this->symlinks;
            throw new \Exception($msg);
        }
    }
    
    /**
     * @param void
     * @return Path
     */
    public function path() {
        return $this->symlinks;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Capsule\Plugin\Storage\Driver\IDriver::readDir()
     * @throws \UnexpectedValueException
     */
    public function readDir($relative_path = null) {
        return new \DirectoryIterator((new Path($this->symlinks, $relative_path))->toString());
    }
    
    /**
     * (non-PHPdoc)
     * @see \Capsule\Plugin\Storage\Driver\IDriver::dropLink()
     */
    public function dropLink($relative_path) {
        $path = new Path($this->symlinks, $relative_path);
        // Убирает переходы типа '/./', '/../' и лишние символы '/' в пути
        $path->absolutize();
        // Проверяет, находится ли удаляемый симлинк в директории симлинков,
        // чтобы предотвратить удаление файлов выше чем указанная директория
        if (!($path->contain($this->symlinks))) {
            $msg = 'Attempting to delete the file is not in the directory symlinks: ' . $path;
            trigger_error($msg, E_USER_ERROR); // Fatal error
        }
        // При попытке удалить несуществующий файл выдается предупреждение
        // Warning: unlink('non existent path'): No such file or directory
        if (!$path->fileExists()) return true; // Файла нет, значит результат true
        // clearstatcache() не требуется, функция unlink() очистит данный кэш 
        // автоматически. http://ru2.php.net/manual/ru/function.clearstatcache.php
        unlink($path);
        if ($path->fileExists()) {
            // Если по каким-то причинам не удалось удалить (например недостаточно прав)
            $msg = 'Unable to delete symlink: ' . $path;
            throw new \Exception($msg);
        }
        return true;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Capsule\Plugin\Storage\Driver\IDriver::addFile()
     */
    public function addFile($source_absolute_path, $link_relative_path, $overwrite = false) {
        $e = new Exceptionizer;
        // check file exists
        $path = new Path($source_absolute_path);
        if (!$path->fileExists()) {
            $msg = 'Source file not exists: ' . $source_absolute_path;
            throw new \Exception($msg);
        }
        // check symlink path
        settype($link_relative_path, 'string');
        $link_absolute_path = new Path($this->symlinks, $link_relative_path);
        $link_absolute_path->absolutize();
        // Проверяет, будет ли находиться ли создаваемый симлинк в директории симлинков,
        // чтобы предотвратить копирование файлов выше чем указанная директория
        if (!$link_absolute_path->contain($this->symlinks)) {
            $msg = 'Attempting to put symlink not in the directory symlinks: ' . $link_absolute_path;
            trigger_error($msg, E_USER_ERROR); // Fatal error
        }
        $link_relative_path = $link_absolute_path->substract($this->symlinks);
        // calculate link absolute dir
        $link_absolute_dir = $link_absolute_path->dirname();
        if (!$link_absolute_dir->contain($this->symlinks)) {
            $msg = 'Attempting to put symlink not in the directory symlinks: ' . $link_absolute_dir;
            trigger_error($msg, E_USER_ERROR); // Fatal error
        }
        // create symlink dir
        if (!$link_absolute_dir->isDir()) {
            mkdir($link_absolute_dir->toString(), 0755, true);
        }
        if (!$link_absolute_dir->isDir()) {
            $msg = 'Unable to create link directory: ' . $link_absolute_dir;
            throw new \Exception($msg);
        }
        // calculate link filename
        $link_filename = $link_absolute_path->substract($link_absolute_dir)->toString();
        if (!$link_filename) {
            $msg = 'Empty symlink filename not allowed';
            throw new \Exception($msg);
        }
        // calculate file param
        \Capsule\Tools\Tools::dump($path->toString());
        $hash = md5_file($path->toString());
        $file_relative_dir = '/' . join('/', array_slice(str_split($hash, 3), 0, 3));
        $file_absolute_dir = new Path($this->files, $file_relative_dir);
        // check file dir
        if (!$file_absolute_dir->isDir()) {
            mkdir($file_absolute_dir->toString(), 0755, true);
        }
        if (!$file_absolute_dir->isDir()) {
            $msg = 'Unable to create directory: ' . $file_absolute_dir;
            throw new \Exception($msg);
        }
        $extension = null;
        $extension = pathinfo($link_absolute_path, PATHINFO_EXTENSION);
        if (strlen($extension)) $extension = '.' . strtolower($extension);
        $file_relative_path = new Path($file_relative_dir, $hash . $extension);
        $file_absolute_path = new Path($this->files, $file_relative_path);
        if (!$file_absolute_path->fileExists()) {
            // trying to copy file
            copy($path->toString(), $file_absolute_path->toString());
            if (!$file_absolute_path->fileExists()) {
                $msg = 'Unable to copy file';
                throw new \Exception($msg);
            }
            // trying to change file mode
            if (!chmod($file_absolute_path->toString(), 0644)) {
                $msg = 'Unable to changes file mode';
                throw new \Exception($msg);
            }
        } else {
            // trying to change existing file mode (наверное не нужно это)
            if (!chmod($file_absolute_path->toString(), 0644)) {
                $msg = 'Unable to changes file mode';
                throw new \Exception($msg);
            }
        }
        // symlink handling
        if ($link_absolute_path->fileExists()) {
            if ($overwrite) {
                // deleting
                // clearstatcache() не требуется, функция unlink() очистит данный кэш
                // автоматически. http://ru2.php.net/manual/ru/function.clearstatcache.php
                unlink($link_absolute_path->toString());
                if ($link_absolute_path->fileExists()) {
                    $msg = 'Unable to delete symlink: ' . $link_absolute_path;
                    throw new \Exception($msg);
                }
            } else {
                $msg = 'Symlink already exists: ' . $link_absolute_path;
                throw new \Exception($msg);
            }
        }
        // create symlink
        if (!symlink($file_absolute_path->toString(), $link_absolute_path->toString())) {
            $msg = 'Unable to create symlink: ' . $link_absolute_path;
            throw new \Exception($msg);
        }
        if (Capsule::getInstance()->osType === Capsule::OS_TYPE_UNIX) {
            // is_link does not work correctly in Windows
            if (!is_link($link_absolute_path->toString())) {
                $msg = 'Unable to create symlink: ' . $link_absolute_path;
                throw new \Exception($msg);
            }
        }
        $f = new \SplFileInfo($link_absolute_path->toString());
        $real_path = (new Path($f->getRealPath()))->toString();
        $pathname = (new Path($f->getPathname()))->toString();
        // excessive check
        if ($link_absolute_path->toString() !== $pathname) {
            $msg = 'Unable to create symlink: ' . $link_absolute_path;
            throw new \Exception($msg);
        }
        if ($file_absolute_path->toString() !== $real_path) {
            $msg = 'Unable to create symlink: ' . $link_absolute_path;
            throw new \Exception($msg);
        }
        return array(
        	'realpath' => $real_path,
            'symlink' => $pathname,
            'path' => $link_relative_path->toString(),
            '_' => str_replace('/', '\\', $pathname)
        );
    }
}