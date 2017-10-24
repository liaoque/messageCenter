<?php
/**
 * mysql 基础类
 * uppdate： wanxiaokuo.1985@163.com   2010-11
 * 
 */
//数据库类
if(!defined('IN_BOOT')) {
	exit('Access Denied');
}
class Mysql1{
	var $link			= NULL;						// 数据库连接			
	var $slink			= NULL;						// 数据库连接			
	var $settings		= array();				//数据库配置信息
	
	public function __construct(){ //构造函数
		global $_SC;
		if(!$this->link){
		$this->link = $this->connect($_SC["db"]["host"], $_SC["db"]["username"], $_SC["db"]["password"], $_SC["db"]["dbname"], $_SC['dbcharset'], $_SC['pconnect'], false);
		}
		if(!$this->slink){
			$key = array_rand($_SC["sdb"]);
			$this->slink = $this->connect($_SC["sdb"][$key]["host"], $_SC["sdb"][$key]["username"], $_SC["sdb"][$key]["password"], $_SC["sdb"][$key]["dbname"], $_SC['dbcharset'], $_SC['pconnect'], false);
		}
		
	}
	
	//静态实例化
	static private  $objCore = '';
	static function getInstance() {
		if (self::$objCore == NULL) {
			self::$objCore = new self();
		}
		return self::$objCore;
	}
	
	
	/**
	 * mysql 连接
	 *
	 * @param string $dbhost    连接地址
	 * @param string $dbuser    用户名
	 * @param string $dbpw      用户密码
	 * @param string $dbname    数据库
	 * @param string $charset   编码
	 * @param boolean $pconnect 长连接
	 * @param boolean $halt     主从选择
	 * @return boolean
	 */
   private function connect($dbhost, $dbuser, $dbpw, $dbname, $charset= 'utf8', $pconnect=0, $halt = TRUE) {
   		//连接数据库
   	    if($pconnect) {//长连接
    		$_link = @mysql_pconnect($dbhost, $dbuser, $dbpw);
    	} else {//短连接
    		$_link = mysql_connect($dbhost, $dbuser, $dbpw, true );
    	}
		if(!$_link) {
			$halt && $this->halt($_link, '数据库连接失败!');
		}
		
		mysql_query("set names $charset", $_link);
       	//选择数据库
        if ($this->select_db($dbname, $_link) === false) {
            $halt && $this->halt($_link, '选择数据库失败!', $_link);
            return false;
        } else {
        	return $_link;
        }
   	}
    
    /**
     * sql query
     *
     * @param string $sql
     * @return boolean | last_id
     */
    public function query($sql, $_link) {
    	$query = mysql_query($sql, $_link);
		if(!$query) {
			$this->halt($_link, 'MySQL 请求错误!', $sql);
		}
		return $query;
    }

    /**
     * 添加方法
     *
     * @param string $sql
     * @return unknown
     */
    public function insert($sql) {
    	if($this->query($sql, $this->link)) {
    		return $this->insert_id($this->link);
    	} else {
    		return 0;
    	}
    }
    
    /**
     * 执行语句
     *
     * @param string $sql
     * @return object
     */
    public function execute($sql) {
    	$sth = $this->query($sql, $this->link);
    	return $sth;
    }
    
    /**
     * 循环使用方法
     *
     * @param object $sth
     * @return array
     */
    public function fetch($sth) {
    	return $this->fetch_array($sth, MYSQL_ASSOC);
    }
    
    /**
     * 以第一个字段为下标的数据
     *
     * @param string $sql
     * @return array
     */
    public function getAssoc($sql) {
    	 $rs = $this->query($sql, $this->slink);
        $data = array();
        while($result = mysql_fetch_array($rs, MYSQL_NUM)) {
        	$data[$result[0]] = $result[1];
		}
		 $this->free_result($rs);
        return $data;
    }
    
 	/**
 	 * 选择数据库
 	 *
 	 * @param string $dbname
 	 * @return db  resource
 	 */
	public function select_db($dbname, $_link) { //选择数据库
		return mysql_select_db($dbname, $_link);
	}
	
	/**
	 * 获取一行记录
	 *
	 * @param object $query
	 * @param cons $result_type
	 * @return array
	 */
    public function fetch_array($query, $result_type = MYSQL_ASSOC){//从结果集取得关联数组
        return mysql_fetch_array($query, $result_type);
    }
    
   
    /**
     * 获取所有数据记录
     *
     * @param string $sql
     * @return array
     */  
    public function getPairs($sql) {
    	$query = $this->query($sql, $this->slink);
        $data = array();
        while ($row = $this->fetch_array($query)) {
        	$key = current($row);
            $data[$key] = $row;
        }
        $this->free_result($query);
        return $data;   	
    }
    
    /**
     * 获取所有数据记录
     *
     * @param string $sql
     * @return array
     */
	public function getAll($sql){//
		$query = $this->query($sql, $this->slink);
		while ($value = $this->fetch_array($query)){
			$r[] = $value;
		}
		$this->free_result($query);
		return $r;
	}
	
	/**
	 * 获取一行结果
	 *
	 * @param unknown_type $sql
	 * @return unknown
	 */
	public function getOne($sql) {
		$rs = $this->query($sql, $this->slink);
		$result = $this->fetch_array($rs, MYSQL_ASSOC);
		$this->free_result($rs);
		return $result;
	}
	
	/**
	 * 获取一个返回值
	 *
	 * @param string $sql
	 * @return string|integer
	 */
	public function getResult($sql) {
		$rs = $this->query($sql, $this->slink);
		$result = $this->fetch_array($rs, MYSQL_NUM);
		$this->free_result($rs);
		return $result[0];
	}
	
	/**
	 * 获取一行记录
	 *
	 * @param string $sql
	 * @return unknown
	 */
	public function getRow($sql) {
		$rs = $this->query($sql, $this->slink);
		return $this->fetch_array($rs, MYSQL_ASSOC );
	}
	
	/**
	 * 获取上一插入产生的id
	 *
	 * @return integer
	 */
    public function insert_id($_link){//取得上一步 INSERT 操作产生的 ID 
        return mysql_insert_id($_link);
    }

    /*
     * 释放结果内存
     *
     * @param object $query
     * @return boolean
     */
    public function free_result($query){//释放结果内存
        return mysql_free_result($query);
    }
    
    /**
     * 关闭mysql
     *
     * @return boolean
     */
    public function close($_link){//关闭数据库
        return mysql_close($_link);
    }
    
    /**
     * mysql报错
     *
     * @return string
     */
	public function error($_link) {
		return (($_link) ? mysql_error($_link) : mysql_error());
	}
	
	/**
	 *  返回错误信息代码
	 *
	 * @return string
	 */
	public function errno($_link) {
		return intval(($_link) ? mysql_errno($_link) : mysql_errno());
	}

		/**
	 * 事务开启
	 *
	 */
	public function begin() {
		mysql_query("BEGIN", $this->link);
	}
	
	/**
	 * 事务失败滚回
	 *
	 */
	public function rollback() {
		mysql_query("ROLLBACK", $this->link);
	}
	
	/**
	 * 事务提交
	 *
	 */
	public function commit() {
		mysql_query("COMMIT", $this->link);
	}
	
	/**
	 * 事务释放
	 *
	 */
	public function end() {
		mysql_query("END", $this->link);
	}
	
	/**
	 * 显示错误信息
	 *
	 * @param string $message
	 * @param string $sql
	 */
	public function halt($_link, $message = '', $sql = '') {//显示错误信息
		$dberror = $this->error($_link);
		$dberrno = $this->errno($_link);
		log_message('MySQL Error', $message . ' : ' . $dberror . ' : '. $dberrno . ' [' . $sql . ']', 'mysql');
		return false;
	}
	
	public function __destruct() {
		$this->link = NULL;
		$this->slink = NULL;
	}
}
?>