<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/25
 * Time: 13:49
 */
class DbMessageCenter_AdminUser extends Model
{

    const DB_PREFIX = 'message_center.admin_user';
    static $ADMIN_TYPE = [
        1 => '超级管理员',
        2 => '高级管理员',
        3 => '普通管理员',
    ];

    public function __construct()
    {
        // TODO: Implement __destruct() method.

        $this->setTableName(self::DB_PREFIX);
        parent::__construct();
    }


    public function findAllManage()
    {
        return parent::findAll(null, 'id, username, pwd, qq,tel,creat_time,type');
    }

    public function delete($id)
    {
        $sql = "delete from " . self::DB_PREFIX . " where id={$id}";
        return $GLOBALS['mysql']->execute($sql);

    }

    public function getTemplate()
    {
        return DbWeb_AdminTemplate::getInstance()->findAll(null, '*', true);
    }

    public function searchFilter($params)
    {
        $userName = $params['username'];
        unset($params['username']);
        $search = parent::searchFilter($params);
        if ($userName) {
            $userData = $this->find(array('username' => $userName), 'id');
            if (!empty($userData)) {
                $search['id'] = $userData['id'];
            }
        }
        return $search;
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
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `username` varchar(100) DEFAULT NULL,
                  `pwd` varchar(100) DEFAULT NULL,
                  `qq` varchar(30) DEFAULT NULL COMMENT 'QQ',
                  `tel` varchar(30) DEFAULT NULL,
                  `creat_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  `type` tinyint(4) DEFAULT NULL COMMENT '管理员类型',
                 PRIMARY KEY (`id`)
            )
            ENGINE=InnoDB
            DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
            COMMENT='管理员表'
            ";
        return $this->getDb()->execute($sql);
    }

}