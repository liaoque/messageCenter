<?php
if (!defined('IN_BOOT')) {
	exit('Access Denied');
}

class AdModel {

	var $table = '';
	var $list_table = '';
	var $fields = array();
	var $_fields = array();
	var $_field = '';
	var $rule = array();
	var $limit = 10;
	var $order = 'order by id desc';
	var $url = '';
	var $manage = true;
	var $create = true;
	var $modify = true;
	var $delete = true;
	var $id = array();
	var $where = '';
	var $creatUrl = "";
	var $modifyUrl = "";
	var $deleteUrl = "";
	var $title = '';

	public function __construct() {
		global $segments;
		$this -> page = Page :: getInstance();
		$this -> url = '/' . $segments['mod'] . '/' . $segments['act'] . '/' . $segments['ac'];
		$this -> _fields = $this -> fields();
		$this -> form = new Form($this);


        $GLOBALS['tpl'] -> assign('hashkey', hashed($this -> url));
		$GLOBALS['tpl'] -> assign('ids', $this -> id);
		$GLOBALS['tpl'] -> assign('fields', $this -> _fields['field']);
		$GLOBALS['tpl'] -> assign('type', $this -> _fields['type']);
		$GLOBALS['tpl'] -> assign('disable', $this -> _fields['disable']);
		$GLOBALS['tpl'] -> assign('style', $this -> _fields['style']);
		$GLOBALS['tpl'] -> assign('tips', $this -> _fields['tips']);
		$GLOBALS['tpl'] -> assign('disshow', $this -> _fields['disshow']);
		$GLOBALS['tpl'] -> assign('class', $this -> _fields['class']);
		$GLOBALS['tpl'] -> assign('url', $this -> _fields['url']);
		$GLOBALS['tpl'] -> assign('d', $this -> _fields['data']);
		$GLOBALS['tpl'] -> assign('manage', $this -> manage);
		$GLOBALS['tpl'] -> assign('create', $this -> create);
		$GLOBALS['tpl'] -> assign('modify', $this -> modify);
		$GLOBALS['tpl'] -> assign('delete', $this -> delete);
		$GLOBALS['tpl'] -> assign('title', $this -> title);
		$GLOBALS['tpl'] -> assignByRef('obj', $this);
	}


	protected function actionRun(){
		global $segments;
		$action = $segments['ac'].'Func';
		if(method_exists($this, $action)){
			$this->$action();
		}else{
			$this->errorFunc();
		}
	}

	protected function ruleRun(){
		$rules = $this -> _fields['rule'];
		$this -> form -> set_rules($rules);
		if ($this -> form -> run() === false) {
			show_message($this -> form -> error_string(), '', 2);
		}
		return $this -> form -> get_validation_data();;
	}

	public function errorFunc()
    {
        $GLOBALS['tpl']->display('errors/error_404.tpl');
    }

    // 创建--即使不操作 也要继承重写--
	public function indexFunc() {

		$params = array();
		param_request(array('page' => 'INT'), '', $params, array('page' => 0));
		$page = $params['page'];
		unset($params['page']);

		if ($this -> where) {
			$this -> where = ' where ' . $this -> where;
		}
		$fd = $this ->_field ?$this -> _field : '*';
		$sql = "select count(0) as num from " . $this->list_table ? $this->list_table : $this ->table . ' ' . $this -> where ;
		$count = $GLOBALS['mysql'] -> getResult($sql);

		$this -> page -> setPageSize($page, $this -> limit, $postion, $allpage, $count);

		$str .= $this -> page -> getPageReNew($page, $allpage, $this -> url . "?", $param);
		$sql = "select $fd from " . $this -> table . "  " . $this -> where . "   $this->order limit $postion, $this->limit ";
		//echo $sql;die;

		$datas = $GLOBALS['mysql'] -> getAll($sql);

		$GLOBALS['tpl'] -> assign('str', $str);
		$GLOBALS['tpl'] -> assign('datas', $datas);
		$GLOBALS['tpl'] -> display("public/list.tpl");
	}



	public function deleteBefor() {
	}

	public function creatBefor(&$infos) {
	}
	public function modifyBefor(&$infos, $rdata) {
	}
	// 创建--即使不操作 也要继承重写--
	public function createFunc() {

		$params = array();
		param_post(array('key' => 'STRING', 'id' => 'INT'), '', $params, array('key' => '', 'id' => 0));
		if ($params['key']) {
			$rules = $this -> _fields['rule'];
			$this -> form -> set_rules($rules);
			if ($this -> form -> run() === false) {
				show_message($this -> form -> error_string(), '', 2);
			}
			$infos = $this -> form -> get_validation_data();


			if ($params['id']) {
				if (!$this -> manage || !$this -> modify) {
					show_message('访问错误', '', 2);
				}
				$sql = "select * from " . $this -> table . " where id=" . $params['id'];
				$rdata = $GLOBALS['mysql'] -> getOne($sql);

				$this -> modifyBefor($infos, $rdata);
			} else {
				if (!$this -> manage || !$this -> create) {
					show_message('访问错误', '', 2);
				}

				$this -> creatBefor($infos);
			}
			foreach ($infos as $k => $info) {
				$t .= $m . $k . "='{$info}'";
				$m = ",";
			}
			if ($params['id']) {
				if (!$this -> manage || !$this -> modify) {
					show_message('访问错误', '', 2);
				}
				$n = $this -> id['name'];
				$sql = "update  " . $this -> table . " set " . $t . " where $n=" . $params[$this -> id['name']];
				$r = $GLOBALS['mysql'] -> execute($sql);
			} else {
				$sql = "insert into  " . $this -> table . " set " . $t;

				$r = $GLOBALS['mysql'] -> insert($sql);
			}
			if ($r) {
				if ($params['id']) {
					$this -> modifyAfter($infos, $rdata);
				} else {
					$this -> creatAfter($infos);
				}
				show_message('操作成功！', $params['id'] ? $this -> modifyUrl : $this -> creatUrl);
			} else {
				show_message('操作失败！', '', 2);
			}
		}

		if (!$this -> manage || !$this -> create) {
			show_error('访问错误', 404);
		}
		$GLOBALS['tpl'] -> display("public/create.tpl");
	}

	public function creatAfter(&$infos) {
	}
	public function modifyAfter(&$infos, $rdata) {
	}

	public function deleteAfter() {
	}
	// 修改---即使不操作 也要继承重写--
	public function modifyFunc() {
		if (!$this -> manage || !$this -> modify) {
			show_error('访问错误', 404);
		}
		$params = array();

		param_request(array($this -> id['name'] => 'INT'), '', $params, array($this -> id['name'] => ""));
		if (!$params[$this -> id['name']]) {

			show_error('数据不存在', 404);
		}

		$n = $this -> id['name'];
		$sql = "select * from $this->table where $n='{$params[$this->id['name']]}'";

		$data = $GLOBALS['mysql'] -> getOne($sql);
		if (!$data) {

			show_error('数据不存在', 404);
		}

		$GLOBALS['tpl'] -> assign("data", $data);
		$GLOBALS['tpl'] -> display("public/create.tpl");
	}
	// 删除---即使不操作 也要继承重写--
	public function deleteFunc() {
		if (!$this -> manage || !$this -> delete) {
			show_error('访问错误', 404);
		}
		$params = array();
		param_request(array($this -> id['name'] => 'INT'), '', $params, array($this -> id['name'] => ""));
		if (!$params[$this -> id['name']]) {
			show_error('数据不存在', 404);
		}
		$this -> deleteBefor();
		$n = $this -> id['name'];
		$sql = "select * from $this->table where $n='{$params[$this->id['name']]}'";
		$data = $GLOBALS['mysql'] -> getOne($sql);
		if (!$data) {
			show_error('数据不存在', 404);
		}

		$sql = "delete from $this->table  where $n='{$params[$this->id['name']]}'";
		if ($GLOBALS['mysql'] -> execute($sql)) {
			$this -> deleteAfter($data);
			webheader($this -> deleteUrl);
		}
	}

	public function fields() {
		$str = array();
		foreach($this -> fields as $field => $rule) {
			list($f, $name) = explode("|", $field);
			$str['field'][$f] = $name;
			if (!$rule['disable']) {
				$str['rule'][$field] = $rule['rule'];
			}
			$str['type'][$f] = $rule['type'];
			$str['disable'][$f] = $rule['disable'];
			$str['disshow'][$f] = $rule['disshow'];
			$str['data'][$f] = $rule['data'];
			$str['style'][$f] = $rule['style'];
			$str['tips'][$f] = $rule['tips'];
			$str['class'][$f] = $rule['class'];
			$str['url'][$f] = $rule['url'];
		}

		return $str;
	}

	public function _fields($field, $v, $param = array()) {
		$m = "field_" . $field;
		if (method_exists($this, $m)) {
			$this -> $m($v, $param);
		}
	}

	public function _manage($param) {
		$m = 'manage';
		if (method_exists($this, $m)) {
			$this -> $m($param);
		}
	}
	public function _filter() {
		$params_list = explode('?', $_SERVER['REQUEST_URI']);
		if (count($params_list) == 2) {
			$get_list = explode('&', $params_list[1]);
		}
		if (is_array($get_list)) {
			foreach($get_list as $v1) {
				$key_value = explode('=', $v1);
				$param[@$key_value[0]] = urldecode(@$key_value[1]);
			}
		}
		$GLOBALS['tpl'] -> assign('filter_datas', $param);

		$m = 'filter';
		if (method_exists($this, $m)) {
			$this -> $m();
		}
	}
}
?>