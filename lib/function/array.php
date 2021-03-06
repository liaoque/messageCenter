<?php
/**
 * 此文件包含了一些数组处理辅助函数
 *
 */

/**
 * 检测是否是一个真实的类C的索引数组
 *
 */
function is_real_array($arr)
{
    if (!is_array($arr)) {
        return FALSE;
    }
    $n = count($arr);
    for ($i = 0; $i < $n; $i++) {
        if (!isset($arr[$i])) {
            return FALSE;
        }
    }
    return TRUE;
}

/**
 * 是否是个关系数组
 */
function is_hashmap($arr)
{
    return is_array($arr) && !is_real_array($arr);
}

/**
 * 从数组第二维中抽出特定的列重组成数组
 *
 * @param array $arr
 * @param string $column
 * @return array
 */
function array_get_column($arr, $column)
{
    if (empty($arr)) {
        return array();
    }
    $res = array();
    foreach ($arr as &$val) {
        $res[] = $val[$column];
    }
    return $res;
}

/**
 * 将2+维数组第2维的某列的值作为key
 *
 * @param array $arr input/out parameter
 * @param string $cloumn
 * @return void
 */
function array_change_key($arr, $column)
{
    if (empty($arr)) {
        return;
    }
    $newArr = array();

    foreach ($arr as $val) {
        $newArr[$val[$column]] = $val;
    }
    return $newArr;
}



/**
 * 将二维数组某俩列值提取出来，一个作为key一个作为value
 * 构成新数组
 *
 * @param Array $arr 二维数组
 * @param String $asKey 要作为key使用的第二维的key
 * @param String $asValue 要作为value使用的第二维的key
 * @param bool $flag 新生成的数组是否是二维的
 * @return Array
 */
function array_rack2nd_keyvalue($arr, $asKey, $asValue, $flag = FALSE)
{
    if (empty($arr)) {
        return array();
    }

    $res = array();
    foreach ($arr as $row) {
        if ($flag) {
            $res[$row[$asKey]][] = $row[$asValue];
        } else {
            $res[$row[$asKey]] = $row[$asValue];
        }
    }

    return $res;
}

/**
 * 复制数组的部分至另一个数组中
 *
 * @param array $source 需要被复制的数组
 * @param array $dest 复制到的目标数组 out parameter
 * @param array $keys 需要被复制的键名数组
 */
function array_partial_copy($source, &$dest, $keys)
{
    $dest = array();
    foreach ($source as $key => $val) {
        if (in_array($key, $keys)) {
            $dest[$key] = $val;
        }
    }
}

/**
 * 将一个数组的key设置为value的值
 * @param array $arr
 * @return array
 */
function array_mirror($arr)
{
    $newArr = array();
    foreach ($arr as $val) {
        $newArr[$val] = $val;
    }
    return $newArr;
}


function array_wrap_values($arr, $prefix = NULL, $suffix = NULL)
{
    foreach ($arr as &$item) {
        if ($prefix) {
            $item = $prefix . $item;
        }
        if ($suffix) {
            $item .= $suffix;
        }
    }
    return $arr;
}

function array_sort_by_keys(&$arr, $keys)
{
    $newArr = array();
    foreach ($keys as $key) {
        if (isset($arr[$key])) {
            $newArr[$key] = $arr[$key];
        }
    }
    $arr = $newArr;
}

function array_flat($arr, $keepKey = TRUE)
{
    $newArr = array();

    if ($keepKey) {
        foreach ($arr as $key => $item) {
            if (is_array($item)) {
                $newArr = $newArr + array_flat($item, TRUE);
            } else {
                $newArr[$key] = $item;
            }
        }
    } else {
        foreach ($arr as $item) {
            if (is_array($item)) {
                $newArr = array_merge($newArr, array_flat($item));
            } else {
                $newArr[] = $item;
            }
        }
    }

    return $newArr;
}

/**
 * 将二维数组二维某列的key值作为一维的key
 *
 */
function array_change_v2k(&$arr, $column)
{
    if (empty($arr)) {
        return;
    }

    $newArr = array();

    foreach ($arr as $val) {
        $newArr[$val[$column]] = $val;
    }
    $arr = $newArr;
}

/** 拼接SQL查询IN条件所需的字符串，示例 field_name in (1,2,3),field_name in ('a','b','c')
 * Summary of implode_4sqlin
 * @param mixed $values
 */
function implode_4sqlin($values = array(), $is_numeric = true)
{
    if (is_array($values)) {
        $values_unique = array_unique($values);
        foreach ($values_unique as $v1) {
            if (is_numeric($v1) && $is_numeric) {
                $in_list[] = $v1;
            } elseif (is_string($v1)) {
                $in_list[] = "'" . $v1 . "'";
            }
        }
        if (is_array($in_list)) {
            return implode(',', $in_list);
        }
    }
    //TODO:WCZ:若没有数据该如何处理，返回NULL还是"''"
    return '';
}

/**
 * 创建一个多维的空数组
 * createEmptyArray(array(), array('k', 'a', 'c', 'd'))
 * @param $p
 * @param $keys
 * @return mixed
 */
function createEmptyArray($arr, $keys)
{
    $data = &$arr;
    foreach ($keys as $key) {
        if (!is_array($data[$key])) {
            $data[$key] = array();
        }
        $data = &$data[$key];
    }
    return $arr;
}

/**
 * 递归删除数组中值为空的元素
 * @param $arr
 * @return array
 */
function array_remove_empty($arr)
{
    $narr = array();

    foreach($arr as $key => $val)
    {
        if (is_array($val))
        {
            $val = array_remove_empty($val);
            if (count($val)!=0)
            {
                $narr[$key] = $val;
            }
        }
        else {
            if (trim($val) != ""){
                $narr[$key] = $val;
            }
        }
    }
    unset($arr);
    return $narr;
}


