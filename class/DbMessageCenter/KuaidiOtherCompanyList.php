<?php
if (!defined('IN_BOOT')) {
    exit('Access Denied');
}

class DbMessageCenter_KuaidiOtherCompanyList extends Model
{
    const DB_PREFIX = 'message_center.kuaidi_otherList';
    const REDIS_KEY = 'mc:kdol:';

    public function __construct()
    {
        $this->setTableName(self::DB_PREFIX);
        parent::__construct();
    }


    public static function getInfoKey($id)
    {
        return self::REDIS_KEY . 'cLId:' . $id;
    }

    /**
     * @param $companyListId
     * @param $appId
     * @return mixed
     * [
     *  otherId,
     *  otherName,
     *  otherEnName,
     *  otherNum
     *  otherStatus
     *  otherAppId,
     *  companyListId
     * ]
     */
    public function getInfoByCompanyListIdOfCache($companyListId, $appId)
    {
        $key = self::getInfoKey($companyListId);
        $result = $this->proxyModelSearchWithFileCahe($key, [
            $this, 'find'
        ], [
            [
                'companyListId' => $companyListId,
                'otherAppId' => $appId
            ],
            'id as otherId, name as otherName, enName as otherEnName, num as otherNum, status as otherStatus'
        ]);
        if (!empty($result)) {
            $result['otherAppId'] = $appId;
            $result['companyListId'] = $companyListId;
        }
        return $result;
    }

    public function clearInfoByCompanyListId($companyListId)
    {
        $key = self::getInfoKey($companyListId);
        Cache_File::getInstance()->del($key);
    }

    public function update($data, $condition = null)
    {
        $result = parent::update($data, $condition); // TODO: Change the autogenerated stub
        if ($result && ($id = self::getWhereConditionValue($condition, 'id'))) {
            self::clearCache($id);
        }
        return $result;
    }

    public function insert($data = array())
    {
        $result = parent::insert($data); // TODO: Change the autogenerated stub
        if ($result) {
            self::clearCache($this->lastInsertId());
        }
        return $result;
    }
}