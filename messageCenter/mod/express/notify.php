<?php

class Express_Notify extends RouteBase
{

    public function kuaidi100Func()
    {
        $otherAppId = 1;
        $result = file_get_contents("php://input");

        $service = new ProMessageCenter_KuaiDiNotify;
        try {
            $result = $service->notify($result, $otherAppId);
            if (empty($result)) {
                throw new Kuaidi_Exception(Kuaidi_Exception::STATUS_FORBIDDEN_PARAM);
            }
            //查找快递信息
            $companyNum = $result['companyNum'];
            $waybill = $result['waybill'];
            $companyNum = DbMessageCenter_KuaidiOtherCompanyList::getInstance()->find([
                'otherAppId' => $service->getService($otherAppId)->getConfig('appId'),
                'num' => $companyNum
            ], 'companyListId as id');
            if (empty($companyNum)) {
                throw new Kuaidi_Exception(Kuaidi_Exception::STATUS_FORBIDDEN_COMPANYNUM);
            }
            $companyListId = $companyNum['id'];
            $info = DbMessageCenter_KuaidiWaybill::getInstance()->find([
                'companyListId' => $companyListId,
                'waybill' => $waybill
            ], 'id, origin, target, subscribeStatus, subscribeCount');

            if (empty($info)) {
                //无快递单号
                throw new Kuaidi_Exception(Kuaidi_Exception::STATUS_FORBIDDEN_WAYBILL);
            }
            //有快递单号
            $result = $service->updateStatusById(
                $info['id'],
                $result['data'],
                $result['status'],
                $result['restart']
            );
            $code = $result ? Kuaidi_Exception::STATUS_SUCCESS : Kuaidi_Exception::STATUS_FORBIDDEN_SERVICE;
            $mes = Kuaidi_Exception::$status[$code];
        } catch (Exception $e) {
            $code = $e->getCode();
            $mes = $e->getMessage();
        }

        $result = $service->response($code, $mes, $otherAppId);
        echo Model::enCode($result);
    }
}