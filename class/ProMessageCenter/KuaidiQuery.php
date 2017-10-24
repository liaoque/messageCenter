<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/31
 * Time: 16:57
 */
class ProMessageCenter_KuaidiQuery
{

    public function query($appId, $companyNum, $waybill)
    {
        $info = [];
        $companyList = Model::factoryCreate('DbMessageCenter_KuaidiCompanyList');
        $id = $companyList->getIdByCompanyNumOfCache($companyNum);
        if ($id) {
            $result = Model::factoryCreate('DbMessageCenter_KuaidiWaybill')->getInfoByCacheWithWaybill($waybill, $id);
            if (!empty($result)) {
                $kuaidiInfo = Model::factoryCreate('DbMessageCenter_KuaidiData')->getInfoByIdOfCache($result['id']);
                $info = array_merge($kuaidiInfo, $result);
            }
        }
        return $info;

    }
}