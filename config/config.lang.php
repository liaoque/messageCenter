<?php
if(!defined('IN_BOOT')) {
	exit('Access Denied');
}
$lang['required']			= "%s为必填。";
$lang['isset']				= "%s必须设置。";
$lang['valid_email']		= "%s不是一个有效的电子邮箱。";
$lang['valid_emails']		= "%s包含的无效的电子邮箱。";
$lang['valid_url']			= "%s不是一个有效的URL地址。";
$lang['valid_ip']			= "%s不是一个有效的IP地址。";
$lang['min_length']			= "%s必须包含至少%s个字符。";
$lang['max_length']			= "%s包含的字符数不超过%s。";
$lang['exact_length']		= "%s必须包含%s个字符,不能有空格。";
$lang['alpha']				= "%s只能包含字母。";
$lang['alpha_numeric']		= "%s只能包含字母、数字。";
$lang['alpha_dash']			= "%s只能包含字母、数字、下划线、中划线。";
$lang['numeric']			= "%s只能包含数字。";
$lang['is_numeric']			= "%s只能包含数字。";
$lang['integer']			= "%s必须是一个整数。";
$lang['regex_match']		= "%s格式不正确。";
$lang['matches']			= "%s与%s不一致。";
$lang['is_natural']			= "%s只能包含自然数(包括零)。";
$lang['is_natural_no_zero']	= "%s只能包含自然数(不包括零)。";
$lang['is_unique'] 			= "The %s field must contain a unique value.";
$lang['decimal']			= "The %s field must contain a decimal number.";
$lang['less_than']			= "%s的值必须小于%s。";
$lang['greater_than']		= "%s的值必须大于%s。";

//操作提示信息
$lang_message['execute_success'] = '操作成功！';
$lang_message['execute_fail'] = '操作失败！';
$lang_message['data_error']='数据错误！';
$lang_message['network_error'] = '网络错误！';

$system_notice = [
    'oa_Stock_shortage' => ['您好，{{1}}的库存已经不足，请尽快补充。'],
];
$GLOBALS['system_notice'] = $system_notice;


/* End of file form_validation_lang.php */
/* Location: ./system/language/english/form_validation_lang.php */
