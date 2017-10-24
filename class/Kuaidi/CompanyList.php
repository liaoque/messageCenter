<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/31
 * Time: 14:01
 */
class Kuaidi_CompanyList
{
    private $list = [];

    private $dataSource;

    /**
     * Kuaidi_CompanyList constructor.
     * @param DbMessageCenter_KuaidiCompanyList $dataSource
     * 获取数据列表的参数
     */
    public function __construct(DbMessageCenter_KuaidiCompanyList $dataSource)
    {
        $this->dataSource = $dataSource;
    }

//    public function getInfoByNum($num, $config)
//    {
//        if (empty($this->list[$num])) {
//            /**
//             * 从数据源那边获取数据
//             */
//            $info = $this->dataSource->getInfoByNumOfCache($num, $config['appId']);
//            if (!empty($info)) {
//                $this->list[$num] = [
//                    'companyName' => $info['otherName'],
//                    'companyEnName' => $info['otherEnName'],
//                    'companyNum' => $info['otherNum'],
//                    'companyId' => $info['otherId'],
//                    'id' => $info['id'],
//                    'name' => $info['name'],
//                    'enName' => $info['enName'],
//                    'num' => $num,
//                ];
//            }
//        }
//        return $this->list[$num];
//    }


    public function getInfoById($id)
    {
        if (empty($this->list[$id]['companyName'])) {
            /**
             * 从数据源那边获取数据
             */
            $info = $this->dataSource->getInfoByIdOfCache($id);
            if (!empty($info)) {
//                $this->list[$id] = array_merge($this->list[$id], [
//                    'companyName' => $info['companyName'],
//                    'companyEnName' => $info['companyEnName'],
//                    'companyNum' => $info['companyNum'],
//                    'companyId' => $id,
//                ]);
                $this->list[$id] = [
                    'companyName' => $info['companyName'],
                    'companyEnName' => $info['companyEnName'],
                    'companyNum' => $info['companyNum'],
                    'companyId' => $id,
                ];
            }
        }
        return $this->list[$id];
    }

    public function getOtherInfoByCompanyListId($companyId, $appId)
    {
        if (empty($this->list[$companyId])) {
            /**
             * 从数据源那边获取数据
             */
            $info = $this->dataSource->getOtherInfoById($companyId, $appId);
            if (!empty($info)) {
//                $this->list[$companyId] = array_merge($this->list[$companyId], [
//                    'otherId' => $info['otherId'],
//                    'otherName' => $info['otherName'],
//                    'otherEnName' => $info['otherEnName'],
//                    'otherNum' => $info['otherNum'],
//                    'otherStatus' => $info['otherStatus'],
//                    'otherAppId' => $appId,
//                ]);
                $this->list[$companyId] = [
                    'otherId' => $info['otherId'],
                    'otherName' => $info['otherName'],
                    'otherEnName' => $info['otherEnName'],
                    'otherNum' => $info['otherNum'],
                    'otherStatus' => $info['otherStatus'],
                    'otherAppId' => $appId,
                ];
            }
        }
        return $this->list[$companyId];
    }


}