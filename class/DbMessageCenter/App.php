<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/8
 * Time: 11:31
 */
class DbMessageCenter_App extends Model
{
    const DB_PREFIX = 'message_center.app';
    const REDIS_KEY = 'mc:app:';

    const STATUS_OPEN = 1;
    const STATUS_CLOSE = 2;
    public static $status = [
        self::STATUS_OPEN => '开启',
        self::STATUS_CLOSE => '关闭',
    ];

    public function __construct()
    {
        $this->setTableName(self::DB_PREFIX);
        $this->fileCache = Cache_File::getInstance();
        parent::__construct();
        $this->createTableOnly();
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
        $sql = "CREATE TABLE IF NOT EXISTS {$tableName} (
                    `id`  int(11) NOT NULL AUTO_INCREMENT COMMENT 'APP应用ID' ,
                    `pid`  int(11) NULL DEFAULT NULL COMMENT '栏目上层id' ,
                    `path`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '应用层级路径' ,
                    `title`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'APP标题' ,
                    `status`  tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态:1》正常；2》关闭' ,
                    `create_time`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ,
                    `key`  char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'key 32位字符串' ,
                    PRIMARY KEY (`id`)
                )
                ENGINE=InnoDB
                DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
                COMMENT='APP应用表'
                AUTO_INCREMENT=8
                ROW_FORMAT=COMPACT";
        return $this->getDb()->execute($sql);
    }
}