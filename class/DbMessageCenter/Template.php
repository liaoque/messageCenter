<?php
if (!defined('IN_BOOT')) {
    exit('Access Denied');
}

class DbMessageCenter_Template extends Model
{
    const DB_PREFIX = 'message_center.template';
    const REDIS_KEY = 'mc:template:';

    const STATUS_OPEN = 1;
    const STATUS_CLOSE = 2;

    const TYPE_MAIL = 1;
    const TYPE_PHONE = 2;
    const TYPE_WX = 4;
    const TYPE_APP = 8;


    public static $status = [
        self::STATUS_OPEN => '正常',
        self::STATUS_CLOSE => '关闭',
    ];

    public static $type = [
        self::TYPE_MAIL => '邮件',
        self::TYPE_PHONE => '手机',
        self::TYPE_WX => '微信',
        self::TYPE_APP => '推送',
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
                    `id`  int(11) NOT NULL AUTO_INCREMENT COMMENT '模板ID' ,
                    `title`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '模板标题' ,
                    `content`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '模板内容' ,
                    `status`  tinyint(4) NOT NULL DEFAULT 1 COMMENT '是否正常启用：1》正常；2》关闭' ,
                    `type`  tinyint(4) NULL DEFAULT 1 COMMENT '1.邮件 2.手机' ,
                    `create_time`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间' ,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='邮件模板表'";
        return $this->getDb()->execute($sql);
    }

//    /** 获取制定模板，并讲模板内容替换为定义邮件内容
//     * @param $id   模板id
//     * @param null $content 邮件内容
//     * @return mixed|null
//     */
//    public function getTemplateById($id, $content = null)
//    {
//        $condition = array('where' => 'id=' . $id);
//        $data = $this->find($condition);
//
//        $content = str_replace('{{text}}', $content, $data['content']);
//        return $content;
//    }

//    public static function parserContent($template, $data)
//    {
//
//    }

    public static function parseTemplate($content, $type, $args = [])
    {
        $match = [];
        switch ($type) {
            case self::TYPE_WX:
                $match = self::wxTemplateParse($content);
                break;
            case self::TYPE_APP:
                $match = Model::deCode($content);
                break;
            case self::TYPE_PHONE:
                $match = self::phoneTemplateParse($content);
                break;
            case self::TYPE_MAIL:
                $match = self::mailTemplateParse($content, $args);
                break;
            default:
                break;
        }
        return $match;
    }

    public static function mailTemplateParse($content, $args)
    {
        $match = [];
        $count = preg_match_all('/\{\{(\w+)\}\}/', $content, $match);
        if (empty($count)) {
            return $content;
        }
        foreach ($match[1] as $key => $value) {
            $content = str_replace("{{{$value}}}",  $args[$value], $content);
        }
        return $content;
    }

    public static function phoneTemplateParse($content)
    {
        $match = [];
        preg_match_all('/\{\{(\w+)\}\}/', $content, $match);
        if (!empty($match[1])) {
            $match = $match[1];
        }
        return $match;
    }

    public static function wxTemplateParse($content)
    {
        $match = [];
        preg_match_all('/\{\{(\w+)\.DATA\}\}/', $content, $match);
        if (!empty($match[1])) {
            $match = $match[1];
        }
        return $match;
    }

    public static function getFindByIdOfCacheKey($templateId)
    {
        return self::REDIS_KEY . $templateId;
    }

    public function findByIdOfCache($templateId)
    {
        $key = self::getFindByIdOfCacheKey($templateId);
        $result = $this->proxyModelSearchWithFileCahe($key, [$this, 'find'], [
            [
                'id' => $templateId
            ],
            'templateNum, content, type, status'
        ]);
        return $result;
    }

    public static function validation($templateId, $type)
    {
        $result = DbMessageCenter_Template::getInstance()->findByIdOfCache($templateId);
        if ($result['status'] == DbMessageCenter_Template::STATUS_CLOSE) {
            throw new Exception('模版已被禁止使用', -500);
        }
        if ($result['type'] != $type) {
            throw new Exception('模版类型错误,请使用正确的模版', -500);
        }
        return $result;
    }


}