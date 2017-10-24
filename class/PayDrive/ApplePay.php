<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/9
 * Time: 9:25
 */
class PayDrive_ApplePay extends Model
{
    const DB_PREFIX = 'kaixinwan.apple_pay';

    const REDIS_KEY = 'kaixinwan:apple_pay:';

    const SANDBOX = true;

    public function __construct()
    {
        $this->setTableName(self::DB_PREFIX);
        parent::__construct();
        $this->fileCache = Cache_File::getInstance();
        $this->createTableOnly();
    }

    public function createTableOnly()
    {
        $key = self::REDIS_KEY . 'lock' . md5($this->getTableName());
        if ($this->fileCache->exists($key)) {
            return true;
        }
        $result = $this->createTable();
        if (!empty($result)) {
            $this->fileCache->set($key, 1);
        }
        return $result;
    }

    public function createTable()
    {
        $tableName = $this->getTableName();
        $sql = "CREATE TABLE if not exists {$tableName}  (
                    `id`  int(11) NOT NULL AUTO_INCREMENT COMMENT 'id' ,
                    `gid`  int(11) NULL DEFAULT 0 COMMENT '游戏id' ,
                    `title`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '标题' ,
                    `appid`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '苹果内购点' ,
                    PRIMARY KEY (`id`),
                    INDEX `gid` (`gid`) USING BTREE
                )
                ENGINE=InnoDB
                DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
                COMMENT='苹果内购支付点配置'
                AUTO_INCREMENT=1
                ROW_FORMAT=COMPACT
                ";
        return $this->getDb()->execute($sql);
    }


    public function getReceiptData($receipt, $isSandBox)
    {
        if ($isSandBox) {
            $endpoint = 'https://sandbox.itunes.apple.com/verifyReceipt';
        } else {
            $endpoint = 'https://buy.itunes.apple.com/verifyReceipt';
        }

        $postData = json_encode(
            array('receipt-data' => $receipt)
        );


        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);  //这两行一定要加，不加会报SSL 错误
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);


        $response = curl_exec($ch);
        $errno = curl_errno($ch);
        $errmsg = curl_error($ch);
        curl_close($ch);

        //判断时候出错，抛出异常
        if ($errno != 0) {
            log_message('app', "[errno: $errno; errmsg: $errmsg]", 'apple');
            return false;
        }

        $data = json_decode($response, true);
        if (!empty($data['status']) || empty($data['receipt']) || empty($data['receipt']['in_app'])) {
            return false;
        }

        $order = array(
            'sn' => $data['receipt']['in_app'][0]["transaction_id"],
            'code' => 1
        );

        if($data['environment'] == 'Sandbox' ){
            $order['code'] = 2;
            return $order;
        }

        return $order;
    }


}