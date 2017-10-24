<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/27
 * Time: 11:09
 */
class DbMessageCenter_KuaidiWaybill extends Model
{
    const DB_PREFIX = 'message_center.kuaidi_waybill';
    const REDIS_KEY = 'mc:kdw:';

    const STATUS_SUBSCRIBE_SUCCESS = 1;
    const STATUS_SUBSCRIBE_FORBIDDIN = 2;
    const STATUS_SUBSCRIBE_ING = 3;
    const STATUS_SUBSCRIBE_WAIT = 4;

    public static $status_subscribe = [
        self::STATUS_SUBSCRIBE_SUCCESS => '订阅成功',
        self::STATUS_SUBSCRIBE_FORBIDDIN => '订阅失败',
        self::STATUS_SUBSCRIBE_ING => '订阅中',
        self::STATUS_SUBSCRIBE_WAIT => '待订阅'
    ];

    public function __construct()
    {
        $this->setTableName(self::DB_PREFIX);
        parent::__construct();
    }

    /**
     * 插入唯一一条运单数据
     * @param $data
     * @return bool
     */
    public function saveFirst($data)
    {
        static $list = [];
        $waybill = $data['waybill'];
        if (in_array($waybill, $list)) {
            return true;
        }
        $info = $this->find([
            'waybill' => $waybill,
        ], 'waybill, id');
        if (empty($info)) {
//            var_dump($data);
            $id = $this->insert($data);
            if ($id) {
                $list[] = $waybill;
            }
        } else {
            $id = $info['id'];
            $list[] = $waybill;
        }
        return $id;
    }


    public function insert($data)
    {
        $result = parent::insert([
            'companyListId' => $data['companyListId'],
            'waybill' => $data['waybill'],
            'origin' => $data['origin'],
            'target' => $data['target'],
//            'status' => empty($data['status']) ? 1 : $data['status'],
            'subscribeCount ' => 0,
            'subscribeTime' => date('Y-m-d H:i:s'),
            'subscribeResult' => '待订阅',
            'subscribeStatus' => self::STATUS_SUBSCRIBE_WAIT
        ]);
        if ($result) {
            $id = $this->lastInsertId();
            if (!empty($data['data'])) {
                DbMessageCenter_KuaidiData::getInstance()->saveById([
                    'id' => $id,
                    'data' => $data['data']
                ], $id);
            }
        }
        return $id;
    }

    public function update($data, $condition = null)
    {
        if (!empty($data['data'])) {
            $_data = $data['data'];
        }
        unset($data['data']);
        $result = parent::update($data, $condition); // TODO: Change the autogenerated stub
        if (!empty($_data)) {
            if ($id = self::getWhereConditionValue($condition, 'id')) {
                DbMessageCenter_KuaidiData::getInstance()->saveById([
                    'id' => $id,
                    'data' => $_data
                ], $id);
            }
        }
        return $result;
    }

    public function getInfoByCacheWithWaybill($waybill, $companyListId)
    {
        $key = self::REDIS_KEY.'w:'.$waybill.':c:'.$companyListId;
        $result = $this->proxyModelSearchWithRedis($key, [
            $this, 'find'
        ], [
            [
                'waybill' => $waybill,
                'companyListId' => $companyListId
            ],
            'id, origin, target, status'
        ]);
        return $result;
    }


    public function saveStatusById($id, $data)
    {
        $flag = $this->update($data, [
            'id' => $id
        ]);
        return $flag;
    }

    public function setSubscribeResartResult($updateDate, $data)
    {
        if ($data['restart']) {
            $result = $this->find([
                'id' => $data['id']
            ], 'subscribeCount,subscribeStatus');
            if (empty($result)) {
                return false;
            }
            /**
             * 如果状态是发送结束
             * 且服务判断需要重新发送
             */
            if (!in_array($result['subscribeStatus'], array(
                    self::STATUS_SUBSCRIBE_WAIT,
                    self::STATUS_SUBSCRIBE_ING
                )) && Model::factoryCreate('Kuaidi_Service')->checkRestart($data)
            ) {
                $updateDate['subscribeStatus'] = self::STATUS_SUBSCRIBE_WAIT;
                $updateDate['subscribeResult'] = '重新订阅';
            }

        }
        return $updateDate;
    }

    /**
     * 处理订阅数据
     * @return $this
     */
    public function subscribe()
    {
        $service = new Kuaidi_Service;
        $waybillList = $this->findAll([
            'where' => [
                'subscribeStatus' => self::STATUS_SUBSCRIBE_WAIT
            ],
            'limit' => [0, 1000]
        ], 'id, companyListId, waybill, origin, target', true);
        if (!empty($waybillList)) {
            $ids = array_keys($waybillList);
            //更新执行中
            $result = $this->update([
                'subscribeStatus' => self::STATUS_SUBSCRIBE_ING
            ], ['id' => $ids]);
            if (!$result) {
                return $this;
            }
            foreach ($waybillList as $data) {
                //循环订阅
                $kuadiData = new Kuaidi_Data(new Kuaidi_CompanyList(Model::factoryCreate('DbMessageCenter_KuaidiCompanyList')));
                $kuadiData->setSubcribeData($data);
                $result = $service->subscribe($kuadiData);
                if ($result['code'] == 1) {
                    $updateData = [
                        'subscribeCount = subscribeCount + 1',
                        'subscribeTime' => date('Y-m-d H:i:s'),
                        'subscribeResult' => $result['mes'],
                        'subscribeStatus' => self::STATUS_SUBSCRIBE_SUCCESS
                    ];
                } else {
                    $updateData = [
                        'subscribeTime' => date('Y-m-d H:i:s'),
                        'subscribeResult' => $result['mes'],
                        'subscribeStatus' => self::STATUS_SUBSCRIBE_FORBIDDIN
                    ];
                }
                $this->update($updateData, [
                    'id' => $data['id']
                ]);
            }
        }
        return $this;
    }


    /**
     * 从队列里面去数据,并更新
     */
    public function saveQueueData()
    {
        $kuaidi = Model::factoryCreate('ProMessageCenter_KuaiDiSubscribe');
        $restartList = [];
        for ($i = 0; $i < 3000; $i++) {
            //获取redis记录
            $kuaiDiInfoString = $kuaidi->getQueue();
            if (empty($kuaiDiInfoString)) {
                break;
            }
            $kuaiDiInfo = Model::deCode($kuaiDiInfoString);
            if (empty($kuaiDiInfo)) {
                log_message('decode error', $kuaiDiInfoString, 'kuaidi_script');
                continue;
            }
            if (empty($kuaiDiInfo['id'])) {
                $id = $this->saveFirst($kuaiDiInfo);
            } else {
                $id = $kuaiDiInfo['id'];
                $data = ProMessageCenter_KuaiDiNotify::getCacheWaybillData($id);
                if (empty($data)) {
                    $id = 0;
                } else {
                    $updateDate = [
                        'status' => $kuaiDiInfo['status'],
                        'data' => $data,
                    ];
                    $updateDate = $this->setSubscribeResartResult($updateDate, $kuaiDiInfo);
                    if ($updateDate && $this->saveStatusById($id, $updateDate)) {
                        ProMessageCenter_KuaiDiNotify::removeCacheWaybillData($id);
                    } else {
                        $id = 0;
                    }
                }

            }
            if (!$id) {
                $restartList[] = $kuaiDiInfo;
            }
        }
        //重新加入队列
        for ($i = 0; $i < count($restartList); $i++) {
            $kuaidi->addQueue($restartList[$i]);
        }
        return $this;
    }

}