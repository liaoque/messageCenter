<?php
/**
 * mysql 基础类
 * uppdate： wanxiaokuo.1985@163.com   2010-11
 *
 */
//数据库类
if (!defined('IN_BOOT')) {
    exit('Access Denied');
}

class Mysql
{
    var $link = NULL;                        // 数据库连接
    var $slink = NULL;                        // 数据库连接
    var $settings = array();                //数据库配置信息
    var $lives = 0;
    var $file = '';

    public function __construct()
    { //构造函数
        global $_SC;
        $this->file = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
        if (!$this->link) {
            try {
                $config = $_SC[TEMPLATE]["db"];
                $this->link = new PDO("mysql:host=" . $config["host"] . ";dbname=" . $config["dbname"], $config["username"], $config["password"], array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . $_SC['dbcharset']));
                $this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->link->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
            } catch (PDOException $e) {
                log_message('MySQL ERROR', $e->__toString(), 'mysql');
                $this->link = false;
            }
        }
        if (!$this->slink) {
            try {
                $config = $_SC[TEMPLATE]["sdb"];
//                var_dump($_SC);
                $key = array_rand($config);
                $config = $config[$key];
                $this->slink = new PDO("mysql:host=" . $config["host"] . ";dbname=" . $config["dbname"], $config["username"], $config["password"], array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . $_SC['dbcharset']));
                $this->slink->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->slink->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
            } catch (PDOException $e) {
                log_message('MySQL ERROR', $e->__toString(), 'mysql');
                $this->slink = false;
            }
        }

    }

    //静态实例化
    static private $objCore = '';

    static function getInstance()
    {
        if (self::$objCore == NULL) {
            self::$objCore = new self();
        }
        return self::$objCore;
    }


    public function query($sql)
    {

        $logtime = array_sum(explode(' ', microtime()));
        try {
            if ($_GET['sw']) {
                $logtime = array_sum(explode(' ', microtime()));
            }
            $rs = $this->slink->query($sql);
            if ($_GET['sw']) {
                //结束用时
                $logetime = array_sum(explode(' ', microtime()));
                $lives = number_format(($logetime - $logtime), 6);
                $this->lives += $lives;
                //写日志
                $string = $sql . "  RUNTIME: " . $lives . " secs ALLTIME：" . $this->lives . "<br>";
                echo $string;
            }
        } catch (PDOException $e) {
            if ($this->slink->errorCode() != '00000') {
                $error = $this->slink->errorInfo();
                log_message('MySQL ERROR', $error[2] . ' ["' . $sql . '"] : ' . $this->file, 'mysql');
                return false;
            }
        }
        $logetime = array_sum(explode(' ', microtime()));
        $lives = number_format(($logetime - $logtime), 6);
        if ($lives > 1) {
            log_message('SLOW MySQL', '[时间: ' . $lives . ' 秒 ]慢语句 [' . $sql . '] : ' . $this->file, 'mysql.slow');
        }
        return $rs;
    }

    /**
     * 添加方法
     *
     * @param string $sql
     * @return unknown
     */
    public function insert($sql, $insertId = true)
    {
        try {
            if ($_GET['sw']) {
                $logtime = array_sum(explode(' ', microtime()));
            }
            $r = $this->link->exec($sql);
            if ($_GET['sw']) {
                //结束用时
                $logetime = array_sum(explode(' ', microtime()));
                $lives = number_format(($logetime - $logtime), 6);
                $this->lives += $lives;
                //写日志
                $string = $sql . "  RUNTIME: " . $lives . " secs ALLTIME：" . $this->lives . "<br>";
                echo $string;
            }
        } catch (PDOException $e) {
            if ($this->link->errorCode() != '00000') {
                $error = $this->link->errorInfo();
                log_message('MySQL ERROR', $error[2] . ' ["' . $sql . '"] : ' . $this->file, 'mysql');
                return false;
            }
        }
        if ($insertId) {
            return $this->link->lastInsertId();
        }
        return $r;
    }

    /**
     * 执行语句
     *
     * @param string $sql
     * @return object
     */
    public function execute($sql)
    {
        try {
            if ($_GET['sw']) {
                $logtime = array_sum(explode(' ', microtime()));
            }
            $rs = $this->link->exec($sql);

            if ($_GET['sw']) {
                //结束用时
                $logetime = array_sum(explode(' ', microtime()));
                $lives = number_format(($logetime - $logtime), 6);
                $this->lives += $lives;
                //写日志
                $string = $sql . "  RUNTIME: " . $lives . " secs ALLTIME：" . $this->lives . "<br>";
                echo $string;
            }
            if ($rs !== false) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            if ($this->link->errorCode() != '00000') {
                $error = $this->link->errorInfo();
                log_message('MySQL info', ' ["' . $sql . '"] : ' . $this->file, 'info');
                log_message('MySQL ERROR', $error[2] . ' ["' . $sql . '"] : ' . $this->file, 'mysql');
                return false;
            }
        }
    }

    /**
     * 循环使用方法
     *
     * @param object $sth
     * @return array
     */
    public function fetch($rs)
    {
        return $rs->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * 以第一个字段为下标的数据
     *
     * @param string $sql
     * @return array
     */
    public function getAssoc($sql)
    {
        $data = array();
        $rs = $this->query($sql);
        if (!$rs) {
            return false;
        }
        while ($result = $rs->fetch(PDO::FETCH_NUM)) {
            $data[$result[0]] = $result[1];
        }
        return $data;
    }


    /**
     * 获取一行记录
     *
     * @param object $query
     * @param cons $result_type
     * @return array
     */
    public function fetch_array($sql)
    {//从结果集取得关联数组
        $rs = $this->query($sql);
        if (!$rs) {
            return false;
        }
        return $rs->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * 获取所有数据记录
     *
     * @param string $sql
     * @return array
     */
    public function getPairs($sql)
    {
        $data = array();
        $rs = $this->query($sql);
        if (!$rs) {
            return false;
        }
        while ($result = $rs->fetch(PDO::FETCH_ASSOC)) {
            $key = current($result);
            $data[$key] = $result;
        }
        return $data;
    }

    /**
     * 获取所有数据记录
     *
     * @param string $sql
     * @return array
     */
    public function getAll($sql)
    {//
        $data = array();
        $rs = $this->query($sql);
        if (!$rs) {
            return array();
        }
        while ($result = $rs->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $result;
        }
        return $data;
    }

    /**
     * 获取一行结果
     *
     * @param unknown_type $sql
     * @return unknown
     */
    public function getOne($sql)
    {
        $rs = $this->query($sql);
        if (!$rs) {
            return false;
        }
        return $rs->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * 获取一个返回值
     *
     * @param string $sql
     * @return string|integer
     */
    public function getResult($sql)
    {
        $data = array();
        $rs = $this->query($sql);
        if (!$rs) {
            return false;
        }
        $result = $rs->fetch(PDO::FETCH_NUM);
        return $result[0];
    }

    /**
     * 获取一行记录
     *
     * @param string $sql
     * @return unknown
     */
    public function getRow($sql)
    {
        $data = array();
        $rs = $this->query($sql);
        if (!$rs) {
            return false;
        }
        return $rs->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * 获取上一插入产生的id
     *
     * @return integer
     */
    public function insert_id()
    {//取得上一步 INSERT 操作产生的 ID
        return $this->link->lastinsertid();
    }


    /**
     * 事务开启
     *
     */
    public function begin()
    {
        $this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->link->beginTransaction();//开启事务

    }

    /**
     * 事务失败滚回
     *
     */
    public function rollback()
    {
        $this->link->rollBack();
    }

    /**
     * 事务提交
     *
     */
    public function commit()
    {
        $this->link->commit();
    }


    public function closePDO()
    {
        unset($this->link);
        unset($this->slink);
    }

    public function __destruct()
    {
        $this->link = NULL;
        $this->slink = NULL;
    }
}

?>