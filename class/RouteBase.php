<?php
if (!defined('IN_BOOT')) {
    exit('Access Denied');
}

class RouteBase
{
    public $errorView = false;
    var $fields = array();
    var $_fields = array();
    private $path = null;

    private $mod = null;

    private $controller = null;

    private $action = null;

    protected $form = null;

    public function __construct()
    {
        $this->form = new Form($this);
        $GLOBALS['mysql'] = Mysql::getInstance();
        $this->controller = get_called_class();
        $this->lastpage = $_SERVER["HTTP_REFERER"];

        if (!empty($GLOBALS['tpl']) && $GLOBALS['tpl'] instanceof Smarty) {
            $this->_fields = $this->fields();
            $GLOBALS['tpl']->assign('fields', $this->_fields['field']);
            $GLOBALS['tpl']->assign('type', $this->_fields['type']);
            $GLOBALS['tpl']->assign('disable', $this->_fields['disable']);
            $GLOBALS['tpl']->assign('style', $this->_fields['style']);
            $GLOBALS['tpl']->assign('tips', $this->_fields['tips']);
            $GLOBALS['tpl']->assign('disshow', $this->_fields['disshow']);
            $GLOBALS['tpl']->assign('class', $this->_fields['class']);
            $GLOBALS['tpl']->assign('url', $this->_fields['url']);
            $GLOBALS['tpl']->assign('d', $this->_fields['data']);
            $GLOBALS['tpl']->assignByRef('obj', $this);
        }

    }

    /**
     *
     * uid必传
     */

    public function checkUser()
    {
        if($GLOBALS["uinfo"]["uid"]){
            $_POST['uid']=$_GET['uid']=$GLOBALS["uinfo"]["uid"];
        }elseif($GLOBALS["wx_info"]){
            com_api_message('请绑定手机号','',3);
        }else{
            com_api_message('请登录','https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxa62d4d55365b5572&redirect_uri=http%3a%2f%2fwebapi.njqm.tianshengwocha.com%2fuser%2fregister%2fgetWeChat&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect',2);
        }
    }

    /**
     *
     * uid非必传
     */

    public function getUser()
    {
        if($GLOBALS["uinfo"]["uid"]){
            $_POST['uid']=$_GET['uid']=$GLOBALS["uinfo"]["uid"];
        }
    }


    public function init($segments)
    {
        $this->path = $segments['path'];
        $this->mod = $segments['mod'];
        $this->action = $segments['ac'];
    }

    public function actionRun()
    {
        if (method_exists($this, $this->action . 'Func')) {
            call_user_func(array($this, $this->action . 'Func'));
        } else {
            $this->errorFunc();
        }
    }

    public function errorFunc()
    {
      $this->errorView ? show_error('THE URL NOT FOUND', 404) :  show_message('THE URL NOT FOUND', '', 2);
    }

    public function getPath()
    {
        global $segments;
        return '/' . $this->getMod() . '/' . $segments['act'];
    }

    public function getForm()
    {
        return $this->form;
    }

    public function getMod()
    {
        return $this->mod;
    }

    public function getController()
    {
        return $this->controller;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function fields()
    {
        $str = array();
        foreach ($this->fields as $field => $rule) {
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

    public function _fields($field, $v, $param = array())
    {
        $m = "field_" . $field;
        if (method_exists($this, $m)) {
            $this->$m($v, $param);
        }
    }

    public static function createSign($data, $key)
    {
        ksort($data);
        $signData = array();
        foreach ($data as $k => $v) {
            if (!empty($v)) {
                $signData[$k] = $v;
            }
        }
        $signData['key'] = $key;
        $sign = urldecode(http_build_query($signData));
        $sign = strtoupper(md5($sign));

        unset($signData['key']);
        return array(
            'data' => $signData,
            'sign' => $sign
        );
    }

    public function isSign($param, $sign, $key)
    {
        unset($param['sign']);
        $_sign = self::createSign($param, $key);
        if ($sign != $_sign['sign']) {
            return false;
        }
        return true;
    }

}