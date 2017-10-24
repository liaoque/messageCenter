<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/27
 * Time: 17:01
 */
class Kuaidi_Data
{
    /**
     * @companyName 公司Id
     */
    private $companyId = 0;

    /**
     * @companyName 公司名字
     */
    private $companyName;

    /**
     * @companyEnName 英文名字
     */
    private $companyEnName;

    /**
     * @companyNum 公司编号
     */
    private $companyNum;

    /**
     * @num 快递单号
     */
    private $waybill;

    /**
     * @from 出发城市
     */
    private $origin;

    /**
     * @to 目的地
     */
    private $target;

    /**
     * @otherCompanyId 三方快递公司id
     */
    private $otherCompanyId;

    /**
     * @otherCompanyName 三方快递公司名字
     */
    private $otherCompanyName;

    /**
     * @otherCompanyEnName 三方快递公司英文名字
     */
    private $otherCompanyEnName;

    /**
     * @otherCompanyNum 三方快递公司编号
     */
    private $otherCompanyNum;


    private $time;
    private $context;
    private $state;
    private $appId;

    /**
     * @var Kuaidi_CompanyList 快递公司列表
     */
    private $companyList;

    const STATUS_TOOK = 1;
    const STATUS_JOURNEY = 2;
    const STATUS_DIFFICULTY = 3;
    const STATUS_SEND = 4;
    const STATUS_SIGN = 5;
    const STATUS_REFUSAL = 6;
    const STATUS_RETURN = 7;

    /**
     * @var array
     */
    private static $status = array(
        self::STATUS_TOOK => '揽件',
        self::STATUS_JOURNEY => '在途',
        self::STATUS_DIFFICULTY => '疑难',
        self::STATUS_SEND => '派件',
        self::STATUS_SIGN => '签收',
        self::STATUS_REFUSAL => '退签',
        self::STATUS_RETURN => '退回'
    );

    /**
     * Kuaidi_Data constructor.
     * @param Kuaidi_CompanyList $companyList
     */
    public function __construct(Kuaidi_CompanyList $companyList)
    {
        $this->companyList = $companyList;
    }


    /**
     * 获取状态名字
     * @param $status
     * @return mixed
     */
    public static function getStatus($status)
    {
        if($status === false){
            return self::$status;
        }
        return self::$status[$status];
    }

    public function getCompanyName()
    {
        if (empty($this->companyName)) {
            $this->setCompanyData($this->getCompanyId());
        }
        return $this->companyName;
    }

    /**
     * 设置本地快递公司名字,
     * 快递公司英文名字,
     * 快递公司编号
     * @param $companyId
     * @return array
     */
    public function setCompanyData($companyId)
    {
        if (empty($companyId)) {
            return [];
        }
        $result = $this->companyList->getInfoById($companyId);
        if (!empty($result)) {
            $this->setCompanyName($result['companyName'])
                ->setCompanyEnName($result['companyEnName'])
                ->setCompanyNum($result['companyNum']);
        }
    }

    public function getCompanyEnName()
    {
        if (empty($this->companyEnName)) {
            $this->setOtherCompanyData($this->getCompanyId());
        }
        return $this->companyEnName;
    }

    public function getCompanyNum()
    {
        if (empty($this->companyNum)) {
            $this->setCompanyData($this->getCompanyId());
        }
        return $this->companyNum;
    }

    public function getCompanyId()
    {
        return empty($this->companyId) ? 0 : $this->companyId;
    }

    /**
     * 设置三方快递公司
     * @param $companyId
     * @return array
     */
    public function setOtherCompanyData($companyId, $appId)
    {
        if (empty($companyId)) {
            return [];
        }
        $result = $this->companyList->getOtherInfoByCompanyListId($companyId, $appId);
        if (!empty($result)) {
            $this->setOtherCompanyId($result['otherId'])
                ->setOtherCompanyName($result['otherName'])
                ->setOtherCompanyEnName($result['otherEnName'])
                ->setOtherCompanyNum($result['otherNum']);
        }
    }

    public function getOtherCompanyId($config = array())
    {
        if (empty($this->otherCompanyId)) {
            $appId = $config['appId'];
            $this->setOtherCompanyData($this->getCompanyId(), $appId);
        }
        return $this->otherCompanyId;
    }

    public function getOtherCompanyName($config = array())
    {
        if (empty($this->otherCompanyName)) {
            $appId = $config['appId'];
            $this->setOtherCompanyData($this->getCompanyId(), $appId);
        }
        return $this->otherCompanyName;
    }

    public function getOtherCompanyEnName($config = array())
    {
        if (empty($this->otherCompanyEnName)) {
            $appId = $config['appId'];
            $this->setOtherCompanyData($this->getCompanyId(), $appId);
        }
        return $this->otherCompanyEnName;
    }

    public function getOtherCompanyNum($config = array())
    {
        if (empty($this->otherCompanyNum)) {
            $appId = $config['appId'];
            $this->setOtherCompanyData($this->getCompanyId(), $appId);
        }
        return $this->otherCompanyNum;
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        $action = substr($name, 0, 3);
        $protype = lcfirst(substr($name, 3));
        switch ($action) {
            case 'get':
                if (property_exists($this, $protype)) {
                    return empty($this->$protype) ? '' : $this->$protype;
                }
                break;
            case 'set':
                if (property_exists($this, $protype) && $arguments[0]) {
                    $this->$protype = $arguments[0];
                }
                break;
            default:
                break;
        }
        return $this;
    }

    /**
     * @param $info
     * [
     *      from =>     出发城市
     *      to =>       目标城市
     *      companNum => 快递公司编码
     *      waybill =>  快递单
     *      appId =>    当前订阅应用id
     * ]
     * @return $this
     */
    public function setSubcribeData($info)
    {
        $this->setOrigin($info['origin'])
            ->setTarget($info['target'])
            ->setCompanyId($info['companyListId'])
            ->setWaybill($info['waybill']);
        return $this;
    }

    public function getFrom()
    {
        return $this->getOrigin();
    }

    public function getTo()
    {
        return $this->getTarget();
    }


}