<?php
if (!defined('IN_BOOT')) {
	exit('Access Denied');
} 
class Form extends BaseForm {
	private $c = '';
	public function __construct($c = '', $rules = array()) {
		$this -> c = $c;
		parent :: __construct($rules);
	} 

	/**
	 * 验证中文
	 * 
	 * @param string $str 
	 */
	function valid_chinese($str) {
		if (empty($str)) {
			return true;
		} 

		if (!preg_match('@^[\x{4e00}-\x{9fa5}]+$@u', $str)) {
			$this -> set_message('valid_chinese', '%s必须为中文');

			return false;
		} 

		return true;
	} 

	function valid_url($str) {
		if (empty($str)) {
			return true;
		} 

		$pattern = "/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i";
		if (!preg_match($pattern, $str)) {
			$this -> set_message('valid_url', '%s不是合法的URL');

			return false;
		} 

		return true;
	} 

	function valid_json($str) {
		if (empty($str)) {
			return true;
		} 

		$json = @json_decode($str);

		if (is_null($json)) {
			$this -> set_message('valid_json', '%s不是一个合法的JSON数据');

			return false;
		} 

		return true;
	} 
    
	// 检查必须是时间字符串,如果为空也返回正确,是否为空用require来判断
	function valid_timestr($str) {
		if (empty($str)) {
			return true;
		} 

		$ret = strtotime($str);
		if ($ret < 0 || $ret === false) {
			$this -> set_message('valid_timestr', '%s不是有效的时间');

			return false;
		} 

		return true;
	} 

	function valid_roles($str) {
		if (empty($str)) {
			return true;
		} 

		if (!$this -> permission -> isValidRoles($str)) {
			$this -> set_message('valid_roles', '%s不是有效的角色');

			return false;
		} 

		return true;
	} 

	function safe_password($str) {
		if (empty($str)) {
			return true;
		} 

		if (strlen($str) < 8) {
			$this -> set_message('safe_password', '%s长度最少为8位');

			return false;
		} 

		$level = 0;

		if (preg_match('@\d@', $str)) $level++;
		if (preg_match('@[a-z]@', $str)) $level++;
		if (preg_match('@[A-Z]@', $str)) $level++;
		if (preg_match('@[^0-9a-zA-Z]@', $str)) $level++;

		if ($level < 3) {
			$this -> set_message('safe_password',
				'您设置的%s太简单，密码必须包含数字、大小写字母、其它符号中的三种及以上'
				);

			return false;
		} 

		return true;
	} 

	function valid_ips($str) {
		if (empty($str)) {
			return true;
		} 

		$ips = explode(',', $str);

		foreach ($ips as $ip) {
			if ($this -> valid_ip($ip) === false) {
				$this -> set_message('valid_ips', '%s包含无效的IP');

				return false;
			} 
		} 

		return true;
	} 

	function limit_char($str, $val) {
		list($minv, $maxv) = explode(',', $val);
		$res = mb_strwidth($str);
		if ($res > $maxv || $res < $minv) {
			$this -> set_message('limit_char', "%s长度需要在{$minv}到{$maxv}之间");
			return false;
		} 
		return true;
	} 
    
	// 手机号码校验
	function valid_mobile($str) {
		if (empty($str)) {
			return true;
		} 
		($r = preg_match("/^(13|15|18|17)\d{9}$/", $str)) || $this -> set_message('valid_mobile', "%s({$str})不是合法的手机号");
		return !!$r;
	} 
    
	// 座机号码校验(未测试)
	function valid_phone($str) {
		if (empty($str)) {
			return true;
		} 
		($r = preg_match("/^(((d{3}))|(d{3}-))?((0d{2,3})|0d{2,3}-)?[1-9]d{6,8}$/", $str)) || $this -> set_message('valid_phone', "%s({$str})不是合法的座机号");
		return !!$r;
	} 

	function max_width($str, $val) {
		if (preg_match("/[^0-9]/", $val)) {
			return false;
		} 

		$res = (mb_strwidth($str) > $val) ? false : true;

		if (!$res) {
			$this -> set_message('max_width', "%s最大长度为{$val}");
		} 

		return $res;
	} 

	function gt($str, $n) {
		if (!$this -> numeric($str)) {
			return false;
		} 

		if ($str <= $n) {
			$this -> set_message('gt', '%s必须大于%s');

			return false;
		} 

		return true;
	} 

	function ge($str, $n) {
		if (!$this -> numeric($str)) {
			return false;
		} 

		if ($str < $n) {
			$this -> set_message('ge', '%s必须大于等于%s');

			return false;
		} 

		return true;
	} 

	function lt($str, $n) {
		if (!$this -> numeric($str)) {
			return false;
		} 

		if ($str >= $n) {
			$this -> set_message('lt', '%s必须小于%s');

			return false;
		} 

		return true;
	} 

	function le($str, $n) {
		if (!$this -> numeric($str)) {
			return false;
		} 

		if ($str > $n) {
			$this -> set_message('le', '%s必须小于等于%s');

			return false;
		} 

		return true;
	} 

	function get_validation_data() {
		$fields = array_keys($this -> _field_data);
		$data = array();

		foreach ($fields as $field) {
			if (isset($_POST[$field])) {
				$data[$field] = $_POST[$field];
			} 
		} 

		return $data;
	} 

	function reset_rules($group, $newRules) {
		$rules = &$this -> _config_rules[$group];
		if (! $rules) {
			return false;
		} 
		if (is_real_array($rules)) {
			foreach ($rules as &$item) {
				if (isset($newRules[$item['field']])) {
					$item['rule'] = $newRules[$item['field']];
				} 
			} 
		} else {
			foreach ($rules as $key => &$rule) {
				$field = array_shift(explode('|', $key));
				if (isset($newRules[$field])) {
					$rule = $newRules[$field];
				} 
			} 
		} 
		return true;
	} 

	function set_rules($field, $label = '', $rules = '') {
		if (is_array($field) && is_hashmap($field)) {
			$rules = array();
			foreach ($field as $name => $rule) {
				$names = explode('|', $name, 2);
				$rules[] = array('field' => $names[0],
					'label' => isset($names[1]) ? $names[1] : $names[0],
					'rules' => $rule,
					);
			} 
			$field = $rules;
		} 
		parent :: set_rules($field, $label, $rules);
	} 

	function line($line = '') {
		global $lang;
		$value = ($line == '' OR ! isset($lang[$line])) ? false : $lang[$line]; 
		// Because killer robots like unicorns!
		if ($value === false) {
			log_message('error', 'Could not find the language line "' . $line . '"');
		} 

		return $value;
	} 
    
	// --------------------------------------------------------------------
	/**
	 * Executes the Validation routines
	 * 
	 * @access private 
	 * @param array $ 
	 * @param array $ 
	 * @param mixed $ 
	 * @param integer $ 
	 * @return mixed 
	 */
	function _execute($row, $rules, $postdata = null, $cycles = 0) {
		// If the $_POST data is an array we will run a recursive call
		if (is_array($postdata)) {
			foreach ($postdata as $key => $val) {
				$this -> _execute($row, $rules, $val, $cycles);
				$cycles++;
			} 

			return;
		} 
		// --------------------------------------------------------------------
		// If the field is blank, but NOT required, no further tests are necessary
		$callback = false;
		if (! in_array('required', $rules) AND (is_null($postdata) || $postdata === '')) {
			// Before we bail out, does the rule contain a callback?
			if (preg_match("/(callback_\w+)/", implode(' ', $rules), $match)) {
				$callback = true;
				$rules = (array('1' => $match[1]));
			} else {
				return;
			} 
		} 
		// --------------------------------------------------------------------
		// Isset Test. Typically this rule will only apply to checkboxes.
		if (is_null($postdata) AND $callback == false) {
			if (in_array('isset', $rules, true) OR in_array('required', $rules)) {
				// Set the message type
				$type = (in_array('required', $rules)) ? 'required' : 'isset';

				if (! isset($this -> _error_messages[$type])) {
					if (false === ($line = $this -> line($type))) {
						$line = 'The field was not set';
					} 
				} else {
					$line = $this -> _error_messages[$type];
				} 
				// Build the error message
				$message = sprintf($line, $this -> _translate_fieldname($row['label'])); 
				// Save the error message
				$this -> _field_data[$row['field']]['error'] = $message;

				if (! isset($this -> _error_array[$row['field']])) {
					$this -> _error_array[$row['field']] = $message;
				} 
			} 

			return;
		} 
		// --------------------------------------------------------------------
		// Cycle through each rule and run it
		foreach ($rules As $rule) {
			$_in_array = false; 
			// We set the $postdata variable with the current data in our master array so that
			// each cycle of the loop is dealing with the processed data from the last cycle
			if ($row['is_array'] == true AND is_array($this -> _field_data[$row['field']]['postdata'])) {
				// We shouldn't need this safety, but just in case there isn't an array index
				// associated with this cycle we'll bail out
				if (! isset($this -> _field_data[$row['field']]['postdata'][$cycles])) {
					continue;
				} 

				$postdata = $this -> _field_data[$row['field']]['postdata'][$cycles];
				$_in_array = true;
			} else {
				$postdata = $this -> _field_data[$row['field']]['postdata'];
			} 
			// --------------------------------------------------------------------
			// Is the rule a callback?
			$callback = false;
			if (substr($rule, 0, 9) == 'callback_') {
				$rule = substr($rule, 9);
				$callback = true;

			}  
			// Strip the parameter (if exists) from the rule
			// Rules can contain a parameter: max_length[5]
			$param = false;
			if (preg_match("/(.*?)\[(.*)\]/", $rule, $match)) {
				$rule = $match[1];
				$param = $match[2];
			} 
			// Call the function that corresponds to the rule
			if ($callback === true) {
				// $obj = $o::getInstance();

				if (! method_exists($this -> c, $rule)) {
					show_error('无法调用方法' . $rule . '不存在');
				} 
				// Run the function and grab the result
				$result = $this -> c -> $rule($postdata, $param); 
				// Re-assign the result to the master data array
				if ($_in_array == true) {
					$this -> _field_data[$row['field']]['postdata'][$cycles] = (is_bool($result)) ? $postdata : $result;
				} else {
					$this -> _field_data[$row['field']]['postdata'] = (is_bool($result)) ? $postdata : $result;
				} 
				// If the field isn't required and we just processed a callback we'll move on...
				if (! in_array('required', $rules, true) AND $result !== false) {
					continue;
				} 
			} else {
				if (! method_exists($this, $rule)) {
					// If our own wrapper function doesn't exist we see if a native PHP function does.
					// Users can use any native PHP function call that has one param.
					if (function_exists($rule)) {
						$result = $rule($postdata);

						if ($_in_array == true) {
							$this -> _field_data[$row['field']]['postdata'][$cycles] = (is_bool($result)) ? $postdata : $result;
						} else {
							$this -> _field_data[$row['field']]['postdata'] = (is_bool($result)) ? $postdata : $result;
						} 
					} 

					continue;
				} 

				$result = $this -> $rule($postdata, $param);

				if ($_in_array == true) {
					$this -> _field_data[$row['field']]['postdata'][$cycles] = (is_bool($result)) ? $postdata : $result;
				} else {
					$this -> _field_data[$row['field']]['postdata'] = (is_bool($result)) ? $postdata : $result;
				} 
			} 
			// Did the rule test negatively?  If so, grab the error.
			if ($result === false) {
				if (! isset($this -> _error_messages[$rule])) {
					if (false === ($line = $this -> line($rule))) {
						$line = 'Unable to access an error message corresponding to your field name.';
					} 
				} else {
					$line = $this -> _error_messages[$rule];
				} 
				// Is the parameter we are inserting into the error message the name
				// of another field?  If so we need to grab its "field label"
				if (isset($this -> _field_data[$param]) AND isset($this -> _field_data[$param]['label'])) {
					$param = $this -> _translate_fieldname($this -> _field_data[$param]['label']);
				} 
				// Build the error message
				$message = sprintf($line, $this -> _translate_fieldname($row['label']), $param); 
				// Save the error message
				$this -> _field_data[$row['field']]['error'] = $message;

				if (! isset($this -> _error_array[$row['field']])) {
					$this -> _error_array[$row['field']] = $message;
				} 

				return;
			} 
		} 
	} 
} 
?>