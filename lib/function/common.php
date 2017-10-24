<?php
function dump($var, $echo = true, $label = null, $strict = true)
{
    $label = ($label === null) ? '' : rtrim($label) . ' ';
    if (!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        } else {
            $output = $label . print_r($var, true);
        }
    } else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if (!extension_loaded('xdebug')) {
            $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        }
    }
    if ($echo) {
        echo($output);
        return null;
    } else
        return $output;
}

/**
 * 获取和设置配置参数 支持批量定义
 * @param string|array $name 配置变量
 * @param mixed $value 配置值
 * @param mixed $default 默认值
 * @return mixed
 */
function C($name = null, $value = null, $default = null)
{

    global $_SC;
    // 无参数时获取所有
    if (empty($name)) {
        return $_SC;
    }
    // 优先执行设置获取或赋值
    if (is_string($name)) {
        if (!strpos($name, '.')) {
            //$name = strtoupper($name);
            if (is_null($value))
                return isset($_SC[$name]) ? $_SC[$name] : $default;
            $_SC[$name] = $value;
            return null;
        }
        // 二维数组设置和获取支持
        $name = explode('.', $name);
        // $name[0] = strtoupper($name[0]);
        if (is_null($value))
            return isset($_SC[$name[0]][$name[1]]) ? $_SC[$name[0]][$name[1]] : $default;
        $_SC[$name[0]][$name[1]] = $value;
        return null;
    }
    // 批量设置
    if (is_array($name)) {
        //$_SC = array_merge($_SC, array_change_key_case($name, CASE_UPPER));
        $_SC = array_merge($_SC, $name);
        return null;
    }
    return null; // 避免非法参数
}

/**
 * Cookie 设置、获取、删除
 * @param string $name cookie名称
 * @param mixed $value cookie值
 * @param mixed $option cookie参数
 * @return mixed
 */
function cookie($name = '', $value = '', $option = null)
{
    // 默认设置

    $config = array(
        'prefix' => C('cookiepre.' . TEMPLATE), // cookie 名称前缀
        'expire' => 3600, // cookie 保存时间
        'path' => C('cookiepath'), // cookie 保存路径
        'domain' => C('cookiedomain'), // cookie 有效域名
        'secure' => $_SERVER['SERVER_PORT'] == 443 ? 1 : 0, //  cookie 启用安全传输
        'httponly' => C('COOKIE_HTTPONLY'), // httponly设置
    );
    // 参数设置(会覆盖黙认设置)
    if (!is_null($option)) {
        if (is_numeric($option))
            $option = array('expire' => $option);
        elseif (is_string($option))
            parse_str($option, $option);
        //$config     = array_merge($config, array_change_key_case($option));
        $config = array_merge($config, $option);
    }
    if (!empty($config['httponly'])) {
        ini_set("session.cookie_httponly", 1);
    }
    // 清除指定前缀的所有cookie
    if (is_null($name)) {
        if (empty($_COOKIE))
            return null;
        // 要删除的cookie前缀，不指定则删除config设置的指定前缀
        $prefix = empty($value) ? $config['prefix'] : $value;
        if (!empty($prefix)) {// 如果前缀为空字符串将不作处理直接返回
            foreach ($_COOKIE as $key => $val) {
                if (0 === stripos($key, $prefix)) {
                    setcookie($key, '', time() - 3600, $config['path'], $config['domain'], $config['secure'], $config['httponly']);
                    unset($_COOKIE[$key]);
                }
            }
        }
        return null;
    } elseif ('' === $name) {
        // 获取全部的cookie
        return $_COOKIE;
    }
    //$name = $config['prefix'] . str_replace('.', '_', $name);
    $name = $config['prefix'] . $name;
    if ('' === $value) {
        if (isset($_COOKIE[$name])) {
            $value = $_COOKIE[$name];
            if (0 === strpos($value, 'array:')) {
                $value = substr($value, 6);
                return array_map('urldecode', json_decode(MAGIC_QUOTES_GPC ? stripslashes($value) : $value, true));
            } else {
                return $value;
            }
        } else {
            return null;
        }
    } else {
        if (is_null($value)) {
            setcookie($name, '', time() - 3600, $config['path'], $config['domain'], $config['secure'], $config['httponly']);
            unset($_COOKIE[$name]); // 删除指定cookie
        } else {
            // 设置cookie
            if (is_array($value)) {
                $value = 'array:' . json_encode(array_map('urlencode', $value));
            }
            $expire = !empty($config['expire']) ? time() + intval($config['expire']) : 0;
            setcookie($name, $value, $expire, $config['path'], $config['domain'], $config['secure'], $config['httponly']);
            $_COOKIE[$name] = $value;
        }
    }
    return null;
}

/**
 * 获取输入参数 支持过滤和默认值
 * 使用方法:
 * <code>
 * I('id',0); 获取id参数 自动判断get或者post
 * I('post.name','','htmlspecialchars'); 获取$_POST['name']
 * I('get.'); 获取$_GET
 * </code>
 * @param string $name 变量的名称 支持指定类型
 * @param mixed $default 不存在的时候默认值
 * @param mixed $filter 参数过滤方法
 * @param mixed $datas 要获取的额外数据源
 * @return mixed
 */
function I($name, $default = '', $filter = null, $datas = null)
{
    if (strpos($name, '/')) { // 指定修饰符
        list($name, $type) = explode('/', $name, 2);
    }
    if (strpos($name, '.')) { // 指定参数来源
        list($method, $name) = explode('.', $name, 2);
    } else { // 默认为自动判断
        $method = 'param';
    }

    $query_string = remove_invisible_characters($_SERVER['QUERY_STRING']);
    $info = explode("&", $query_string);
    if ($info[0]) {
        foreach (explode('/', $info[0]) as $val) {
            if (strpos($val, '-') !== false) {
                list($p, $v) = explode('-', $val);
                $_GET[$p] = $v;
                continue;
            }
        }
    }

    switch (strtolower($method)) {
        case 'get'     :
            $input =& $_GET;
            break;
        case 'post'    :
            $input =& $_POST;
            break;
        case 'put'     :
            parse_str(file_get_contents('php://input'), $input);
            break;
        case 'param'   :
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    $input = $_POST;
                    break;
                case 'PUT':
                    parse_str(file_get_contents('php://input'), $input);
                    break;
                default:
                    $input = $_GET;
            }
            break;
        case 'path'    :
            $input = array();
            if (!empty($_SERVER['PATH_INFO'])) {
                $depr = '-';   //参数分割符 我们系统可以用? 和& 也可以用 - 这里我们用 -
                $input = explode($depr, trim($_SERVER['PATH_INFO'], $depr));
            }
            break;
        case 'request' :
            $input =& $_REQUEST;
            break;
        case 'session' :
            $input =& $_SESSION;
            break;
        case 'cookie'  :
            $input =& $_COOKIE;
            break;
        case 'server'  :
            $input =& $_SERVER;
            break;
        case 'globals' :
            $input =& $GLOBALS;
            break;
        case 'data'    :
            $input =& $datas;
            break;
        default:
            return NULL;
    }
    if ('' == $name) { // 获取全部变量
        $data = $input;
        $filters = isset($filter) ? $filter : 'htmlspecialchars,trim';   //默认的过滤方法
        if ($filters) {
            if (is_string($filters)) {
                $filters = explode(',', $filters);
            }
            foreach ($filters as $filter) {
                $data = array_map_recursive($filter, $data); // 参数过滤
            }
        }
    } elseif (isset($input[$name])) { // 取值操作
        $data = $input[$name];
        $filters = isset($filter) ? $filter : 'htmlspecialchars,trim';    //默认的过滤方法
        if ($filters) {
            if (is_string($filters)) {
                $filters = explode(',', $filters);
            } elseif (is_int($filters)) {
                $filters = array($filters);
            }

            foreach ($filters as $filter) {
                if (function_exists($filter)) {
                    $data = is_array($data) ? array_map_recursive($filter, $data) : $filter($data); // 参数过滤
                } elseif (0 === strpos($filter, '/')) {
                    // 支持正则验证
                    if (1 !== preg_match($filter, (string)$data)) {
                        return isset($default) ? $default : NULL;
                    }
                } else {
                    $data = filter_var($data, is_int($filter) ? $filter : filter_id($filter));
                    if (false === $data) {
                        return isset($default) ? $default : NULL;
                    }
                }
            }
        }
        if (!empty($type)) {
            switch (strtolower($type)) {
                case 's':   // 字符串
                    $data = (string)$data;
                    break;
                case 'a':   // 数组
                    $data = (array)$data;
                    break;
                case 'd':   // 数字
                    $data = (int)$data;
                    break;
                case 'f':   // 浮点
                    $data = (float)$data;
                    break;
                case 'b':   // 布尔
                    $data = (boolean)$data;
                    break;
            }
        }
    } else { // 变量默认值
        $data = isset($default) ? $default : NULL;
    }
    is_array($data) && array_walk_recursive($data, 'secure_filter');
    return $data;
}

function secure_filter(&$value)
{
    // TODO 其他安全过滤

    // 过滤查询特殊字符
    if (preg_match('/^(EXP|NEQ|GT|EGT|LT|ELT|OR|XOR|LIKE|NOTLIKE|NOT BETWEEN|NOTBETWEEN|BETWEEN|NOTIN|NOT IN|IN)$/i', $value)) {
        $value .= ' ';
    }
}

function debugShow($data)
{
    if (!empty($_GET['debug'])) {
        var_dump($data);
    }
}

function debugShowExit($data)
{
    if (!empty($_GET['debug'])) {
        var_dump($data);
    }
    exit();
}

function wh_size($_width, $_height, $zwidth, $zheight)
{
    $size = array();
    if ($zwidth <= $_width && $zheight <= $_height) {
        $size['width'] = $zwidth;
        $size['height'] = $zheight;
    } else {
        $r = $zwidth / $_width;
        $t = $zheight / $_height;
        if (intval($t * 100) > intval($r * 100)) {
            $size['height'] = $_height;
            $size['width'] = $zwidth / $t;
        } else {
            $size['width'] = $_width;
            $size['height'] = $zheight / $r;
        }
    }
    return $size;
}

function createSignGame($secretkey, $paramArr)
{
    $sign = $secretkey;
    ksort($paramArr);
    foreach ($paramArr as $key => $val) {
        if ($key != '' && $val != '') {
            $sign .= $key . $val;
        }
    }
    $sign = strtoupper(md5($sign));
    return $sign;
}

function createSign($paramArr)
{
    $sign = WEBKEY;
    ksort($paramArr);
//    var_dump($paramArr);
    foreach ($paramArr as $key => $val) {
        if ($key != '' && $val != '') {
            $sign .= $key . $val;
        }
    }
    $sign = strtoupper(md5($sign));
    return $sign;
}

function hashed($string)
{
    return hash('md5', $string);
}

function mkdirs($dir)
{
    if (!is_dir($dir)) {
        if (!mkdirs(dirname($dir))) {
            return false;
        }
        if (!mkdir($dir, 0777)) {
            return false;
        }
    }
    return true;
}

function show_message_static($mes, $url = '', $code = 1, $func = '', $timeHide = false)
{
    echo json_encode(array('mes' => $mes, 'code' => $code, 'url' => $url, 'func' => $func, 'timeHide' => $timeHide));
}

function show_message($mes, $url = '', $code = 1, $func = '', $timeHide = false)
{
    echo json_encode(array('mes' => $mes, 'code' => $code, 'url' => $url, 'func' => $func, 'timeHide' => $timeHide), JSON_UNESCAPED_UNICODE);
    exit;
}

function api_message($code = 1, $data)
{
    echo json_encode(array('error_code' => $code, 'data' => $data));
    exit;
}

//
function com_api_message($mes, $url = '', $code = 1)
{
	echo json_encode(array('code' => $code, 'url' => $url, 'mes' => $mes));
	exit;
}

function show_message_jsonCallBack($mes, $url = '', $code = 1, $func = '', $timeHide = false)
{
    if ($_GET['callback']) {
        echo $_GET['callback'] . '(' . json_encode(array('mes' => $mes, 'code' => $code, 'url' => $url, 'func' => $func, 'timeHide' => $timeHide), JSON_UNESCAPED_UNICODE) . ')';
    } else {
        show_message($mes, $url, $code, $func, $timeHide);
    }
    exit;
}

function log_message($type, $var, $file = '')
{
    $dir = ROOT . '/log/';
    if (!$file) {
        $dir .= date("Y") . '/' . date("m") . '/' . date('d');
        mkdirs($dir);
        $file = $dir . '/' . date("Ymd") . ".log";
    } else {
        $file = $dir . TEMPLATE . '_' . $file . '.log';
    }

    @ $sh = fopen($file, "a");
    $var = "[$type] " . date('Y-m-d H:i:s') . ' : ' . $var . "\n";
    @ fwrite($sh, $var, strlen($var));
    @  fclose($sh);
}

function tpl_init()
{
    $tpl = new Smarty();

    $tpl->template_dir = ROOT . '/' . TEMPLATE . '/tpl';
    $tpl->compile_dir = ROOT . '/' . TEMPLATE . '/var/template_r';
    $tpl->left_delimiter = '{';
    $tpl->right_delimiter = '}';
    return $tpl;
}

/**
 * SQL 过滤函数
 */
function saddslashes($string)
{
    if (is_array($string)) {
        foreach ($string as $key => $val) {
            $string[$key] = saddslashes($val);
        }
    } else {
        $string = trim(addslashes($string));
    }
    return $string;
}

function flag_table($uid, $str, $_flag = 1)
{
    $muid = md5($uid);
    if ($_flag == 1) {
        $flag = substr($muid, 31, 1);
    }
    if ($_flag == 2) {
        $flag = substr($muid, 31, 1) . '_' . substr($muid, 30, 1);
    }
    return $str . $flag;
}

function flag_str($str, $num = 2)
{
    $arr = array();
    $ds = md5($str);
    $arr['md5'] = $ds;
    $arr['dir'] = substr($ds, 30, $num);
    $arr['dirn'] = substr($ds, 28, $num);
    $arr['file'] = $ds . ".log";
    return $arr;
}

// 10个分表以下的取分表名的方法，默认为4个分表
function flag_table_little($uid, $str, $total = 4)
{
    $muid = $uid % $total;
    return $str . $muid;
}

// 自动加载
function fu_autoload($class)
{
    $dir = ROOT . '/class/';
    if (strpos($class, '_') === false) {
        $filename = $dir . $class . '.php';
        if (file_exists($filename)) {
            include $filename;
        }
    } else {
        $infos = explode('_', $class);
        $filename = $dir . implode('/', $infos) . ".php";
        if (file_exists($filename)) {
            include $filename;
        }
    }
}

// 自动加载
function class_autoload($class = null)
{
    $dir = getcwd() . '/class/';

    if (strpos($class, '_') === false) {
        $filename = $dir . $class . '.php';
        if (file_exists($filename)) {
            include $filename;
        }
    } else {
        $infos = explode('_', $class);
        $filename = $dir . implode('/', $infos) . ".php";
        if (file_exists($filename)) {
            include $filename;
        }
    }
}


function webheader($url)
{
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: $url");
    exit();
}

/**
 * 写cookie
 */
function ssetcookie($var, $value, $life = 0)
{
    global $_SC;
    $nowtime = time();
    $life = $life ? ($nowtime + $life) : ($nowtime + $_SC['cookietime']);
    setcookie($_SC['cookiepre'][TEMPLATE] . $var, $value, $life, $_SC['cookiepath'], $_SC['cookiedomain'], $_SERVER['SERVER_PORT'] == 443 ? 1 : 0);
}

/**
 * 写cookie
 */
function qssetcookie($pre, $var, $value, $life = 0)
{
    global $_SC;
    $nowtime = time();
    $life = $life ? ($nowtime + $life) : ($nowtime + $_SC['cookietime']);
    setcookie($pre . $var, $value, $life, $_SC['cookiepath'], $_SC['cookiedomain'], $_SERVER['SERVER_PORT'] == 443 ? 1 : 0);
}

/**
 * 字符串解密加密
 */
function authcode($string, $operation = 'DECODE')
{
    $key = WEBKEY;

    if ($operation == 'DECODE') {
        $string = base64_decode($string);
    }
    $string_len = strlen($string);

    for ($j = $i = 0; $i < $string_len; $i++) {
        $key{$j} ? $j : $j = 0;
        $str .= $string{$i} ^ $key{$j};
        $j++;
    }
    if ($operation != 'DECODE') {
        $str = str_replace('=', '', base64_encode($str));
    }
    return $str;
}

// 检查邮箱格式
function is_email($email)
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    if (6 > utf8_strlen($email) || 64 < utf8_strlen($email)) {
        return false;
    }
    return true;
}

function is_url($url)
{
    return 'http://' == substr($url, 0, 7);
}

// 检查手机格式
function is_phone($phone)
{
    if (!preg_match("/^1[34578][0-9]{9}$/", $phone)) {
        return false;
    }
    return true;
}

// 检查身份证格式
function is_idCard($vStr)
{
    $vCity = array('11', '12', '13', '14', '15', '21', '22',
        '23', '31', '32', '33', '34', '35', '36',
        '37', '41', '42', '43', '44', '45', '46',
        '50', '51', '52', '53', '54', '61', '62',
        '63', '64', '65', '71', '81', '82', '91'
    );

    if (!preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $vStr)) return false;

    if (!in_array(substr($vStr, 0, 2), $vCity)) return false;

    $vStr = preg_replace('/[xX]$/i', 'a', $vStr);
    $vLength = strlen($vStr);

    if ($vLength == 18) {
        $vBirthday = substr($vStr, 6, 4) . '-' . substr($vStr, 10, 2) . '-' . substr($vStr, 12, 2);
    } else {
        $vBirthday = '19' . substr($vStr, 6, 2) . '-' . substr($vStr, 8, 2) . '-' . substr($vStr, 10, 2);
    }

    if (date('Y-m-d', strtotime($vBirthday)) != $vBirthday) return false;
    if ($vLength == 18) {
        $vSum = 0;

        for ($i = 17; $i >= 0; $i--) {
            $vSubStr = substr($vStr, 17 - $i, 1);
            $vSum += (pow(2, $i) % 11) * (($vSubStr == 'a') ? 10 : intval($vSubStr, 11));
        }

        if ($vSum % 11 != 1) return false;
    }

    return true;
}

/**
 * 过滤关键字
 *
 * @param unknown_type $info
 * @param unknown_type $infoback
 * @return unknown
 */
function isWordMask($info, $infoback = false)
{
    global $_SC;
    $maskfalg = true;
    $maskword = file(ROOT . "/lib/mask_word.txt");
    if ($infoback) {
        foreach ($maskword as $v) {
            if (strpos($info, trim($v)) !== false) {
                $keyw = utf8_strlen(trim($v));
                $str = "";
                for ($i = 0; $i < $keyw; $i++) {
                    $str .= "*";
                }
                $info = str_replace(trim($v), $str, $info);
            }
        }
        return $info;
        exit;
    } else {
        foreach ($maskword as $v) {
            if (strpos($info, trim($v)) !== false) {
                $maskfalg = false;
                break;
            }
        }
        return $maskfalg;
        exit;
    }
}

// utf_8 文字裁字
function utf8_substr($str, $start, $length)
{
    return mb_substr($str, $start, $length, 'UTF-8');
}

// utf_8 文字计数
function utf8_strlen($str)
{
    return mb_strlen($str, 'UTF-8');
}

// 获取在线IP
function getonlineip()
{
    if ($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"]) {
        $ip = $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"];
    } elseif ($HTTP_SERVER_VARS["HTTP_CLIENT_IP"]) {
        $ip = $HTTP_SERVER_VARS["HTTP_CLIENT_IP"];
    } elseif ($HTTP_SERVER_VARS["REMOTE_ADDR"]) {
        $ip = $HTTP_SERVER_VARS["REMOTE_ADDR"];
    } elseif (getenv("HTTP_X_FORWARDED_FOR")) {
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    } elseif (getenv("HTTP_CLIENT_IP")) {
        $ip = getenv("HTTP_CLIENT_IP");
    } elseif (getenv("REMOTE_ADDR")) {
        $ip = getenv("REMOTE_ADDR");
    } else {
        $ip = "Unknown";
    }
    return $ip;
}

//通过在线IP获取省市地理位置
function getIpLookup($ip = '')
{
    if (empty($ip)) {
        $ip = GetIp();
    }
    $res = @file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=' . $ip);
    if (empty($res)) {
        return false;
    }
    $jsonMatches = array();
    preg_match('#\{.+?\}#', $res, $jsonMatches);
    if (!isset($jsonMatches[0])) {
        return false;
    }
    $json = json_decode($jsonMatches[0], true);
    if (isset($json['ret']) && $json['ret'] == 1) {
        $json['ip'] = $ip;
        unset($json['ret']);
    } else {
        return false;
    }
    return $json;
}

function getIP()
{
    if (isset($_SERVER)) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $realip = $_SERVER['REMOTE_ADDR'];
        }
    } else {
        if (getenv("HTTP_X_FORWARDED_FOR")) {
            $realip = getenv("HTTP_X_FORWARDED_FOR");
        } elseif (getenv("HTTP_CLIENT_IP")) {
            $realip = getenv("HTTP_CLIENT_IP");
        } else {
            $realip = getenv("REMOTE_ADDR");
        }
    }
    return $realip;
}

function generateHtmlByCURL($url)
{
    $ch = curl_init();
    $timeout = 1000;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    $handles = curl_exec($ch);
    curl_close($ch);
    return $handles;
}

function show_error($message, $status_code = 500, $template = 'error_general')
{
    $h = set_status_header($status_code);
    $message = implode(' ', (!is_array($message)) ? array($message) : $message);

    $GLOBALS['tpl']->assign('heading', $h);
    $GLOBALS['tpl']->assign('message', $message);
    if ($status_code == 404) {
        $GLOBALS['tpl']->display("errors/error_404.tpl");
    } else {
        $GLOBALS['tpl']->display("errors/$template.tpl");
    }

    exit;
}

function assert_show_error($file, $line, $code, $desc = null)
{
    if ($_GET['errorMsg']) {
        var_dump($file, $line, $code, $desc);
    }
    show_error($desc);
}


function set_status_header($code = 200, $text = '')
{
    $stati = array(200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',

        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',

        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',

        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported'
    );

    if ($code == '' OR !is_numeric($code)) {
        show_error('Status codes must be numeric', 500);
    }

    if (isset($stati[$code]) AND $text == '') {
        $text = $stati[$code];
    }

    if ($text == '') {
        show_error('No status text available.  Please check your status code number or supply your own message text.', 500);
    }

    $server_protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : false;

    if (substr(php_sapi_name(), 0, 3) == 'cgi') {
        return "Status: {$code} {$text}";
    } elseif ($server_protocol == 'HTTP/1.1' OR $server_protocol == 'HTTP/1.0') {
        return $server_protocol . " {$code} {$text}";
    } else {
        return "HTTP/1.1 {$code} {$text}";
    }
}

function _explode_segments($str)
{
    $default_name = array('mod' => 'index', 'act' => 'index', 'ac' => 'index');
    $sfile = '';
    $info = explode("&", $str);
    if ($info[0]) {
        $sfile = $info[0];
        //if ($info[0] == 'index.php') {
        //  $segments[0] = $default_name['mod'];
        // } else {
        foreach (explode('/', $info[0]) as $val) {
            if (strpos($val, '-') !== false) {
                list($p, $v) = explode('-', $val);
                $_GET[$p] = $v;
                continue;
            }
            // Filter segments for security
            $val = trim(_filter_uri($val));
            if ($val != '') {
                $segments[] = $val;
            }
            // unset($_GET[$val]);
            // }
        }
    }

    $default_name['mod'] = $segments[0] ? $segments[0] : $default_name['mod'];
    $default_name['sfile'] = $sfile;
    $default_name['act'] = $segments[1] ? $segments[1] : $default_name['act'];
    $default_name['ac'] = $segments[2] ? $segments[2] : $default_name['ac'];
    $default_name['path'] = '/' . $default_name['mod'] . '/' . $default_name['act'] . '/' . $default_name['ac'];

    return $default_name;
}

function _filter_uri($str)
{
    // Convert programatic characters to entities
    $bad = array('$', '(', ')', '%28', '%29');
    $good = array('&#36;', '&#40;', '&#41;', '&#40;', '&#41;');

    return str_replace($bad, $good, $str);
}

function remove_invisible_characters($str, $url_encoded = true)
{
    $non_displayables = array();
    // every control character except newline (dec 10)
    // carriage return (dec 13), and horizontal tab (dec 09)
    if ($url_encoded) {
        $non_displayables[] = '/%0[0-8bcef]/'; // url encoded 00-08, 11, 12, 14, 15
        $non_displayables[] = '/%1[0-9a-f]/'; // url encoded 16-31
    }

    $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S'; // 00-08, 11, 12, 14-31, 127

    do {
        $str = preg_replace($non_displayables, '', $str, -1, $count);
    } while ($count);

    return $str;
}

// 生成四位随字母加数字字符串
function fourRandomCode()
{
    $chars_array = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
    $charsLen = count($chars_array) - 1;
    $outputStr = "";
    for ($i = 0; $i < 4; $i++) {
        $outputStr .= $chars_array[mt_rand(0, $charsLen)];
    }

    return $outputStr;
}

/** 生成指定长度随机数字字符串
 * @param $length
 * @return string
 */
function ranNumCode($length)
{
    $chars_array = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
    $charsLen = count($chars_array) - 1;
    $outputStr = "";
    for ($i = 0; $i < $length; $i++) {
        $outputStr .= $chars_array[mt_rand(0, $charsLen)];
    }
    return $outputStr;
}

/**生成指定长度随机字母字符串
 * @param $length
 * @return string
 */
function ranCharCode($length)
{
    $chars_array = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
    $charsLen = count($chars_array) - 1;
    $outputStr = "";
    for ($i = 0; $i < $length; $i++) {
        $outputStr .= $chars_array[mt_rand(0, $charsLen)];
    }
    return $outputStr;
}

// 删除字符串中所有空格
function trimall($str)
{
    $qian = array(" ", "　", "\t", "\n", "\r");
    $hou = array("", "", "", "", "");
    return str_replace($qian, $hou, $str);
}

// 截取中文字符串
function m_substr($string, $len)
{
    if (mb_strlen($string) > $len) {
        return mb_substr($string, 0, $len, 'utf-8') . "...";
    } else {
        return $string;
    }
}

// 邮件发送模版
function sendEmailModel($username, $title, $code, $url)
{
    global $_SC;
    /*$sql = "select src from kxwadmin.img where flag=19";
    $img = $GLOBALS['mysql']->getResult($sql);*/

    return "<div style='background: #f4f4f3 none repeat scroll 0 0; height: 600px;padding-left: 10px;padding-top: 40px;width: 100%;'>
                <div style='border: 1px solid #aaa;height: 456px;margin-bottom: 33px;width: 600px;'>
                    <div style='background: #fff none repeat scroll 0 0; color: #000; height: 225px; padding: 25px 25px 0;'>
                        <div style='overflow: hidden'>
                            <img src='{$_SC['imgpath']}email-bg.png' style='float:left;margin:26px 0;' />
                            <h2 style='float: left; padding-left:4px'>{$title}</h2>
                        </div>
                        <p style='font-size: 14px;line-height: 1em;margin-bottom: 10px;margin-top: 0;text-align: left;'><span style='color: #f40;'>{$username}</span>，您好！</p>
                        <p style='font-size: 16px;line-height: 1em;margin-top: 0;text-align: left;'>您的{$title}为：</p>
                        <span style='color: #f40;display: block;font-size: 18px;line-height: 56px;'>{$code}</span>
                        <p style='font-size: 14px;line-height: 1em;margin-bottom: 10px;margin-top: 0;text-align: left;'>30分钟内输入有效。</p>
                    </div>
                    <div style='background: #e7e7e7 none repeat scroll 0 0;height: 181px;padding: 25px 0 0 25px;'>
                        <div style='height: 81px;margin-bottom: 32px;width: 290px;'><img src='{$_SC['imgpath']}email-logo.png' /></div>
                        <p style='color: #a9a9a9;font-size: 12px;line-height: 1em;margin-bottom: 14px;margin-top: 0;text-align: left;'>如果您在使用中有任何的疑问和建议，欢迎联系您的 <a href='{$url}' style='color: #f40;text-decoration: none;'>专属客服</a></p>
                        <p style='color: #a9a9a9;font-size: 12px;line-height: 1em;margin-bottom: 14px;margin-top: 0;text-align: left;'>苏州游创空间网络科技有限公司</p>
                    </div>
                </div>
                <p style='color: #a9a9a9;font-size: 12px;line-height: 1.5em;margin-top: 0;text-align: left;'>联系电话：0512-68838882    QQ：545544360</p>
                <p style='color: #a9a9a9;font-size: 12px;line-height: 1.5em;margin-top: 0;text-align: left;'>这是一封自动发送的邮件，请勿回复</p>
        </div>";
}

// 邮件发送模版
function sendEmailModelWeb($username, $title, $code, $url)
{
    global $_SC;
    /* $sql = "select src from kxwadmin.img where flag=19";
     $img = $GLOBALS['mysql']->getResult($sql);*/

    return "<div style='background: #f4f4f3 none repeat scroll 0 0; height: 600px;padding-left: 10px;padding-top: 40px;width: 100%;'>
                <div style='border: 1px solid #aaa;height: 456px;margin-bottom: 33px;width: 600px;'>
                    <div style='background: #fff none repeat scroll 0 0; color: #000; height: 225px; padding: 25px 25px 0;'>
                        <div style='overflow: hidden'>
                            <img src='{$_SC['imgpath']}email-bg.png' style='float:left;margin:26px 0;' />
                            <h2 style='float: left; padding-left:4px'>{$title}</h2>
                        </div>
                        <p style='font-size: 14px;line-height: 1em;margin-bottom: 10px;margin-top: 0;text-align: left;'><span style='color: #f40;'>{$username}</span>，您好！</p>
                        <p style='font-size: 16px;line-height: 1em;margin-top: 0;text-align: left;'>您的{$title}为：</p>
                        <span style='color: #f40;display: block;font-size: 18px;line-height: 56px;'>{$code}</span>
                        <p style='font-size: 14px;line-height: 1em;margin-bottom: 10px;margin-top: 0;text-align: left;'>30分钟内输入有效。</p>
                    </div>
                    <div style='background: #e7e7e7 none repeat scroll 0 0;height: 181px;padding: 25px 0 0 25px;'>
                        <div style='height: 81px;margin-bottom: 32px;width: 290px;'><img src='{$_SC['imgpath']}email-logo.png' /></div>
                        <p style='color: #a9a9a9;font-size: 12px;line-height: 1em;margin-bottom: 14px;margin-top: 0;text-align: left;'>如果您在游戏过程中有任何的疑问和建议，欢迎联系<a href='{$url}' style='color: #f40;text-decoration: none;'>客服</a></p>
                        <p style='color: #a9a9a9;font-size: 12px;line-height: 1em;margin-bottom: 14px;margin-top: 0;text-align: left;'>Copyright @ 2015 - 2015 kxwan.com All Rights Reserved</p>
                    </div>
                </div>
                <p style='color: #a9a9a9;font-size: 12px;line-height: 1.5em;margin-top: 0;text-align: left;'>这是一封自动发送的邮件，请勿回复</p>
        </div>";
}

/*
 * UTF-8网页
 * 反转义JS的escape()方法转义的字符串
 * */
function unescape($str)
{
    $ret = '';
    $len = strlen($str);
    for ($i = 0; $i < $len; $i++) {
        if ($str[$i] == '%' && $str[$i + 1] == 'u') {
            $val = hexdec(substr($str, $i + 2, 4));
            if ($val < 0x7f) $ret .= chr($val);
            else if ($val < 0x800) $ret .= chr(0xc0 | ($val >> 6)) . chr(0x80 | ($val & 0x3f));
            else $ret .= chr(0xe0 | ($val >> 12)) . chr(0x80 | (($val >> 6) & 0x3f)) . chr(0x80 | ($val & 0x3f));
            $i += 5;
        } else if ($str[$i] == '%') {
            $ret .= urldecode(substr($str, $i, 3));
            $i += 2;
        } else $ret .= $str[$i];
    }
    return $ret;
}


function get_zip_originalsize($filename, $path)
{
    //先判断待解压的文件是否存在
    if (!file_exists($filename)) {
        //die("文件 $filename 不存在！");
        return false;
    }
    //打开压缩包
    $resource = zip_open($filename);
    $i = 1;
    $files = array();

    //遍历读取压缩包里面的一个个文件
    while ($dir_resource = zip_read($resource)) {
        //如果能打开则继续
        if (zip_entry_open($resource, $dir_resource)) {
            //获取当前项目的名称,即压缩包里面当前对应的文件名
            $file_name = $path . zip_entry_name($dir_resource);
            $files[] = zip_entry_name($dir_resource);
            //以最后一个“/”分割,再用字符串截取出路径部分
            $file_path = substr($file_name, 0, strrpos($file_name, "/"));
            //如果路径不存在，则创建一个目录，true表示可以创建多级目录
            if (!is_dir($file_path)) {
                mkdirs($file_path);
            }
            //如果不是目录，则写入文件
            if (!is_dir($file_name)) {
                //读取这个文件
                $file_size = zip_entry_filesize($dir_resource);
                //最大读取6M，如果文件过大，跳过解压，继续下一个
                if ($file_size < (1024 * 1024 * 6)) {
                    $file_content = zip_entry_read($dir_resource, $file_size);
                    file_put_contents($file_name, $file_content);
                } else {
                    //echo "<p> ".$i++." 此文件已被跳过，原因：文件过大， -> ".$file_name." </p>";
                    return false;
                }
            }
            //关闭当前
            zip_entry_close($dir_resource);
        }
    }
    //关闭压缩包
    zip_close($resource);
    return $files;
}


function deldir($dir)
{
    //先删除目录下的文件：
    $dh = opendir($dir);
    while ($file = readdir($dh)) {
        if ($file != "." && $file != "..") {
            $fullpath = $dir . "/" . $file;
            if (!is_dir($fullpath)) {
                unlink($fullpath);
            } else {
                deldir($fullpath);
            }
        }
    }
    closedir($dh);
    //删除当前文件夹：
    if (rmdir($dir)) {
        return true;
    } else {
        return false;
    }
}


function readDirs($dir)
{
    $fileNameList = [];
    $dh = opendir($dir);
    if ($dh) {
        while (($file = readdir($dh)) !== false) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $fileName = $dir . '/' . $file;
            if (is_dir($fileName)) {
                $fileNameList[$file] = readDirs($fileName);
            } else {
                $fileNameList[] = $fileName;
            }
        }
        closedir($dh);
    }
    return $fileNameList;
}


function array2urlparam($data = array())
{
    if (is_array($data) && count($data)) {
        foreach ($data as $k1 => $v1) {
            $urlparams[] = $k1 . '=' . $v1;
        }
        return $urlparams;
    }
    return array();
}


function attachString($_arr)
{
    //业务范围|运营者id|游戏id|服务器id|链接id|素材id|推广员id|attach|渠道|营销订单
    //work|yuid||gid|server_id|linkid|mid|proid|attach|q|order_id
    //work=>1,2,3 (1=>指定服推广， 2=>cps推广， 3=>其他)
    //yuid=>整形
    $arr = array();
    $arr['work'] = intval($_arr['work']);
    $arr['yuid'] = intval($_arr['yuid']);
    $arr['gid'] = intval($_arr['gid']);
    $arr['server_id'] = intval($_arr['server_id']);
    $arr['linkid'] = intval($_arr['linkid']);
    $arr['mid'] = intval($_arr['mid']);
    $arr['proid'] = intval($_arr['proid']);
    $arr['attach'] = trim($_arr['attach']);
    $arr['q'] = intval($_arr['q']);
    $arr['order_id'] = trim($_arr['order_id']);
    $str = $arr['work'] . "|" . $arr['yuid'] . "|" . $arr['gid'] . '|' . $arr['server_id'] . '|' . $arr['linkid'] . "|" . $arr['mid'] . "|" . $arr['proid'] . "|" . $arr['attach'] . '|' . $arr['q'] . '|' . $arr['order_id'];
    return authcode($str, 1);
}

function attachDeString($str)
{
    //业务范围|运营者id|游戏id|服务器id|链接id|素材id|推广员id|attach|渠道|营销订单
    //work|yuid||gid|server_id|linkid|mid|proid|attach|q|order_id
    //work=>1,2,3 (1=>指定服推广， 2=>cps推广， 3=>其他)
    //yuid=>整形
    $arr = $_arr = array();
    $arr = explode('|', authcode($str));
    $_arr['work'] = intval($arr[0]);
    $_arr['yuid'] = intval($arr[1]);
    $_arr['gid'] = intval($arr[2]);
    $_arr['server_id'] = intval($arr[3]);
    $_arr['linkid'] = intval($arr[4]);
    $_arr['mid'] = intval($arr[5]);
    $_arr['proid'] = intval($arr[6]);
    $_arr['attach'] = trim($arr[7]);
    $_arr['q'] = intval($arr[8]);
    $_arr['order_id'] = trim($arr[9]);
    return $_arr;

}

function send_system_notice($uid, $type, $options = array())
{
    $time = date("Y-m-d H:i:s");
    $status = '2';
    if (is_array($options)) {
        //$time=$options['time'];
        //$status=$options['status'];
        $title = $GLOBALS['system_notice'][$type][0];
        $content = $GLOBALS['system_notice'][$type][1];
        foreach ($options as $k1 => $v1) {
            $index = $k1 + 1;
            $content = str_replace("{{{$index}}}", $v1, $content);
        }
    }
    $sql = "insert into kaixinwan.notice (uid,title,content,time,status) values ('{$uid}','{$title}','{$content}','{$time}','{$status}')";
    $GLOBALS['mysql'] = Mysql::getInstance();
    return $GLOBALS['mysql']->insert($sql);
}

function isID($value)
{
    return is_numeric($value) && (intval($value) > 0);
}

/** 概率计算函数
 * @param $proArr
 * @return int|string
 */
function get_rand($proArr)
{
    $result = '';
    //概率数组的总概率精度
    $proSum = array_sum($proArr);
    //概率数组循环
    foreach ($proArr as $key => $proCur) {
        $randNum = mt_rand(1, $proSum);
        if ($randNum <= $proCur) {
            $result = $key;
            break;
        } else {
            $proSum -= $proCur;
        }
    }
    unset ($proArr);
    return $result;
}

/** 创建GUID字符串
 * @return string
 */
function create_guid()
{
    $charid = strtoupper(md5(uniqid(mt_rand(), true)));
    $hyphen = chr(45);// "-"
    $uuid = substr($charid, 0, 8) . $hyphen
        . substr($charid, 8, 4) . $hyphen
        . substr($charid, 12, 4) . $hyphen
        . substr($charid, 16, 4) . $hyphen
        . substr($charid, 20, 12);
    return $uuid;
}

/*
功能：补位函数
str:原字符串
type：类型，0为后补，1为前补
len：新字符串长度
msg：填补字符
*/
function dispRepair($str, $len, $msg, $type = '1')
{
    $length = $len - strlen($str);
    if ($length < 1) return $str;
    if ($type == 1) {
        $str = str_repeat($msg, $length) . $str;
    } else {
        $str .= str_repeat($msg, $length);
    }
    return $str;
}

/** 判断两个数组是否完全相等
 * @param $arr1
 * @param $arr2
 * @return bool
 */
function isSameArray($arr1, $arr2)
{
    if (count($arr1) != count($arr2)) {
        return false;
    }

    foreach ($arr1 as $k => $v) {

        if (is_array($v) && is_array($arr2[$k])) {
            if (!isSameArray($v, $arr2[$k])) {
                return false;
            }
        }

        if (!isset($arr2[$k])) {
            return false;
        }

        if ($arr2[$k] != $v) {
            return false;
        }
    }

    return true;
}

/** 计算两个日期相差天数（根据需要，修改日期前后顺序）
 * @param $day1 date()型
 * @param $day2 date()型
 * @return int
 */
function diffBetweenTwoDays($day1, $day2)
{
    $second1 = strtotime($day1);
    $second2 = strtotime($day2);
    if ($second1 < $second2) {
        $tmp = $second2;
        $second2 = $second1;
        $second1 = $tmp;
    }
    return intval(($second1 - $second2) / 86400);
}

//实例化类，并调用类
function M($obj)
{
    $obj = $obj::getInstance();
    return $obj;
}

//电话号码省略代替如：132****5589
function tel_elliptical($tel)
{
    if (!$tel || !is_string($tel)) return '';
    if (strlen($tel) != 11) return '';
    $new_tel = substr_replace($tel, '****', 3, 4);
    return $new_tel;
}

//地址
function address_to_str($province, $city, $contory = 0, $b = '', $level = 3)
{

    $str_address = '';


    $file = ROOT . '/lib/address/address.json';
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), 256);
        $data = $data['data'];
        foreach ($data as $k1 => $v1) {
            if ($v1['id'] == $province) {
                $str_address .= $v1['name'];
                if ($level == 1) {
                    echo $str_address;
                    return;
                }

                if (!is_array($v1['child'])) {
                    echo $str_address;
                    return;
                }
                foreach ($v1['child'] as $k2 => $v2) {
                    if ($v2['id'] == $city) {

                        $str_address .= $b . $v2['name'];
                        if ($level == 2) {
                            echo $str_address;
                            return;
                        }

                        if (!is_array($v2['child'])) {
                            echo $str_address;
                            return;
                        }
                        foreach ($v2['child'] as $k3 => $v3) {
                            if ($v3['id'] == $contory) {
                                $str_address .= $b . $v3['name'];
                                if ($level == 3) {
                                    echo $str_address;
                                    return;
                                }
                            }
                        }
                    }
                }
            }
        }


    }
    echo $str_address;
}

function address_to_str_new($province, $city, $contory = 0, $b = '', $level = 3)
{

    $str_address = '';


    $file = ROOT . '/lib/address/address.json';
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), 256);
        $data = $data['data'];
        foreach ($data as $k1 => $v1) {
            if ($v1['id'] == $province) {
                $str_address .= $v1['name'];
                if ($level == 1) {
                    return $str_address;
                }

                if (!is_array($v1['child'])) {
                    return $str_address;

                }
                foreach ($v1['child'] as $k2 => $v2) {
                    if ($v2['id'] == $city) {

                        $str_address .= $b . $v2['name'];
                        if ($level == 2) {
                            return $str_address;
                        }

                        if (!is_array($v2['child'])) {
                            return $str_address;
                        }
                        foreach ($v2['child'] as $k3 => $v3) {
                            if ($v3['id'] == $contory) {
                                $str_address .= $b . $v3['name'];
                                if ($level == 3) {
                                    return $str_address;
                                }
                            }
                        }
                    }
                }
            }
        }


    }
    return $str_address;
}


//微信通知
/**
 * @param $uid 用户id
 * @param $order_status  订单状态
 * @param $order_id 订单号
 * @return bool
 */
function wx_order_notice($uid, $order_status, $order_id)
{
    log_message('WEIXIN INFO',$uid.$order_status.$order_id,'notice');
    $user = DbPassport_UserPage::getInstance();
    $user_info = $user->userByUid($uid);
    //订单信息
    $model_order = DbNongJiaqm_Order::getInstance();
    $order_info = $model_order->find(['id' => $order_id]);
    $model_order_product = new DbNongJiaqm_OrderProduct();
    $product = $model_order_product->findAll(['order_id' => $order_id]);
    //订单已经生成，等待付款
    $product_name = [];
    foreach($product as $v){
        $product_name[] = $v['name'];
    }
    $name = implode(',', $product_name);

    $url = ConfigPath::HOST_WEB;
    if ($order_status == DbNongJiaqm_Order::WAIT_PAY) {

        $data = array(
            'first' => array('value' => '订单生成通知', 'color' => '#173177'),
            'keyword1' => array('value' => date('Y-m-d H:i:s', time()), 'color' => '#173177'),  //时间
            'keyword2' => array('value' => $name, 'color' => '#173177'),    //商品名称
            'keyword3' => array('value' => $order_info['order_number'], 'color' => '#173177'),
            'remark' => array('value' => '点击去支付', 'color' => '#173177'),
        );

        $url .= 'order/oid-' . $order_info['id'];
    } elseif ($order_status == DbNongJiaqm_Order::WAIT_SEND) {
        $data = array(
            'first' => array('value' => '您的订单已成功付款，等待发货。', 'color' => '#173177'),
            'keyword1' => array('value' => date('Y-m-d H:i:s', time()), 'color' => '#173177'),  //时间
            'keyword2' => array('value' => $name, 'color' => '#173177'),    //商品名称
            'keyword3' => array('value' => $order_info['order_number'], 'color' => '#173177'),
            'remark' => array('value' => '点击查看详情', 'color' => '#173177'),
        );
        $url .= 'order/oid-' . $order_info['id'];
    } elseif ($order_status == DbNongJiaqm_Order::WAIT_GET) {  //发货通知
        $data = array(
            'first' => array('value' => '亲，您购买的商品已于今日登上飞船，向你处出发！', 'color' => '#173177'),
            'keyword1' => array('value' => date('Y-m-d H:i:s', time()), 'color' => '#173177'),  //时间
            'keyword2' => array('value' => $name, 'color' => '#173177'),    //商品名称
            'keyword3' => array('value' => $order_info['order_number'], 'color' => '#173177'),
            'remark' => array('value' => '点击查看物流', 'color' => '#173177'),
        );
        $url .= 'order/oid-' . $order_info['id'];
    } elseif ($order_status == DbNongJiaqm_Order::WAIT_CANCEL) {  //订单已取消
        $data = array(
            'first' => array('value' => '尊敬的' . $user_info['weixin_name'] . '您好！', 'color' => '#173177'),
            'keyword1' => array('value' => date('Y-m-d H:i:s', time()), 'color' => '#173177'),  //时间
            'keyword2' => array('value' => $name, 'color' => '#173177'),    //商品名称
            'keyword3' => array('value' => $order_info['order_number'], 'color' => '#173177'),
            'remark' => array('value' => '此单已于' . date('Y-m-d H:i:s', time()) . ' 被取消，如有疑问，请联系我们', 'color' => '#173177'),
        );
        $url .= 'order/oid-' . $order_info['id'];
    } elseif ($order_status == DbNongJiaqm_Order::WAIT_SUCCESS) {  //订单完成
        $data = array(
            'first' => array('value' => '尊敬的用户您好，您的订单已完成。', 'color' => '#173177'),
            'keyword1' => array('value' => date('Y-m-d H:i:s', time()), 'color' => '#173177'),  //时间
            'keyword2' => array('value' => $name, 'color' => '#173177'),    //商品名称
            'keyword3' => array('value' => $order_info['order_number'], 'color' => '#173177'),
            'remark' => array('value' => '如有任何疑问，请联系我们', 'color' => '#173177'),
        );
        $url .= 'order/oid-' . $order_info['id'];
    }elseif ($order_status == DbNongJiaqm_Order::WAIT_SERVICE) {  //订单完成
        $data = array(
            'first' => array('value' => '尊敬的用户您好，您的订单已申请售后。', 'color' => '#173177'),
            'keyword1' => array('value' => date('Y-m-d H:i:s', time()), 'color' => '#173177'),  //时间
            'keyword2' => array('value' => $name, 'color' => '#173177'),    //商品名称
            'keyword3' => array('value' => $order_info['order_number'], 'color' => '#173177'),
            'remark' => array('value' => '点击查看售后状态', 'color' => '#173177'),
        );
        $url .= 'order/oid-' . $order_info['id'];
    }


    $model_request = new ProMessageCenter_Request();
    $ext = array(
        'url' => $url,
        'appid' => '',
        'pagepath' => '',
    );


    $res = $model_request->wxTemplateOne(7, 77777777, $user_info['weixin_login_openid'], 13, $data, $ext);
    if ($res['code'] == 1) {
        return true;
    } else {
        return false;
    }


}

function wx_return_notice($return_status, $return_id)
{
    global $_SC;

    $url = ConfigPath::HOST_WEB.'order/oid-' . $order_info['id'];

    $return_model = DbNongJiaqm_Return::getInstance();
    $status = DbNongJiaqm_Return::$STATUS;
    $reason = DbNongJiaqm_Return::$REASON;
    $return_info = $return_model->find(['id' => $return_id]);
    $data = array(
        'first' => array('value' => '您好，您在农家情迷申请退款，' . $status[$return_status] . '。', 'color' => '#173177'),
        'reason' => array('value' => $reason[$return_info['return_reason_type']], 'color' => '#173177'),  //
        'refund' => array('value' => sprintf('%.2f', $return_info['return_money'] / 100), 'color' => '#173177'),    //
        'remark' => array('value' => '如有疑问，请联系我们，或回复微信公众号', 'color' => '#173177'),
    );


    $user = DbPassport_User::getInstance();
    $uid = $return_info['uid'];
    $user_info = $user->userByUid($uid);
    $model_request = new ProMessageCenter_Request();
    $ext = array(
        'url' => $url,
        'appid' => '',
        'pagepath' => '',
    );


    $res = $model_request->wxTemplateOne(7, 77777777, $user_info['weixin_login_openid'], 14, $data, $ext);
    if ($res['code'] == 1) {
        return true;
    } else {
        return false;
    }


}


/**
 * 过滤emoji表情
 * @param string $str 字符串
 * @return string
 */
function filterEmoji($str)
{
    if (empty($str)) return null;
    $str = preg_replace_callback(
        '/[\xf0-\xf7].{3}/',
        function ($r) {
            return '@E' . base64_encode($r[0]);
        }, $str);
    $countt = substr_count($str, "@");
    for ($i = 0; $i < $countt; $i++) {
        $c = stripos($str, "@");
        $str = substr($str, 0, $c) . substr($str, $c + 10, strlen($str) - 1);
    }
    $str = preg_replace_callback(
        '/@E(.{6}==)/',
        function ($r) {
            return base64_decode($r[1]);
        }, $str);
    return $str;
}

function filterEmoji_new($str)
{
    $str = preg_replace_callback(
        '/./u',
        function (array $match) {
            return strlen($match[0]) >= 4 ? '' : $match[0];
        },
        $str);

    return $str;
}


/**
 * 深度遍历数组调用回调函数, 但不会携带键名
 * @param $callback
 * @param $arrayList
 */
function arrayMapAllWidthNokey($callback, $arrayList)
{
    array_map(function ($arrayList) use ($callback) {
        if (is_array($arrayList)) {
            arrayMapAllWidthNokey($callback, $arrayList);
        } else {
            call_user_func($callback, $arrayList);
        }
    }, $arrayList);
}


?>