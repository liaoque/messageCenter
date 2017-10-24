<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/9
 * Time: 17:23
 */
class DbMessageCenter_PayOtherList extends Model
{
    const DB_PREFIX = 'message_center.pay_otherList';
    const REDIS_KEY = 'mc:pol:';


    public function __construct()
    {
        $this->setTableName(self::DB_PREFIX);
        parent::__construct();
    }

    public static function getInfoByIdKey($id)
    {
        return self::REDIS_KEY . 'id:' . $id;
    }

    public function getInfoByIdOfCache($id)
    {
        $key = self::getInfoByIdKey($id);
        $result = $this->proxyModelSearchWithFileCahe($key, [
            $this, 'find'
        ], [
            [
                'id' => $id
            ],
            'name,title,partner,partnerName,partnerKey,partnerSecret,partnerAppId,notifyUrl,returnUrl,publicRSA2,privateRSA2,driveType'
        ]);
        if (!empty($result)) {
            $result['id'] = $id;
        }
        return $result;
    }




}