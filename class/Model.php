<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/19
 * Time: 16:00
 *
 * Date: 2016/06/02
 * 1.修改常量小写为大写。
 * 2.增加初始化判断，只有子类定义了 常量“REDIS_KEY” 才会调用 PhpRedis::getInstance();
 *
 */
abstract class Model
{
    const LIMIT = 10;

    protected $tableName;

    private $redis;

    private static $instanceHandl = array();

    private $db;

    protected $pkId;

    private $colmuns;

    protected $pageNum = 0;

    protected static $debugSql = false;

    protected static $sqlList = [];

    public static function getInstance()
    {
        $className = get_called_class();
        if (empty(self::$instanceHandl[$className])) {
            self::$instanceHandl[$className] = new $className;
        }
        return self::$instanceHandl[$className];
    }

    /**
     * 工场模式
     * @param $className
     * @param $config
     * @return null
     */
    public static function factoryCreate($className, $config = array())
    {
        if(empty($className)){
            throw new Exception('$className 不能为空', -500);
        }
        if (empty(self::$instanceHandl[$className]) && class_exists($className)) {
            self::$instanceHandl[$className] = empty($config) ? new $className : new $className($config);
        }
        return empty(self::$instanceHandl[$className]) ? null : self::$instanceHandl[$className];
    }


    /**
     * 触发方法
     * @param $object
     * @param $method
     * @param array $ages
     * @return bool|mixed
     */
    public static function emit($object, $method, $ages = array())
    {
        $result = false;
        if (is_object($object) && method_exists($object, $method)) {
            $result = call_user_func_array(array($object, $method), $ages);
        }
        return $result;
    }


    public function __construct()
    {
        assert_options(ASSERT_ACTIVE, 1);
        assert_options(ASSERT_WARNING, 0);
        assert_options(ASSERT_BAIL, 1);
        assert_options(ASSERT_CALLBACK, 'assert_show_error');
        $this->db = Mysql::getInstance();
        if (defined('static::REDIS_KEY')) {
            $this->redis = PhpRedis::getInstance();
        }
        $this->setPkId('id');
//        $this->getColumns();
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
        if (self::$debugSql) {
            foreach(self::$sqlList as $sqlListString){
                echo "<script>console.log('" . $sqlListString . "')</script>";
            }
        }
    }

    public static function addSqlList($sql)
    {
        if (self::$debugSql) {
            self::$sqlList[] = $sql;
        }
    }

    /**
     * 表名是否存在
     * @param $tableName
     * @param string $dataBase
     * @return bool
     */
    public static function tablesExits($tableName, $dataBase = 'kaixinwan')
    {
        $tableName = str_replace($dataBase . '.', '', $tableName);
        $sql = "show tables from {$dataBase} LIKE '{$tableName}'";
        self::addSqlList($sql);
        $db = Mysql::getInstance();
        return !!$db->getResult($sql);
    }

    public function getRedis()
    {
        return $this->redis;
    }

    /**
     * 获取列
     * @return array|mixed
     */
    public function getColumns()
    {
        if (empty($this->colmuns)) {
            $tableName = $this->getTableName();
            assert($tableName, '表名未定义:');
            $fileMzq = Cache_File::getInstance();
            if ($fileMzq->exists($tableName) && $fileMzq->timeOut($tableName)) {
                $this->colmuns = json_decode($fileMzq->get($tableName), true);
            } else {
                $this->colmuns = $this->findColumns();
                !empty($this->colmuns) && $fileMzq->set($tableName, $this->colmuns);
            }
        }
        return $this->colmuns;
    }

    /**
     * 查找列
     * @return array
     */
    public function findColumns()
    {
        $sql = 'SHOW COLUMNS FROM ' . $this->getTableName();
        self::addSqlList($sql);
        $result = $this->getDb()->getAll($sql);
        $columns = array();
        foreach ($result as $v) {
            $columns[] = $v['Field'];
        }
        return $columns;
    }

    /**
     * 编码，默认是json
     * @param array $value
     * @param bool $flag
     * @return string
     */
    public static function enCode($value = array(), $flag = false)
    {
        $result = $value;
        if (is_array($value) || is_object($value)) {
            $result = $flag ? json_encode($value, JSON_UNESCAPED_UNICODE) : json_encode($value);
        }
        return $result;
    }

    /**
     * 解码， 默认是json
     * @param array $value
     * @return mixed
     */
    public static function deCode($value = array())
    {
        if (!is_array($value) || !is_object($value)) {
            $result = json_decode($value, true);
        }
        return $result;
    }

    /**
     * 设置主键
     * @param $pkId
     * @return mixed
     */
    public function setPkId($pkId)
    {
        return $this->pkId = $pkId;
    }

    /**
     * 获取主键，默认是id
     * @return mixed
     */
    public function getPkId()
    {
        return $this->pkId;
    }

    /**
     * 获取表名
     * @return mixed
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * 设置表名
     * @param $tableName
     * @return mixed
     */
    public function setTableName($tableName)
    {
        return $this->tableName = $tableName;
    }

    /**
     * 设置db对象
     * @param $db
     */
    public function setDb($db)
    {
        $this->db = $db;
    }

    /**
     * 获取db对象
     * @return Mysql|string
     */
    public function getDb()
    {
        assert($this->db, '数据库错误');
        return $this->db;
    }


    /**
     * 开启事务
     */
    public function trans_begin()
    {
        $this->getDb()->begin();
    }

    /**
     * 事务回滚
     */
    public function trans_rollback()
    {
        $this->getDb()->rollback();
    }

    /**
     * 事务提交
     */
    public function trans_commit()
    {
        $this->getDb()->commit();
    }


    /**
     * 允许传递字符串或者数组
     * $this->insert('INSERT INTO xxx (a, b) VALUES('1', '2')');
     * $this->insert(array('a'=>1, 'b'=>2));
     * @param array $data
     * @return unknown
     */
    public function insert($data = array())
    {
        if (is_string($data)) {
            $sql = $data;
        } else {
//            $columns = $this->getColumns();
            $values = array();
            foreach ($data as $k => $v) {
//                if (in_array($k, $columns)) {
                $values[$k] = "'{$v}'";
//                }
            }
            $columns = implode(', ', array_keys($values));
            $values = implode(', ', $values);
            $sql = 'INSERT INTO ' . $this->getTableName() . "({$columns})VALUES({$values})";
        }
        self::addSqlList($sql);
        //com_api_message($sql);
        return $this->getDb()->insert($sql, false);
    }

    /**
     * $data = array( array( 'title' => 'My title', 'name' => 'My Name', 'date' => 'My date'), array( 'title' => 'Another title', 'name' => 'Another Name', 'date' => 'Another date') );
     * $this->db->insert_batch($data);
     * @param array $data
     * @return unknown
     */
    public function insert_batch($data = array())
    {
        if (is_string($data)) {
            $sql = $data;
        } else {
//            $columns = $this->getColumns();
            $values = array();

            foreach ($data as $k => $v) {
                foreach ($v as $key => $value) {
//                    if (in_array($key, $columns)) {
                    $values[$k][$key] = "'{$value}'";
//                    }
                }
                $values[$k] = '(' . implode(', ', $values[$k]) . ')';
            }

            $columns = implode(', ', array_keys($data[0]));
            $values = implode(', ', $values);
            $sql = 'INSERT INTO ' . $this->getTableName() . "({$columns})VALUES{$values}";
        }
        self::addSqlList($sql);
        return $this->getDb()->insert($sql);
    }

    /**
     * 允许传递字符串或者数组
     * $this->update('update xxx set a = 1 where a = 2');
     * $this->update(array('a' => 1), array('where'=> 'a = 1', 'order' => 'id', 'limit' => '10'))
     *  $this->update(array('a' => 1), array('where'=> ['a = 1', 'b'=>3], 'order' => 'id', 'limit' => '10'))
     * @param $data
     * @param null $condition
     * @return bool
     */
    public function update($data, $condition = null)
    {

        if (is_string($data)) {
            $sql = $data;
        } else {
            $str = $this->parserUpdateSqlCondition($condition);
//            $columns = $this->getColumns();
            $values = array();
            foreach ($data as $k => $v) {
                if (is_numeric($k) && strpos($v, '=') !== false) {
                    $v = trim($v);
                    $values[] = preg_replace('/,$/', '', $v);
                } else {
//                if (in_array($k, $columns)) {
                    $values[] = "{$k} = '{$v}'";
//                }
                }

            }
            $values = implode(', ', $values);
            $sql = 'UPDATE ' . $this->getTableName() . ' SET ' . $values . ' ' . $str;
        }
        self::addSqlList($sql);
        //echo $sql;
        return $this->getDb()->execute($sql);
    }

    /**
     * 参考insert和update
     * @param $data
     * @param null $id
     * @return array|bool|unknown
     */
    public function save($data, $id = null)
    {
        if ($id) {
            $result = $this->existById($id);
            unset($data['id']);
            $result = empty($result) ? false : $this->update($data, array($this->getpkId() => $id));
        } else {
            $result = $this->insert($data);
        }
        return $result;
    }

    public static function getWhereConditionValue($condition, $name)
    {
        if (!empty($condition[$name])) {
            $id = $condition[$name];
        } elseif (!empty($condition['where']) && !empty($condition['where'][$name])) {
            $id = $condition['where'][$name];
        }
        return $id;
    }


    /**
     * 最后插入的id
     * @return int
     */
    public function lastInsertId()
    {
        return $this->getDb()->insert_id();
    }


    /**
     * 表格分页
     * @param int $pageNum
     * @param int $count
     * @param string $url
     * @return string
     */
    public function tablePage($pageNum = 0, $count = 0, $url = '')
    {
        $page = \Page:: getInstance();
        $param = $postion = $allpage = null;
        $page->setPageSize($pageNum, self::LIMIT, $postion, $allpage, $count);
        $this->pageNum = $postion;
        return $page->getPageReNew($pageNum, $allpage, $url, $param);
    }


    /**
     * 表格数据
     * @param null $condition
     * @param string $filter
     * @return array
     */
    public function getTableList($condition = null, $filter = '*')
    {
        $condition['limit'] = $this->pageNum;
        $result = $this->findAll($condition, $filter);
        return $result;
    }

    /**
     * 用于检测是否存在该数据
     * @param $id
     * @return array|unknown
     */
    public function existById($id)
    {
        return $this->find(array($this->getpkId() => $id), $this->getpkId());
    }

    /**
     * 通过主键查找id，一条数据
     * @param $id
     * @return array|unknown
     */
    public function findById($id)
    {
        return $this->find(array($this->getpkId() => $id));
    }

    /**
     * 清除列为空的数据
     * @param $data
     * @return mixed
     */
    public function clearEmptyColumns($data)
    {
        foreach ($data as $key => $value) {
            if (empty($value)) {
                unset($data[$key]);
            }
        }
        return $data;
    }


    /**
     * 查找一条数据
     * @param null $condition array( '表列名' => '查询条件' )
     * @param string $filter
     * @return array|unknown
     */
    public function find($condition = null, $filter = '*')
    {
        $key = $this->getSqlKey($condition, $filter, 'FIND');
        $sql = $this->getCacheSql($key);

        if (empty($sql)) {
            // TODO: Implement find() method.
            if (is_string($condition)) {
                $sql = $condition;
            } else {
                $sqlFilter = is_string($filter) ? $filter : (is_array($filter) ? implode(', ', $filter) : '*');
                $sql = 'SELECT ' . $sqlFilter . ' FROM ' . $this->getTableName();
                if (!empty($condition)) {
                    $sql .= $this->parserSqlCondition($condition);
                }
            }

            $fileCache = Cache_File::getInstance();
            $fileCache->set($key, $sql);
        }
        //debugShow($sql . "<br/>");
        self::addSqlList($sql);
        $result = $this->getDb()->getOne($sql);

        return empty($result) ? array() : $result;
    }

    /**
     * 查找所有数据
     * @param null $condition
     * @param string $filter
     * @return array
     */
    public function findAll($condition = null, $filter = '*', $column = false)
    {
        $key = $this->getSqlKey($condition, $filter, 'FINDALL');
        $sql = $this->getCacheSql($key);
        if (empty($sql)) {
            if (is_string($condition)) {
                $sql = $condition;
            } else {
                $sqlFilter = is_string($filter) ? $filter : (is_array($filter) ? implode(', ', $filter) : '*');
                $sql = 'SELECT ' . $sqlFilter . ' FROM ' . $this->getTableName();
                if (!empty($condition)) {
                    $sql .= $this->parserSqlCondition($condition);
                }
            }
            $fileCache = Cache_File::getInstance();
            $fileCache->set($key, $sql);
        }
        // TODO: Implement findAll() method.
//        debugShow($sql);
        self::addSqlList($sql);

        if ($column) {
            $result = $this->getDb()->getPairs($sql);
        } else {
            $result = $this->getDb()->getAll($sql);
        }

        return empty($result) ? array() : $result;
    }

    public function cleanCacheSql($key)
    {
        $fileCache = Cache_File::getInstance();
        return $fileCache->get($key);
    }

    /**
     * 把sql语句的查询条件缓存起来，
     * @param null $condition
     * @param string $filter
     * @param string $salt
     * @return string
     */
    public function getSqlKey($condition = null, $filter = '*', $salt = 'FIND')
    {
        $key = md5(self::enCode(array(
            'condition' => $condition,
            'tableName' => $this->getTableName(),
            'filter' => $filter,
            'salt' => $salt
        )));
        return $key;
    }

    /**
     * 缓存sql语句，减少解析时间
     * @param $key
     * @return mixed
     */
    public function getCacheSql($key)
    {
        $fileCache = Cache_File::getInstance();
        return $fileCache->get($key);
    }


    /**
     * 获取总的数量
     * @param null $condition
     * @return int|string
     */
    public function getCount($condition = null)
    {
        // TODO: Implement getCount() method.
        $key = $this->getSqlKey($condition, 'count(1)', 'COUNT');
        $sql = $this->getCacheSql($key);
        if (empty($sql)) {
            $sql = 'SELECT count(1) as count FROM ' . $this->getTableName();
            if (!empty($condition)) {
                $sql .= $this->parserSqlCondition($condition);
            }
            self::addSqlList($sql);
            $fileCache = Cache_File::getInstance();
            $fileCache->set($key, $sql);
        }
        //debugShow($sql);
        return $this->getDb()->getResult($sql);
    }

    /**
     * 获取某个字段的总和
     * @param $filer 必须填写,且必须是一个字段
     * @param null $condition
     * @return int|string
     */
    public function getSum($filer, $condition = null)
    {
        $sql = " SELECT sum({$filer}) as sum FROM " . $this->getTableName();
        if (!empty($condition)) {
            $sql .= $this->parserSqlCondition($condition);
        }
        self::addSqlList($sql);
        return $this->getDb()->getResult($sql);
    }

    /**
     * 设置过滤条件
     * 重写方法
     * 返回一个数组
     * return array(
     *      'where' => '',
     *      'group' => '',
     *      'having' => '',
     *      'order' => '',
     *      'limit' => '',
     * )
     * 具体传值方法，可参考 parserWhere。。。
     */
    public function search()
    {
        return array();
    }


    /**
     * 解析limit
     * 写法
     * parserLimit(0)
     * parserLimit(0， 10)
     * parserLimit([0， 10])
     * @param int $start
     * @param null $count
     * @return string
     */
    protected function parserLimit($start = null, $count = null)
    {
        if ($start === null) {
            return '';
        }
        if (is_array($start)) {
            $count = $start[1];
            $start = $start[0];
        } else {
            $count = $count ? $count : self::LIMIT;
        }
        return ' LIMIT ' . $start . ', ' . $count;
    }


    /**
     * 规则比较简单, 只用适用连续的AND语句， 且不带括号
     * pareserWhere(['a'=>111, 'b'=>'222']) => WHERE a = '111' AND b = '222'
     * pareserWhere(['a !='=>111, 'b >'=>'222']) => WHERE a != '111' AND b > '222'
     * pareserWhere(['a'=>111, 'b'=>[1, 2]]) => WHERE a = '111' AND b IN ('1', '2')
     * pareserWhere(['a'=>111, 'b NOT IN'=>[1, 2]]) => WHERE a = '111' AND b  NOT IN ('1', '2')
     * @param null $where
     * @return null|string
     */
    protected function pareserWhere($where = null)
    {
        if (empty($where)) {
            return '';
        }
        if (is_string($where)) {
            $result = preg_match('/WHERE/i', $where) != false ? $where : ' WHERE ' . $where;
        } elseif (is_array($where)) {
            $result = ' WHERE ' . implode(' AND ', $this->parserAndOfArray($where));
        }
        return $result;
    }

    /**
     * 可支持写法
     * 数组： parserOrder(['id1', 'id2'])
     * 数组： parserOrder(['id1 ASC', 'id2 DESC'])
     * 字符串 parserOrder('id1, id2 DESC')
     * @param null $order
     * @param string $sort
     * @return string
     */
    protected function parserOrder($order = null, $sort = 'DESC')
    {
        $order = $order ? $order : $this->pkId;
        $order = is_array($order) ? implode(', ', $order) : $order;
        if (preg_match('/DESC|ASC$/i', $order) == false) {
            $order .= ' ' . $sort;
        }
        return ' ORDER BY ' . $order;
    }

    /**
     * 支持写法
     * parserGroup('id')
     * @param null $group
     * @return string
     */
    protected function parserGroup($group = null)
    {
        if (empty($group)) {
            return '';
        }
        return ' GROUP BY ' . $group;
    }

    /**
     * 写法
     * parserHavering(['a'=>111, 'b'=>'222']) => HAVEING a = '111' AND b = '222'
     * parserHavering(['a !='=>111, 'b >'=>'222']) => HAVEING a != '111' AND b > '222'
     * parserHavering(['a'=>111, 'b'=>[1, 2]]) => HAVEING a = '111' AND b IN ('1', '2')
     * parserHavering(['a'=>111, 'b NOT IN'=>[1, 2]]) => HAVEING a = '111' AND b  NOT IN ('1', '2')
     * @param null $haveing
     * @return null|string
     */
    protected function parserHavering($haveing = null)
    {
        if (empty($haveing)) {
            return '';
        }
        if (is_string($haveing)) {
            $result = preg_match('/HAVING/i', $haveing) != false ? $haveing : ' HAVEING ' . $haveing;
        } elseif (is_array($haveing)) {
            $result = ' HAVEING ' . implode(' AND ', $this->parserAndOfArray($haveing));
        }
        return $result;
    }

    /**
     * 只解析成连续的 AND 语句
     * @param array $data
     * @return array
     */
    private function parserAndOfArray($data = array())
    {
        $whereDate = array();
        foreach ($data as $key => $value) {
            $key = trim($key);
            if (preg_match('/^([\w\.]+)\s*(.*)$/', $key, $result)) {

                $column = $result[1];
                $symbol = empty($result[2]) ? '=' : trim($result[2]);

                if (is_array($value)) {
                    $value = '( "' . implode('", "', $value) . '" )';
                    if (strripos($symbol, 'IN') === false) {
                        $symbol = 'IN';
                        $whereDate[] = $column . ' ' . $symbol . ' ' . $value;
                        continue;
                    }
                }

                $whereDate[] = $column . ' ' . $symbol . ' ' . (is_numeric($value) ? $value : "'{$value}'");
            }
        }
        return $whereDate;
    }

    /**
     * 简单解析sql条件
     * @param string $condition
     * @return string
     */
    protected function parserSqlCondition($condition = '')
    {
        if (empty($condition)) {
            return $condition;
        }
        if (is_string($condition)) {
            return $condition;
        }
        $group = $this->parserGroup($condition['group']);
        unset($condition['group']);
        $having = $this->parserHavering($condition['having']);
        unset($condition['having']);
        $order = $this->parserOrder($condition['order']);
        unset($condition['order']);
        $limit = $this->parserLimit($condition['limit']);
        unset($condition['limit']);
        if (empty($condition['where'])) {
            unset($condition['where']);
            $condition['where'] = $condition;
        }
        $where = $this->pareserWhere($condition['where']);
        return $where . $group . $having . $order . $limit;
    }

    /**
     * 简单解析Updatesql条件
     * @param string $condition
     * @return string
     */
    protected function parserUpdateSqlCondition($condition = '')
    {
        if (empty($condition)) {
            return $condition;
        }
        if (is_string($condition)) {
            return $condition;
        }
        $order = $this->parserOrder($condition['order']);
        unset($condition['order']);

        $limit = empty($condition['limit']) ? '' : ' LIMIT ' . $condition['limit'];
        unset($condition['limit']);

        if (empty($condition['where'])) {
            $condition['where'] = $condition;
        }
        $where = $this->pareserWhere($condition['where']);

        return $where . $order . $limit;
    }


    /**
     * 使用redis存储值， 把值作为字符串存储进去
     * 最简单的使用方式
     * 建议：如果存储二维数组，且数组长度少于200的使用 proxyModelSearchWithHash更合适
     * @param $key
     * @param $action
     * @param $arges
     * @param int $timeOut
     * @param int $defaultValue
     * @return array|bool|mixed|string
     */
    public function proxyModelSearchWithRedis($key, $action, $arges, $timeOut = 600, $defaultValue = -1)
    {
        //$this->getRedis()->delete($key);
        $result = $this->getRedis()->get($key);

        if ($result === false) {
            $result = call_user_func_array($action, $arges);
            $this->getRedis()->set($key, empty($result) ? $defaultValue : self::enCode($result), $timeOut);
        } elseif ($result == $defaultValue) {
            $result = array();
        } else {
            $result = self::deCode($result);

        }
        return $result;
    }

    /**
     * 使用redis hash存储值，请注意查询出的数组不能超过200长度，超出会报错，谨慎使用
     * 支持存储二维数组，使用二维数组存储的时，请把主键或者某个唯一值作为一维数组key
     * @param $key
     * @param $action
     * @param null $field
     * @param array $arges
     * @param int $timeOut
     * @return array|mixed
     */
    public function proxyModelSearchWithHash($key, $action, $field = null, $arges = array(), $timeOut = 600)
    {
        $hashCache = Cache_Hash::getInstance();
        $result = $hashCache->get($key, $field);
        if (empty($result) || empty($result[$field])) {
            $result = call_user_func_array($action, $arges);
            $hashCache->set($key, empty($result) ? -1 : self::enCode($result), $field, $timeOut);
        } elseif ($result == -1 || $result[$field] == -1) {
            $result = array();
        } else {
            if ($field) {
                $result = self::deCode($result[$field]);
            } else {
                $result = self::deCode($result);
            }
        }
        return $result;
    }

    /**
     * 组建hash索引
     * 该方法必须重写，按照自己的要求组件hash索引。用做hash索引的字段
     * 例如
     * public function buildHashIndex($data){
     *      $result = array();
     *      foreach($data as $v){
     *          $key = $v['id'].'_'.$v['pid'];
     *          $result[$key] = $v;
     *      }
     *      return $result;
     * }
     */
    public function buildHashIndex($data)
    {
        $result = array();
        foreach ($data as $v) {
            $key = $v[$this->getPkId()];
            $result[$key] = $v;
        }
        return $result;
    }

    /**
     * 需要返回特殊hashhit结果，请重写该方法
     * 必定返回一个非空数组，可通过判断 $result[0] == -1 确定是否查询是否为空
     * @return array|unknown
     */
    public function findHashHit()
    {
        $result = array();
        $argLength = func_num_args();
        $arges = func_get_args();
        $lastIndex = $argLength - 1;
        /**
         * 获取最后一个参数，hash字段
         */
        $field = empty($arges[$lastIndex]) ? 0 : $arges[$lastIndex];
        unset($arges[$lastIndex]);
        if (is_array($field) || is_object($field)) {

            /**
             * 如果最后一个参数是数组或对象， 则使用findAll查询
             */
            $result = call_user_func_array(array(
                $this, 'findAll'
            ), $arges);
            if (empty($result)) {
                $result[0] = -1;
            } else {
                /**
                 * 重建索引，必须重写，不重写直接报错
                 */
                $result = $this->buildHashIndex($result);
            }
        } else if (is_string($field) || is_numeric($field)) {
            /**
             * 如果最后一个参数是数组或对象， 则使用find查询,
             * 并返回结果
             * array($field => $field)
             */
            $result = call_user_func_array(array(
                $this, 'find',
            ), $arges);
            $result = array(
                $field => empty($result) ? -1 : $result
            );
        }
        return $result;
    }

    /**
     * 使用redis hash存储值，
     * 主要用途 存储热点数据，hash长度最多限制255，默认配置限制在200
     * 如果要修改长度，请先优化redis支持的hash长度
     * @param $key
     * @param null $field 普通情况下字符串，如果需要返回数组，请重写findHashHit方法
     * @param array $arges
     * @param bool $timeOut
     * @return array|mixed
     */
    public function proxyModelSearchWithRedisHashHit($key, $field = null, $arges = array(), $timeOut = false)
    {
        $hashCach = Cache_Hash::getInstance();
        //从缓存中读取
        $result = $hashCach->get($key, $field);
        $fields = array();
        if ($field) {
            $fields = is_array($field) ? $field : array($field);
            foreach ($fields as $field) {
                if (empty($result[$field])) {
                    $result = false;
                }
            }
        }

        if (empty($result)) {
            //从数据库里面读取
            $arges[] = $fields;
            $realResult = call_user_func_array(array(
                $this, 'findHashHit'
            ), $arges);
            //存入缓存
            $hashCach->addHotList($key, $realResult);
            if ($timeOut) {
                //设置缓存时间
                $hashCach->setHitListTimeOut($key, $timeOut);
            }
            $result = $realResult;
        } elseif (!empty($result[0]) && $result[0] == -1) {
            $result = array();
        }

        //返回要查询的字段
        $resultRetrun = array();
        if (empty($fields)) {
            $resultRetrun = $result;
        } else {
            foreach ($fields as $field) {
                if (empty($result[$field]) || $result[$field] == -1) {
                    $resultRetrun[$field] = array();
                } else if (is_string($result[$field])) {
                    $resultRetrun[$field] = self::deCode($result[$field]);
                } else {
                    $resultRetrun[$field] = $result[$field];
                }
            }
        }
        return $resultRetrun;
    }

    /**
     * 文件缓存
     * @param $key
     * @param $action
     * @param $arges
     * @param bool $timeOut
     * @return mixed
     */
    public function proxyModelSearchWithFileCahe($key, $action, $arges, $timeOut = false)
    {
        $fileCache = Cache_File::getInstance();
        return $fileCache->proxyModelSearch($key, $action, $arges, $timeOut);
    }

    /**
     * 适合保存 [id=> value , id=>value],这样形式的数组
     * @param $key
     * @param $value
     * @param $action   方法必须返回 [value => id, value => id]
     * @param $arges
     * @param bool $timeOut
     * @return mixed
     *      [ id => value ]
     */
    public function proModelSearchWithSortGather($key, $value, $action, $arges, $timeOut = false)
    {
        $sortGather = Cache_SortGather::getInstance();
        $result = $sortGather->getIndex($key, $value);
        if (empty($result)) {
            $result = call_user_func_array($action, $arges);
            if (!empty($result)) {
                if ($sortGather->set($key, $result) && $timeOut) {
                    $sortGather->setTimeOut($key, $timeOut);
                }
            }
        } else {
            $result = [
                $value => $result
            ];
        }
        if (!empty($result)) {
            $result = array_flip($result);
        }
        return $result;
    }


    /**
     * 文件和redis缓存共用
     * @param $key
     * @param $action
     * @param $arges
     * @param bool $timeOut
     * @return array|mixed
     */
    public function proxyModelSearchWithFileCaheAndRedis($key, $action, $arges, $timeOut = false)
    {
        $fileCache = Cache_File::getInstance();
        if ($timeOut) {
            $timeOut = $fileCache->timeOut($key, $timeOut) ? false : true;
        }
        $result = $fileCache->get($key);
        if (empty($result) || $timeOut) {
            $result = $this->getRedis()->get($key);
            if (empty($result)) {
                $result = call_user_func_array($action, $arges);
                $data = $result;
                $result = empty($result) ? -1 : self::enCode($result);
                $this->getRedis()->set($key, $result);
                $fileCache->set($key, $result);
            } else if ($result == -1) {
                $data = array();
            } else {
                $data = self::deCode($result);
            }
        } else if ($result == -1) {
            $data = array();
        } else {
            $data = self::deCode($result);
        }
        return $data;
    }

    /**
     * 刷新CDN
     */
    public static function refreshCDN($file, $flag = 'File')
    {
        if (!defined('CDN_OPEN')) {
            return;
        }
        $code = true;
        try {
            $aliyunClient = new AliyunClient();
            $cdn = new Cdn20141111RefreshObjectCachesRequest();
            $cdn->setObjectType($flag); // or Directory
            $cdn->setObjectPath($file);
            $data = (array)$aliyunClient->execute($cdn);
            if (!empty($data['code'])) {
                $code = false;
            }
        } catch (Exception $e) {
            $code = false;
        }
        if (!$code) {
            log_message('CDN ERROR', $flag . ' ["' . $file . '"] result: ' . json_encode($data) . ' : ', 'cdn');
        }
        return $code;
    }

    public function searchFilter($params)
    {
        $where = array();
        if (class_exists('Filter')) {
            $filter = new Filter();
            $where = $filter->filterParams($params);
//            $where['page'] = intval($params['page']);
        }
        return $where;
    }

    public function delete($condition)
    {
        if (is_string($condition)) {
            $sql = $condition;
        } else {
            if (empty($condition['where'])) {
                $condition['where'] = $condition;
            }
            $str = $this->pareserWhere($condition['where']);
            $sql = 'DELETE FROM ' . $this->getTableName() . $str;
        }

        self::addSqlList($sql);
        return $this->getDb()->execute($sql);
    }


}

