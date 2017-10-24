<?php
if (!defined('IN_BOOT')) {
    exit('Access Denied');
}

class DbMessageCenter_AppTemplate extends Model
{
    const DB_PREFIX = 'message_center.app_template';
    const REDIS_KEY = 'mc:app_template:';

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
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `appId` int(11) NOT NULL COMMENT '应用APPid',
                  `template_id` int(11) NOT NULL COMMENT '模板ID',
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='App模板关联表'";
        return $this->getDb()->execute($sql);
    }
}