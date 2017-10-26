<?php
if (!defined('IN_BOOT')) {
    exit('Access Denied');
}

class DbMessageCenter_KuaidiOtherAppList extends Model
{
    const DB_PREFIX = 'message_center.kuaidi_otherAppList';
    const REDIS_KEY = 'mc:kdoal:';

    const STATUS_OPEN = 1;
    const STATUS_CLOSE = 2;

    public static $status = [
        self::STATUS_OPEN => '开启',
        self::STATUS_CLOSE => '关闭',
    ];

    public static $typeLits = [
        1 => 'KuaiDi100'
    ];


    public function __construct()
    {
        $this->setTableName(self::DB_PREFIX);
        parent::__construct();
    }

    /**
     * 随机获取一个有效的三方快递配置
     * @return array
     */
    public function findRandAppOfCache()
    {
        $key = self::REDIS_KEY . 'randApp';
        $result = $this->proxyModelSearchWithRedis($key, [$this, 'findAll'], [
            [
                'status' => self::STATUS_OPEN
            ]
        ]);
        $app = [];
        if (!empty($result)) {
            $app = $result[intval(mt_rand(0, count($result)))];
        }
        return $app;
    }

    public static function getClassName($type = 1)
    {
        return self::$typeLits[$type];
    }
}