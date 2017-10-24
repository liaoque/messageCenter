<?php

/**
 * 有序集合
 * 注意: 该结构里面包含了三个字段
 *  1. 分数,用来控制排序的
 *  2. 元素
 *  3. 索引,元素所在集合中的实际位置
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/12
 * Time: 9:36
 */
class Cache_SortGather extends Cache_Base
{
    private $redis;


    public function __construct()
    {
        $this->setRedis(\PhpRedis::getInstance());
    }

    protected function getRedis()
    {
        return $this->redis;
    }

    protected function setRedis($redis)
    {
        $this->redis = $redis;
    }

    /**
     * 获取集合中所有的元素
     * @param null $cacheName
     * @param string $sort
     * @param null $withscores
     * @return mixed
     */
    public function get($cacheName = null, $sort = 'asc', $withscores = null)
    {
        return $this->getByIndex($cacheName, 0, -1, $sort, $withscores);
    }

    /**
     * 获取索引区间的
     * @param null $cacheName
     * @param int $start 开始索引
     * @param int $end 结束索引
     * @param string $sort asc/desc 按索引排序
     * @param bool $withscores 默认为false不返回分数,如果设置成true,返回格式如下
     *      array('值'=> '分数', '值' => '分数')
     * @return mixed
     */
    public function getByIndex($cacheName, $start = 0, $end = -1, $sort = 'asc', $withscores = null)
    {
        if ($sort == 'asc') {
            $result = $this->getRedis()->zRange($cacheName, $start, $end, $withscores);
        } else {
            $result = $this->getRedis()->zRevRange($cacheName, $start, $end, $withscores);
        }
        return $result;
    }

    /**
     * @param null $cacheName
     * @param int|string $min 开始的分数
     * @param int|string $max 结束的分数
     * @param string $sort asc/desc     按分数排序
     * @param null $withscores 默认为false不返回分数,如果设置成true,返回格式如下
     *                                  array('值'=> '分数', '值' => '分数')
     * @param null $offset 在第几个元素开始返回int
     * @param int $count 返回的数量
     * @return mixed
     */
    public function getByScore($cacheName = null, $min = '-inf', $max = '+inf', $sort = 'asc', $withscores = null, $offset = null, $count = 10)
    {
        $options = array(
            'withscores' => $withscores
        );
        if ($offset) {
            $options['limit'] = array($offset, $count);
        }
        if ($sort == 'asc') {
            $result = $this->getRedis()->zRangeByScore($cacheName, $min, $max, $options);
        } else {
            $result = $this->getRedis()->zRevRangeByScore($cacheName, $min, $max, $options);
        }
        return $result;
    }


    /**
     * 检查集合过期时间
     * @param null $cacheName
     * @return mixed
     */
    public function ttl($cacheName = null)
    {
        return $this->getRedis()->ttl($cacheName);
    }

    /**
     * 设置过期时间
     * @param null $cacheName
     * @param int $timeOut
     * @return bool
     */
    public function setTimeOut($cacheName = null, $timeOut = 0)
    {
        if ($this->exists($cacheName)) {
            return $this->getRedis()->expire($cacheName, $timeOut);
        }
        return false;
    }

    /**
     * 集合是否存在,或者集合中某个元素是否存在
     * @param null $cacheName
     * @param null $v
     * @return mixed
     */
    public function exists($cacheName = null, $v = null)
    {
        if ($v) {
            return $this->getIndex($cacheName, $v) !== false;
        }
        return $this->getRedis()->exists($cacheName);
    }

    /**
     * 获取多个集合的交集,到新的集合中
     * @param $Output                   新集合的名字
     * @param $ZSetKeys                 数组,要计算的多个集合
     * @param array|null $Weights 乘法因子,如果定义,每个集合都要相城
     * @param string $aggregateFunction 聚合方式, 'SUM', 'MIN', 'MAX'
     * @return mixed                    返回新集合的元素数量
     *
     * $aaa = 'aaaa';
     * $bbb = 'bbbb';
     * $ccc = 'cccc';
     * $sortGather->set($aaa, 0, 'a1', 1, 'a2', 2, 'a3', 4, 'a4', 6, 'c6');
     * $sortGather->set($bbb, 0, 'b1', 1, 'b2', 2, 'b3', 4, 'b4', 5, 'c6');
     * $result =  $sortGather->zInter($ccc, array($aaa, $bbb));
     * var_dump($result); // 1
     * $result = $sortGather->getByIndex($ccc, 0, -1, 'asc', true);
     * var_dump($result); //array(1) { ["c6"]=> float(11) }
     *
     * $result =  $sortGather->zInter($ccc, array($aaa, $bbb), array(2, 3));
     * var_dump($result); // 1
     * $result = $sortGather->getByIndex($ccc, 0, -1, 'asc', true);
     * var_dump($result); //array(1) { ["c6"]=> float(27) }  27 = 6 * 2 + 5 * 3
     *
     * $result =  $sortGather->zInter($ccc, array($aaa, $bbb), array(1, 1), 'MIN');
     * var_dump($result); // 1
     * $result = $sortGather->getByIndex($ccc, 0, -1, 'asc', true);
     * var_dump($result); //array(1) { ["c6"]=> float(5) }  5 = MIN(6 * 1 ,  5 * 1)
     */
    public function zInter($Output, $ZSetKeys, $Weights = null, $aggregateFunction = 'SUM')
    {
        return $this->getRedis()->zInter($Output, $ZSetKeys, $Weights, $aggregateFunction);
    }


    /**
     * 获取多个集合的并集,到新的集合中. 参考 zInter
     * @param $Output                   新集合的名字
     * @param $ZSetKeys                 数组,要计算的多个集合
     * @param array|null $Weights 乘法因子,如果定义,每个集合都要相城
     * @param string $aggregateFunction 聚合方式, 'SUM', 'MIN', 'MAX'
     * @return mixed                    返回新集合的元素数量
     */
    public function zUnion($Output, $ZSetKeys, $Weights = null, $aggregateFunction = 'SUM')
    {
        return $this->getRedis()->zUnion($Output, $ZSetKeys, $Weights, $aggregateFunction);
    }


    /**
     * 对集合中某个元素的分数,进行自增
     * @param $cacheName 集合名字
     * @param $v 元素值
     * @param int $stup 步长
     */
    public function incSort($cacheName, $v, $stup = 1)
    {
        return $this->getRedis()->zIncrBy($cacheName, $stup, $v);
    }


    /**
     * 获取集合的长度
     * 如果设置了 $min 和 $max 则获取这个分数区间内有多少个元素
     * @param $cacheName
     * @param null $min
     * @param null $max
     * @return mixed
     */
    public function len($cacheName, $min = null, $max = null)
    {
        if ($min === null) {
            $result = $this->getRedis()->zCard($cacheName);
        } elseif ($max) {
            $result = $this->getRedis()->zCount($cacheName, $min, $max);
        }
        return $result;
    }

    /**
     * $this->set(xxx, [ 'aa' => 1,  '123' => 2 ])
     * $this->set(xxx, 1, 'aa', 2 , '123')
     * @param $cacheName
     * @param $v
     * @return mixed
     */
    public function set($cacheName, $v)
    {
        if (is_array($v)) {
            $args = array($cacheName);
            foreach ($v as $value => $index) {
                $args[] = $index;
                $args[] = $value;
            }
        } else if (func_num_args() > 2) {
            $args = func_get_args();
        }
        $result = call_user_func_array(array($this->getRedis(), 'zAdd'), $args);
        return $result;
    }

    /**
     * 根据元素返回该元素在集合中的索引
     * @param $cacheName
     * @param $v
     * @param string $sort
     *      asc/desc  asc 返回从小到大的索引, desc返回从大到小的索引
     * @return mixed
     */
    public function getIndex($cacheName, $v, $sort = 'asc')
    {
        if ($sort == 'asc') {
            $result = $this->getRedis()->zRank($cacheName, $v);
        } else {
            $result = $this->getRedis()->zRevRank($cacheName, $v);
        }
        return $result;
    }

    /**
     * 根据元素,返回该元素在集合中的分数
     * @param $cacheName
     * @param $v
     * @return mixed
     */
    public function getScore($cacheName, $v)
    {
        $result = $this->getRedis()->zScore($cacheName, $v);
        return $result;
    }


    /**
     * 删除集合，或者集合中某几个元素.
     * $this->del(xxx) 删除整个集合
     * $this->del(xxx, 2) 删除整个集合中两个元素
     * $this->del(xxx, [1, 4]) 删除集合中元素为1, 4的两个元素
     * @param null $cacheName
     * @param null $v
     * @return bool
     */
    public function del($cacheName = null, $v = null)
    {
        if (!$cacheName) {
            return false;
        }
        if (empty($v)) {
            //如果没有指定元素,则直接删除整个集合
            $result = $this->getRedis()->del($cacheName);
        } elseif (is_array($v)) {
            //指定为数组,则删除数组中的元素
            $result = $this->remove($cacheName, $v);
        } else {
            $result = $this->removeCount($cacheName, 0, $v);
        }
        return $result;
    }

    /**
     * 删除指定分数区间的元素
     * $this->delByScore('xxx', 0, 5); 删除分数0-5的元素
     * @param $cacheName
     * @param $start
     * @param $end
     */
    public function delByScore($cacheName, $start, $end)
    {
        $result = $this->getRedis()->zRemRangeByScore($cacheName, $start, $end);
        return $result;
    }

    /**
     * 删除指定索引区间的元素
     * $this->removeCount('xxx', 0, 5); 从索引0开始,删除5个元素
     * @param $cacheName
     * @param $start
     * @param $end
     * @return mixed
     */
    public function removeCount($cacheName, $start, $end)
    {
        $result = $this->getRedis()->zRemRangeByRank($cacheName, $start, $end);
        return $result;
    }

    /**
     * 从集合中删除一个或多个指定的元素
     * $this->remove(xxxx, 1)
     * $this->remove(xxxx, 1, 2, 3, 4)
     * $this->remove(xxxx, [1, 2 , 3, 4])
     * @param $cacheName
     * @param $v
     * @return mixed
     */
    public function remove($cacheName, $v)
    {
        if (is_array($v)) {
            $args = $v;
            array_unshift($args, $cacheName);
            $result = call_user_func_array(array($this->getRedis(), 'zRem'), $args);
        } else {
            $args = func_get_args();
            $result = call_user_func_array(array($this->getRedis(), 'zRem'), $args);
        }
        return $result;
    }


}