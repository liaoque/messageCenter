<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/20
 * Time: 16:30
 *
 * 过滤中转类
 *
 */
class Filter
{

    public function filterUid($uid)
    {
        $where = array();
        if (empty($uid)) {
            return $where;
        }
        $where['uid'] = $uid;
        return $where;
    }

    public function filterReturnStatusType($status)
    {
        $where = array();
        if (empty($status)) {
            return $where;
        }
        $where['return_status_type'] = $status;

        return $where;
    }
    public function filterReturnDel($status)
    {
        $where = array();
        if (empty($status)) {
            return $where;
        }
        $where['del'] = $status;

        return $where;
    }



    public function filterId($id)
    {
        $where = array();
        if (empty($id)) {
            return $where;
        }
        $where['id'] = $id;
        return $where;
    }

    public function filterOrderId($id)
    {
        $where = array();
        if (empty($id)) {
            return $where;
        }
        $where['order_id'] = $id;
        return $where;
    }
    public function filterPhone($phone)
    {
        $where = array();
        if (empty($phone)) {
            return $where;
        }
        $passport = Passport_User::getInstance();
        $result = $passport->find(array('phone' => $phone));
        $where['uid'] = empty($result) ? -1 : $result['uid'];
        return $where;
    }

    public function filterLoginName($loginName)
    {
        $where = array();
        if (empty($loginName)) {
            return $where;
        }
        $passport = User_User::getInstance();
        $result = $passport->dataFromName($loginName);
        if ($result['code'] == 1) {
            $result = Model::deCode(authcode($result['ck']));
            $where['uid'] = $result['uid'];
        } else {
            $where['uid'] = -1;
        }
        return $where;
    }


    /**
     * 把 用户名，uid, 手机， 转成uid
     * @param $type
     * @param $loginName
     * @return array|int
     */
    public function filterTypePassprot($type, $loginName)
    {
        switch ($type) {
            case 1:
                $result = $this->filterLoginName($loginName);
                break;
            case 2:
                $result = $this->filterUid($loginName);
                break;
            case 3:
                $result = $this->filterPhone($loginName);
                break;
            default:
                $result = array();
                break;
        }
        return $result;
    }


    public function filterType($serverId)
    {
        $where = array();
        if (empty($serverId)) {
            return $where;
        }
        $where['type'] = $serverId;
        return $where;
    }

    public function filterFlag($flag){
        $where = array();
        if (empty($flag)) {
            return $where;
        }
        $where['flag'] = $flag;
        return $where;
    }

    public function filterStatus($serverId)
    {
        $where = array();
        if (empty($serverId)) {
            return $where;
        }
        $where['status'] = $serverId;
        return $where;
    }


    public function filterShippingAddress($shipping_address)
    {
        $where = array();
        if (empty($shipping_address)) {
            return $where;
        }
        $where['shipping_address'] = $shipping_address;
        return $where;
    }
    //茶园名称
    public function filterProductName($serverId)
    {
        $where = array();
        if (empty($serverId)) {
            return $where;
        }
        $where['name'] = $serverId;
        return $where;
    }
    //茶园名称
    public function filterShopName($serverId)
    {
        $where = array();
        if (empty($serverId)) {
            return $where;
        }
        $where['shopname'] = $serverId;
        return $where;
    }


    public function filterProductId($pid){
        $where = array();
        if (empty($pid)) {
            return $where;
        }
        $where['product_id'] = $pid;
        return $where;
    }


    public function filterReviewRating($rating){
        $where = array();
        if (empty($rating)) {
            return $where;
        }
        $where['rating'] = $rating;
        return $where;
    }

    public function filterCode($code){
        $where = array();
        if (empty($code)) {
            return $where;
        }
        $where['code'] = $code;
        return $where;
    }

    public function filterDiyCategory($category){
        $where = array();
        if (empty($category)) {
            return $where;
        }
        $where['category'] = $category;
        return $where;
    }

    public function filterName($name){
        $where = array();
        if (empty($name)) {
            return $where;
        }
        $where['name'] = $name;
        return $where;
    }

    public function filterHot($hot){
        $where = array();
        if (empty($hot)) {
            return $where;
        }
        $where['hot'] = $hot;
        return $where;
    }
    public function filterAwardId($award_id){
        $where = array();
        if (empty($award_id)) {
            return $where;
        }
        $where['award_id'] = $award_id;
        return $where;
    }
    public function filterFarmId($id){
        $where = array();
        if (empty($id)) {
            return $where;
        }
        $where['farm_id'] = $id;
        return $where;
    }

    public function filterTitle($title){
        $where = array();
        if (empty($title)) {
            return $where;
        }
        $where['title'] = $title;
        return $where;
    }
    public function filterNjqmPhone($phone){
        $where = array();
        if (empty($phone)) {
            return $where;
        }
        $where['phone'] = $phone;
        return $where;
    }
    public function filterNjqmloginName($login_name){
        $where = array();
        if (empty($login_name)) {
            return $where;
        }
        $where['login_name'] = $login_name;
        return $where;
    }
    //茶园编码
    public function filterModel($serverId)
    {

        $where = array();
        if (empty($serverId)) {
            return $where;
        }
        $where['model'] = $serverId;
        return $where;
    }




    public function filterClassName($serverId)
    {

        $where = array();
        if (empty($serverId)) {
            return $where;
        }
        $where['className'] = $serverId;
        return $where;
    }
    public function filterRangeType($type){
        $where = array();
        if (empty($type)) {
            return $where;
        }
        $where['range_type'] = $type;
        return $where;
    }

    public function filterPublishedUid($uid){
        $where = array();
        if (empty($uid)) {
            return $where;
        }
        $where['published_uid'] = $uid;
        return $where;
    }

    public function filterOrderNumber($orderNumber){
        $where = array();
        if (empty($orderNumber)) {
            return $where;
        }
        $where['order_number'] = $orderNumber;
        return $where;
    }

    public function filterParams($params)
    {

        $passport = $this->filterTypePassprot(1, $params['username']);
        $where = array(
            $passport,
            self::filterReturnDel($params['del']),
            self::filterType($params['type']),
            self::filterFlag($params['flag']),
            self::filterOrderId($params['order_id']),
            self::filterID($params['id']),
            self::filterStatus($params['status']),
            self::filterShippingAddress($params['shipping_address']),
            self::filterModel($params['model']),
            self::filterUid($params['uid']),
            self::filterReturnStatusType($params['return_status_type']),
            self::filterProductName($params['productName']),
            self::filterShopName($params['shopname']),
            self::filterReviewRating($params['rating']),
            self::filterProductId($params['product_id']),
            self::filterCode($params['code']),
            self::filterDiyCategory($params['category']),
            self::filterName($params['name']),
            self::filterTitle($params['title']),
            self::filterNjqmPhone($params['phone']),
            self::filterNjqmloginName($params['login_name']),
            self::filterHot($params['hot']),
            self::filterAwardId($params['award_id']),
            self::filterFarmId($params['farm_id']),

            self::filterRangeType($params['range_type']),
            self::filterPublishedUid($params['published_uid']),
            self::filterOrderNumber($params['order_number']),
        );

        if (!empty($params['date'])) {
            $where[] = array('date' => $params['date']);
        }


        if (!empty($params['create_time'])) {
            $where[] = array('create_time >=' => $params['create_time']);
        }
        if (!empty($params['end_time'])) {
            $where[] = array('create_time <' => $params['end_time']);
        }




        $filter = call_user_func_array('array_merge', $where);
        return $filter;
    }


}