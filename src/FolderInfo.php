<?php

namespace Tmkook;

/**
 * 类 FileInfo 提供了目录文件的排序功能。
 *
 * @link http://github.com/tmkook/folder
 *
 * @copyright (c) 2016 tmkook.
 * @license MIT
 *
 * @version $Id: FileInfo.php
 */
class FolderInfo
{
    private $lists = array();
    public function __construct(array $list)
    {
        foreach ($list as $file) {
            $this->lists[] = $this->getInfo($file);
        }
    }

    /**
     * 获取文件信息列表.
     *
     * @return array
     */
    public function get()
    {
        return $this->lists;
    }

    /**
     * 根据修改时间进行升序排序.
     *
     * @return object
     */
    public function orderByMtimeAsc()
    {
        return $this->setOrder('mtime', 'asc');
    }

    /**
     * 根据inode时间进行升序排序.
     *
     * @return object
     */
    public function orderByCtimeAsc()
    {
        return $this->setOrder('ctime', 'asc');
    }

    /**
     * 根据访问时间进行升序排序.
     *
     * @return object
     */
    public function orderByAtimeAsc()
    {
        return $this->setOrder('atime', 'asc');
    }

    /**
     * 根据访问时间进行降序排序.
     *
     * @return object
     */
    public function orderByAtimeDesc()
    {
        return $this->setOrder('atime', 'desc');
    }

    /**
     * 根据修改时间进行降序排序.
     *
     * @return object
     */
    public function orderByMtimeDesc()
    {
        return $this->setOrder('mtime', 'desc');
    }

    /**
     * 根据inode时间进行降序排序.
     *
     * @return object
     */
    public function orderByCtimeDesc()
    {
        return $this->setOrder('ctime', 'desc');
    }

    /**
     * 根据文件名进行升序排序.
     *
     * @return object
     */
    public function orderByNameAsc()
    {
        return $this->setOrder('name', 'asc');
    }

    /**
     * 根据文件名进行降序排序.
     *
     * @return object
     */
    public function orderByNameDesc()
    {
        return $this->setOrder('name', 'desc');
    }

    /**
     * 根据传入的参数进行排序.
     *
     * @param $name mtime|ctime|atime|name
     * @param $ordertype asc|desc
     *
     * @return object
     */
    public function setOrder($name, $ordertype = 'asc')
    {
        $order = $lists = array();
        foreach ($this->lists as $key => $item) {
            $order[$key] = $item[$name];
        }

        $ordertype == 'asc' ? asort($order) : arsort($order);
        foreach ($order as $key => $val) {
            $lists[] = $this->lists[$key];
        }
        $this->lists = $lists;

        return $this;
    }

    /**
     * 获取文件信息.
     *
     * @param $file 文件路径
     *
     * @return array
     */
    private function getInfo($file)
    {
        $mtime = filemtime($file);
        $ctime = filectime($file);
        $atime = fileatime($file);
        if (empty($mtime)) {
            touch($file);
            $mtime = filemtime($file);
        }
        if (empty($atime)) {
            $atime = time();
        }
        if (empty($ctime)) {
            $ctime = $atime;
        }
        if (empty($mtime)) {
            $mtime = $ctime;
        }
        $name = dirname($file);
        $name = trim(str_replace($name, '', $file), '/');
        if (is_dir($file)) {
            $title = $name;
        } elseif (is_file($file)) {
            $title = $this->getFileFirstLine($file);
        }

        return array('path' => $file, 'name' => $name, 'title' => $title, 'mtime' => $mtime, 'ctime' => $ctime, 'atime' => $atime);
    }

    /**
     * 获取文件第一行.
     *
     * @param $file 文件路径
     *
     * @return string
     */
    private function getFileFirstLine($file)
    {
        $myfile = fopen($file, 'r') or die('Unable to open file');
        $title = '';
        while (!feof($myfile)) {
            $title = fgets($myfile);
            if (!empty($title)) {
                break;
            }
        }
        if (empty($title)) {
            $title = '# Untitle';
        }
        $title = trim(str_replace('#', '', $title));
        fclose($myfile);

        return $title;
    }
}
