<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/27
 * Time: 11:09
 */
class   DbMessageCenter_LogPhone extends Model
{
    const DB_PREFIX = 'message_center.log_phone';

    const STATUS_SEND_OK = 1;
    const STATUS_SEND_ERROR = 2;
    const STATUS_SEND_ING = 3;

    public static $status = [
        self::STATUS_SEND_OK => '成功',
        self::STATUS_SEND_ERROR => '失败',
        self::STATUS_SEND_ING => '进行中'
    ];

    public function __construct()
    {
        $this->setTableName(self::DB_PREFIX);
        parent::__construct();
    }

//    public function insert($data = array())
//    {
//        $flag = parent::insert([
//            'pkId' => $data['pkId'],
//            'appId' => $data['appId'],
//            'templateId' => $data['templateId'],
//            'target' => $data['target'],
//            'status' => $data['status']
//        ]); // TODO: Change the autogenerated stub
//        if ($flag) {
//            $logId = $this->lastInsertId();
//            $flag = DbMessageCenter_LogPhoneContent::getInstance()->insert([
//                'logId' => $logId,
//                'content' => is_string($data['content']) ?  $data['content'] : Model::enCode($data['content'], trur),
//            ]);
//        }
//        return $flag;
//    }
}