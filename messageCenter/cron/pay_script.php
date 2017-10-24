<?php
set_time_limit(0);
$logtime = array_sum(explode(' ', microtime()));

@define('TEMPLATE', 'messageCenter');
include dirname(dirname(dirname(__FILE__))) . '/init.php';

$payOrder = new DbMessageCenter_PayOrder;
$payPay = new ProMessageCenter_PayPay;
$payClose = new ProMessageCenter_PayClose;
$payNotify = new ProMessageCenter_PayNotify;


//从集合中取数据

//while (1) {
    try {
        run($payPay, $payOrder, $payClose, $payNotify);
    } catch (Exception $e) {
        sleep(1000);
        mysql::getInstance()->__destruct();
        PhpRedis::getInstance()->close();
        PhpRedis::getInstance()->__construct();
        unset($payOrder);
        unset($payPay);
        $payOrder = new DbMessageCenter_PayOrder;
        $payPay = new ProMessageCenter_PayPay;
        $payClose = new ProMessageCenter_PayClose;
        $payNotify = new ProMessageCenter_PayNotify;
    }

//}

function run(ProMessageCenter_PayPay $payPay, DbMessageCenter_PayOrder $payOrder, ProMessageCenter_PayClose $payClose, ProMessageCenter_PayNotify $payNotify)
{
    $data = $payPay->getPayQueue();
    if (!insert($data, $payOrder, $payPay, $payClose, $payNotify)) {
        throw new Exception('', 500);
    }
    $data = $payClose->getCloaseQueue();
    if (!close($data, $payOrder, $payClose, $payNotify)) {
        throw new Exception('', 500);
    }
    $data = $payNotify->getNotifyQueue();
    if (!notify($data, $payOrder, $payClose, $payNotify)) {
        throw new Exception('', 500);
    }
}


function insert($dataList, DbMessageCenter_PayOrder $payOrder, ProMessageCenter_PayPay $payPay, ProMessageCenter_PayClose $payClose, ProMessageCenter_PayNotify $payNotify)
{

    if(!$dataList){
        return true;
    }
    foreach ($dataList as $sn => $id) {
        $reuslt = $payOrder->find([
            'sn' => $sn
        ], 'id');
        if (!empty($reuslt)) {
            $payPay->removeSnOfQueue($sn);
            continue;
        }

        //取出数据
        $data = $payPay->getFileCacheOfQueue($sn);
        if ($data) {
            $payCloseFlag = $payNotifyFlag = false;
            /**
             * 关闭队列有数据
             */
            $data = Model::deCode($data);
            if ($payClose->inQueue($sn)) {
                $data['status'] = DbMessageCenter_PayOrder::STATUS_PAY_CLOSE;
                $payCloseFlag = true;
            }
            /**
             * 充值回调队列有数据
             *
             */
            if ($payNotify->inQueue($sn)) {
                $notifyData = $payNotify->getFileCacheOfQueue($sn);
                $data = array_merge($data, $notifyData);
                $payNotifyFlag = true;
            }

            //插入数据
            $result = $payOrder->insert($data);
            if ($result) {
                $payPay->removeSnOfQueue($sn);
                $payCloseFlag && $payClose->removeSnOfQueue($sn);
                $payNotifyFlag && $payNotify->removeSnOfQueue($sn);
            } else {
                return false;
            }
        }
    }
    return true;
}

function close($data, DbMessageCenter_PayOrder $payOrder, ProMessageCenter_PayClose $payClose, ProMessageCenter_PayNotify $payNotify)
{
    if(!$data){
        return true;
    }
    foreach ($data as $sn) {
        /**
         * 如果存在支付成功队列, 则直接删除
         */
        if ($payNotify->inQueue($sn)) {
            $payClose->removeSnOfQueue($sn);
            continue;
        }
        $info = $payOrder->getInfoBySnKey($sn);
        if (!empty($info)) {
            $r = $payOrder->find([
                'id' => $info['id']
            ], 'status');
            /**
             * 如果状态是支付成功
             * 则跳过
             */
            if ($payOrder->checkStatusWithStatus($r['status'])) {
                continue;
            }
            $r = $payOrder->update([
                'status' => DbMessageCenter_PayOrder::STATUS_PAY_CLOSE
            ], [
                'id' => $info['id']
            ]);
            if (!$r) {
                return false;
            }
            $payClose->removeSnOfQueue($sn);
        }
    }
    return true;
}

function notify($data, DbMessageCenter_PayOrder $payOrder, ProMessageCenter_PayClose $payClose, ProMessageCenter_PayNotify $payNotify)
{
    if(!$data){
        return true;
    }
    foreach ($data as $sn) {
        /**
         * 如果存在关闭队列, 删除
         */
        if ($payClose->inQueue($sn)) {
            $payClose->remove($sn);
        }
        $info = $payOrder->getInfoBySnKey($sn);
        if (!empty($info)) {
            $data = $payOrder->getFileCacheOfQueue($sn);
            if ($data) {
                $data = Model::deCode($data);
                $r = $payOrder->update(
                    $data,
                    [
                        'id' => $info['id']
                    ]);
                if (!$r) {
                    return false;
                }
                $payNotify->removeSnOfQueue($sn);
            }

        }
    }
    return true;
}


//结束用时
$logetime = array_sum(explode(' ', microtime()));
$lives = number_format(($logetime - $logtime), 6);
//写日志
$string = __FILE__ . "    :" . date('Y-m-d H:i:s', $logtime) . "  :" . $lives . "secs\r\n";
echo $string;
exit;