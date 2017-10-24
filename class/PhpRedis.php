<?php
if(!defined('IN_BOOT')) {
	exit('Access Denied');
}
class PhpRedis extends redis{
	//var $phpRedis = NULL;
	static $dbs = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15');
	public function __construct(){ //构造函数
		global $_SC;
		try {
			$this->connect($_SC['redis_cachehost'], $_SC['redis_cacheport']);
			$this->auth($_SC['redis_passport']);
		} catch (Exception $e) {
			log_message('REDIS ERROR', $e->__toString(), 'redis');
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

}


?>
