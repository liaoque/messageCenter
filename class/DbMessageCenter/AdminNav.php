<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/25
 * Time: 14:03
 */
class DbMessageCenter_AdminNav extends Model
{
    const DB_PREFIX = 'message_center.admin_nav';

    const STATUS_OPEN = 1;
    const STATUS_CLOSE = 2;
    public static  $status = [
        self::STATUS_OPEN => '开启',
        self::STATUS_CLOSE => '关闭'
    ];

    public function __construct()
    {
        $this->setTableName(self::DB_PREFIX);
        parent::__construct();
        $this->fileCache = Cache_File::getInstance();
    }


    /**
     * 根据条件寻找所有栏目，并缓存，
     * @param array $condition
     * @param null $key 如果key存在，则指定缓存的key，不存在则使用条件的md5作为key
     * @return array
     */
    public function findAllOfCacheWithYes($condition = array(), $key = null)
    {
        if (empty($condition)) {
            $condition = array('status' => 1);
        } else if (is_array($condition)) {
            $condition['status'] = 1;
        } else {
            return array();
        }

        $key = empty($key) ? md5(empty($condition) ? 'findAllOfCacheWithYes' : self::enCode($condition)) : $key;
        return $this->fileCache->proxyModelSearch($key, array($this, 'findAll'), array($condition));
    }


    public function getNavs($pid = 0)
    {
        $result = $this->findAll([
            'where' => ['pid' => $pid],
            'order' => 'id ASC'
        ]);
        foreach ($result as $k => $v) {
            $result[$k]['subMenu'] = $this->getNavs($v['id']);
        }
        return $result;
    }


    /**
     * 删除状态为1的菜单缓存
     * @param array $condition
     * @param null $key
     * @return bool
     */
    public function delFindAllOfCacheWithYes($condition = array(), $key = null)
    {
        if (empty($condition)) {
            $condition = array('status' => 1);
        } else if (is_array($condition)) {
            $condition['status'] = 1;
        } else {
            return true;
        }
        $key = empty($key) ? md5(self::enCode($condition)) : $key;
        return $this->fileCache->del($key);
    }

    /**
     * 建立缓存
     * @param $data
     * @param $root
     * @param int $pid
     * @return mixed
     */
    public function createBranch(&$data, $root, $pid = 0)
    {
        $_root = $pid == 0 ? $root : $root['subMenu'];
        foreach ($data as $key => $value) {
            if ($value['pid'] == $pid) {
                $_root[intval($value['id'])] = $value;
                unset($data[$key]);
            }
        }
        if (!empty($data)) {
            foreach ($_root as $id => $value) {
                $value['subMenu'] = array();
                $_root[$id] = $this->createBranch($data, $value, $id);
            }
        }
        ksort($_root);
        if ($pid != 0) {
            $root['subMenu'] = $_root;
        } else {
            $root = $_root;
        }
        return $root;
    }

    public function delete($id)
    {
        $sql = "delete from " . self::DB_PREFIX . " where id={$id}";
        return $GLOBALS['mysql']->execute($sql);

    }


    public function createTableOnly()
    {
        $key = self::REDIS_KEY . 'lock:' . md5($this->getTableName());
        if ($this->fileCache->exists($key)) {
            return true;
        }
        $result = $this->createTable();
        if (!empty($result)) {
            $this->fileCache->set($key, 1);
        }
        return $result;
    }

    public function createTable()
    {
        $tableName = $this->getTableName();
        $sql = "CREATE TABLE if not exists {$tableName} (
                  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '栏目Id',
                  `title` varchar(50) DEFAULT NULL COMMENT '栏目标题',
                  `pid` int(11) DEFAULT NULL COMMENT '栏目上级id',
                  `src` varchar(255) DEFAULT NULL COMMENT '路径',
                  `status` tinyint(4) DEFAULT '1' COMMENT '状态,1开启, 2关闭',
                  `icon` text COMMENT '图标',
                  `path` varchar(50) DEFAULT NULL COMMENT '路径',
                  PRIMARY KEY (`id`)
            )
            ENGINE=InnoDB
            DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
            COMMENT='菜单表'
            ";
        return $this->getDb()->execute($sql);
    }

}