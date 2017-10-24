<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/20
 * Time: 9:45
 *
 * 缓存目录是当前运行脚本目录.'data/fileCache';
 * 文件缓存
 *
 */
class Cache_File extends Cache_Base
{
    private $path;

    private $timeOut = 86400;

    private $fileName;


    public function __construct()
    {
        if(!defined('ROOT')){
            throw new Exception('ROOT未定义', -1);
        }
        $path = ROOT.'/data/fileCache/';
        $this->setPath($path . date('Y-m-d'));
    }

    /**
     * 删除昨天的缓存文件
     * @return bool
     */
    public static function removeYestDayFile()
    {
        //$path = '/alidata/www/data/fileCache/';
        $path = ROOT.'/data/fileCache/';
        $oldPath = $path . date('Y-m-d', strtotime('-1 day'));
        file_exists($oldPath) && deldir($oldPath);
    }


    /**
     * 设置缓存文件路径并创建
     * @param $path
     * @return mixed
     */
    public function setPath($path)
    {
        $this->path = $path;
        if ($this->path) {
            self::createFileDir($this->path);
        }
        return $this->path;
    }

    /**
     * 创建缓存文件目录
     * @param $fileName
     * @return bool
     */
    public static function createFileDir($fileName)
    {
        if (file_exists($fileName)) {
            return true;
        }
        if (self::createFileDir(dirname($fileName))) {
            mkdir($fileName, 0777);
            chmod($fileName, 0777);
        }
        return true;
    }


    /**
     * 设置文件名
     * @param $fileName
     * @return mixed
     */
    public function setFileName($fileName)
    {
        return $this->fileName = $fileName;
    }

    /**
     * 获取文件缓存路径
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * 获取文件缓存的超时时间
     * @param int $cacheName
     * @return int
     */
    public function ttl($cacheName = 86400)
    {
        return $this->timeOut;
    }

    /**
     * 获取文件的更新时间
     * @param $cacheName
     * @return int
     */
    public function uTime($cacheName)
    {
        $file = $this->getFile($cacheName);
        return filemtime($file);
    }

    /**
     * 获取文件缓存路径+文件名
     * @param null $cacheName
     * @return string
     */
    public function getFile($cacheName = null)
    {
        $fileName = trim($cacheName);
        $fileName = empty($fileName) ? $this->fileName : $fileName;
        if ($fileName) {
            $fileName = md5($fileName);
            return $this->getPath() . '/' . $fileName;
        }
        return '';
    }


    /**
     * 判断文件缓存是否存在
     * @param null $cacheName
     * @return bool
     */
    public function exists($cacheName = null)
    {
        $file = $this->getFile($cacheName);
        if ($file) {
            return file_exists($file);
        }
        return false;
    }

    /**
     * 文件更新超时为 false，未超时返回 true
     * @param null $cacheName
     * @param int $timeOut
     * @return bool
     */
    public function timeOut($cacheName = null, $timeOut = 0)
    {
        if ($this->exists($cacheName)) {
            $file = $this->getFile($cacheName);
            $time = filemtime($file);
            return time() - $time <= (empty($timeOut) ? $this->timeOut : $timeOut);
        }
        return false;
    }

    /**
     * 设置文件缓存
     * $this->set(null, 111);
     * $this->set(null, [111, 2222]);
     * $this->set('aaaa', 111);
     * $this->set('aaaa', [111, 2222]);
     * @param $v
     * @param null $cacheName
     * @return bool|int|void
     */
    public function set($cacheName, $v)
    {

        $value = is_string($v) ? $v : json_encode($v);
        $fileName = $this->getFile($cacheName);
        return file_put_contents($fileName, $value);
    }

    /**
     * 获取文件缓存
     * @param null $cacheName
     * @return string
     */
    public function get($cacheName = null)
    {
        if ($this->exists($cacheName)) {
            $fileName = $this->getFile($cacheName);
            return file_get_contents($fileName);
        }
        return '';
    }

    public function del($cacheName = null)
    {
        // TODO: Implement del() method.
        if ($this->exists($cacheName)) {
            $fileName = $this->getFile($cacheName);
            unlink($fileName);
        }
    }

    /**
     * @param $key 缓存的键名
     * @param $action 回调方法
     * @param $arges 参数
     * @param bool $timeOut 设置超时时间
     * @return array|mixed|string
     */
    public function proxyModelSearch($key, $action, $arges, $timeOut = false)
    {
        if ($timeOut) {
            $timeOut = $this->timeOut($key, $timeOut) ? false : true;
        }
		$this->del($key);
        $result = $this->get($key);

        if (empty($result) || $timeOut) {

            $result = call_user_func_array($action, $arges);
            $data = empty($result) ? -1 : $result;
            $this->set($key, $data);
        } elseif ($result == -1) {
            $result = array();
        } else {
            $result = Model::deCode($result);
        }
        return $result;
    }


}