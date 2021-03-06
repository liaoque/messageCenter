<?php
if (!defined('IN_BOOT')) {
    exit('Access Denied');
}

class DbMessageCenter_KuaidiData extends Model
{
    const DB_PREFIX = 'message_center.kuaidi_data';
    const REDIS_KEY = 'mc:kdd:';


    public function __construct()
    {
        $this->setTableName(self::DB_PREFIX);
        parent::__construct();
    }

    public function saveById($data, $id = null)
    {
        $data['data'] = str_replace('\"', '\\\"', $data['data']);
        if ($this->existById($id)) {
            unset($data['id']);
            $result = empty($result) ? false : $this->update($data, array($this->getpkId() => $id));
        } else {
            $result = $this->insert($data);
        }
        return $result;
    }

    public function getInfoByIdOfCache($id)
    {
        $key = self::getCacheKey($id);
        $result = $this->proxyModelSearchWithFileCahe($key, [
            $this, 'find'
        ], [
            ['id' => $id]
        ], 'data');
        if (!empty($result)) {
            $result['id'] = $id;
        }
        return $result;
    }

    public static function getCacheKey($id)
    {
        return self::REDIS_KEY . 'id:' . $id;
    }

    public function clearCache($id)
    {
        Cache_File::getInstance()->del(self::getCacheKey($id));
    }

    public function insert($data = array())
    {
        $result = parent::insert($data); // TODO: Change the autogenerated stub
        if ($result) {
            self::clearCache($this->lastInsertId());
        }
        return $result;
    }

    public function update($data, $condition = null)
    {
        $result = parent::update($data, $condition); // TODO: Change the autogenerated stub
        if ($result && ($id = self::getWhereConditionValue($condition, 'id'))) {
            self::clearCache($id);
        }
        return $result;
    }

}