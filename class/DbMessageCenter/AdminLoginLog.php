<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/8
 * Time: 12:45
 */
class DbMessageCenter_AdminLoginLog extends Model
{
    const DB_PREFIX = 'message_center.admin_login_log';

    public function __construct()
    {
        // TODO: Implement __destruct() method.

        $this->setTableName(self::DB_PREFIX);
        parent::__construct();
    }


    public function createTableOnly()
    {
        $key = self::DB_PREFIX . 'lock:' . md5($this->getTableName());
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
                    `id`  int(11) NOT NULL AUTO_INCREMENT ,
                    `uid`  int(11) NOT NULL DEFAULT 0 ,
                    `ip`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
                    `info`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
                    `login_time`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
                    PRIMARY KEY (`id`)
           )
            ENGINE=InnoDB
            DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
            COMMENT='登录日志'
            ";
        return $this->getDb()->execute($sql);
    }
}