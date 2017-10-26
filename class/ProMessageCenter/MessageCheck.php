<?php
if (!defined('IN_BOOT')) {
    exit('Access Denied');
}

class ProMessageCenter_MessageCheck
{

    public $param = array();
    public $redis = null;
    public $appKey = null;
    public $companyListId = null;
    public $templateId = null;
    public $payData = [];

    public function checkApp($appId)
    {
        $app = DbMessageCenter_App::getInstance();
        $result = $app->find(['id' => $appId], 'authorizeKey');
        if (empty($result)) {
            $this->form->set_message('checkApp', '未知的应用');
            return false;
        }
        $this->appKey = $result['authorizeKey'];
        return true;
    }

    public function checkTemplate($tmpId)
    {
        $mail = DbMessageCenter_AppTemplate::getInstance();
        $result = $mail->find(['id' => $tmpId], 'id, appId, template_id');
        if (empty($result)) {
            $this->form->set_message('checkTemplate', '未知模版');
            return false;
        }
        if ($result['appId'] != $this->param['appId']) {
            $this->form->set_message('checkTemplate', '您无权限使用该模版');
            return false;
        }
        $this->templateId = $result['template_id'];
        return true;
    }


    public function checkSign($sign)
    {
        $result = self::isSign($this->param, $sign, $this->appKey);
        if (!$result) {
            $this->form->set_message('checkSign', '无效提交');
            return false;
        }
        return true;
    }

    public function checkCode($code)
    {
        $pkId = $_POST['pkId'];
        $appId = $_POST['appId'];
        $code2 = ProMessageCenter_Code::checkCode($pkId, $code, $appId, $this->param['type']);
        if ($code2 === null) {
            $this->form->set_message('checkCode', '验证码已过期');
            return false;
        }
        if ($code2 === false) {
            $this->form->set_message('checkCode', '验证码错误');
            return false;
        }
        return true;
    }

    public function checkCompanyNum($companyNum)
    {
        $result = DbMessageCenter_KuaidiCompanyList::getInstance()->find(['num' => $companyNum], 'id,status');
        if (empty($result)) {
            $this->form->set_message('checkCompanyNum', '快递公司编号错误');
            return false;
        }
        if ($result['status'] != DbMessageCenter_KuaidiCompanyList::STATUS_NORMAL) {
            $this->form->set_message('checkCompanyNum', '快递公司在休息');
            return false;
        }
        $this->companyListId = $result['id'];
        return true;
    }

    public function checkSn($sn)
    {
        $result = Model::factoryCreate('DbMessageCenter_PayOrder')->getInfoBySnOfCache($sn);
        if (empty($result)) {
            $this->form->set_message('checkSn', '订单不存在');
            return false;
        }
        foreach ($result as $key => $value) {
            $this->payData['id'] = $key;
        }
        return true;
    }

    public function checkPhone($phone)
    {
        if (preg_match("/^1[34578]{1}\d{9}$/", $phone)) {
            return true;
        } else {
            $this->form->set_message('checkPhone', '请输入正确手机号码');
        }
    }

}