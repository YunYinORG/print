<?php
// ===================================================================
// | FileName:  /Common/Common/Encrypt.php
// |  加密函数库 使用前许对数据格式进行检查防止异常
// ===================================================================
// # 手机号格式保留加密
// ## 核心思想是格式保留加密，核心加密算法AES可逆加密
// ## 加密步骤：
// #### 尾号四位全局统一加密（通过相同尾号查找手机号或者去重）
// #### 中间六位单独混淆加密（每个人的密码表唯一）
//
// # 框架函数说明
//  C()读取配，可以直接改成相应密钥;
//  F()读取或者写入缓存，sae上对应KVDB;
//  E()抛出异常信息并终止
//
// # 邮箱加密邮箱
//   只对邮箱用户名加密（@字符之前的）
//   邮箱长度限制：63位（保留一位）
//   邮箱用户名（@之前的）长度限制：16位（加密后24位）
//   邮箱域名（@之后）长度限制：38位
// ## 加密原理
// #### 截取用户名——>AES加密——>base64转码 ——>特殊字符替换——>字符拼接
// +------------------------------------------------------------------
// | author： NewFuture
// | version： 1.0
// +------------------------------------------------------------------
// | Copyright (c) 2014 ~ 2015云印南开团队 All rights reserved.
// +------------------------------------------------------------------

/**
 *  encrypt_phone($phone, $snum, $id)
 *  手机号格式保留加密
 * @param $phone string 11位手机号
 * @param $snum string 用户编号字符串,用于混淆密钥
 * @param $id int 用户id,在1~100000之间的整数,用于混淆原文
 * @return string(11) 加密后的手机号
 */
function encrypt_phone($phone, $snum, $id)
{
    $mid = substr($phone, -10, 6);
    $end = substr($phone, -4);
    return substr($phone, 0, -10) . encrypt_mid($mid, $snum, $id) . encrypt_end($end);
}

/**
 *  encrypt_phone($phone, $snum, $id)
 *  手机号格式保留解密
 * @param $phone string 11位手机号
 * @param $snum string 用户编号
 * @param $id int 用户id,
 * @return string(11) 加密后的手机号
 */
function decrypt_phone($phone, $snum, $id)
{
    $mid = substr($phone, -10, 6);
    $end = substr($phone, -4);
    return substr($phone, 0, -10) . decrypt_mid($mid, $snum, $id) . decrypt_end($end);
}

/**
 *  encrypt_end($endNum)
 *  4位尾号加密
 * @param $endNum 4位尾号
 * @return string(4) 加密后的4位数字
 */
function encrypt_end($endNum)
{
    $key = C('ENCRYPT_PHONE_END'); //获取配置密钥
    //对后四位进行AES加密
    $cipher = aes_encode($endNum, $key);
    //加密后内容查找密码表进行匹配
    $table = cipher_table($key);
    $encryption = array_search($cipher, $table);

    if (false === $encryption) {
        //密码表查找失败
        //抛出异常
        E('尾数加密匹配异常!');
        return false;
    } else {
        //转位4位字符串,不足4位补左边0
        return sprintf('%04s', $encryption);
    }
}

/**
 *  encrypt_mid($midNum, $snum, $id)
 *  中间6位数加密
 * @param $midNum string 6位数字
 * @param $snum string 编号字符串,用于混淆密钥
 * @param $id int 用户id,在1~100000之间的整数,用于混淆原文
 * @return string(6) 加密后的6位数字
 */
function encrypt_mid($midNum, $snum, $id)
{
    $key = C('ENCRYPT_PHONE_MID'); //获取配置密钥
    $key = substr($snum . $key, 0, 32); //混淆密钥,每个人的密钥均不同
    $table = cipher_table($key, 'mid');
    //拆成两部分进行解密
    $midNum -= $id;
    list($mid1, $mid2) = str_split($midNum, 3);
    $e1 = array_search(aes_encode($mid1, $key), $table);
    $e2 = array_search(aes_encode($mid2, $key), $table);

    if (false === $e1) {
        //前密码表查找失败
        E('中间前三位加密异常!');
    } elseif (false === $e2) {
        //密码表查找失败
        E('中间前后三位加密异常!');
        return false;
    } else {
        //调换位置转成6位字符串,不足三位前面置位0
        return sprintf('%03s%03s', $e2, $e1);
    }
}

/**
 * decrypt_end($encodeEnd)
 *  4位尾号解密
 * @param $encodeEnd 加密后4位尾号
 * @return string(4) 解密后的后四位
 */
function decrypt_end($encodeEnd)
{
    $key = C('ENCRYPT_PHONE_END'); //获取配置密钥
    $table = cipher_table($key); //读取密码表
    //获取对应aes密码
    $end = intval($encodeEnd);
    $cipher = $table[$end];
    if (!$cipher) {
        E('尾号密码查找失败');
    }
    //对密码进行解密
    $endNum = (int)aes_decode($cipher, $key);
    return sprintf('%04s', $endNum);
}

/**
 *  decrypt_mid($midEncode, $snum, $id)
 *  中间6位数解密函数
 * @param $midEncode string 加密后的6位数字
 * @param $snum string 编号字符串,用于混淆密钥
 * @param $id int 用户id,在1~100000之间的整数,用于混淆原文
 * @return string(6) 加密后的6位数字
 */
function decrypt_mid($midEncode, $snum, $id)
{
    //获取配置密钥
    $key = C('ENCRYPT_PHONE_MID');
    $key = substr($snum . $key, 0, 32);
    //获取密码表
    $table = cipher_table($key, 'mid');
    list($mid1, $mid2) = str_split($midEncode, 3);
    $c1 = $table[intval($mid1)];
    $c2 = $table[intval($mid2)];
    //解密
    $n1 = (int)aes_decode($c1, $key);
    $n2 = (int)aes_decode($c2, $key);
    //还原
    $num = $n2 * 1000 + $n1;
    $num += $id;
    return sprintf('%06s', $num);
}

/**
 * cipher_table($key,$type)
 *  获取密码表
 *  现在缓存中查询,如果存在,则直接读取,否则重新生成
 * @param $key 加密的密钥
 * @param $type 密码表类型
 * @return array 密码映射表
 */
function cipher_table($key, $type = 'end')
{
    $tableName = $key . $type; //缓存表名称
    $table = F($tableName); //读取缓存中的密码表
    if (!$table) {
        //密码表不存在则重新生成
        $num = ('end' == $type) ? 10000 : 1000; //密码表大小
        //对所有数字,逐个进行AES加密生成密码表
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        mcrypt_generic_init($td, $key, 0);
        for ($i = 0; $i < $num; ++$i) {
            $table[] = mcrypt_generic($td, $i);
        }
        mcrypt_generic_deinit($td);
        sort($table); //根据加密后内容排序得到密码表
        F($tableName, $table); //缓存密码表
    }
    return $table;
}

/**
 * aes_encode(&$data, $key)
 *  aes加密函数,$data引用传真，直接变成密码
 *  采用mcrypt扩展,为保证一致性,初始向量设为0
 * @param &$data 原文
 * @param $key 密钥
 * @return string(16) 加密后的密文
 */
function aes_encode(&$data, $key)
{
    $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
    mcrypt_generic_init($td, $key, 0);
    $data = mcrypt_generic($td, $data);
    mcrypt_generic_deinit($td);
    return $data;
}

/**
 * aes_decode(&$cipher, $key)
 *  aes解密函数,$cipher引用传真也会改变
 * @param &$cipher 密文
 * @param $key 密钥
 * @return string 解密后的明文
 */
function aes_decode(&$cipher, $key)
{
    $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
    mcrypt_generic_init($td, $key, 0);
    $cipher = mdecrypt_generic($td, $cipher);
    mcrypt_generic_deinit($td);
    return $cipher;
}

/**
 *  encrypt_email($email)
 *  加密邮箱
 * @param $email 邮箱
 * @return string 加密后的邮箱
 */
function encrypt_email($email)
{
    list($name, $domain) = explode('@', $email);
    //aes加密
    aes_encode($name, C('ENCRYPT_EMAIL'));
    //base64转码
    $name = base64_encode($name);
    $encodeMap = array(//编码映射表
         '+' => '-',
        '=' => '_',
        '/' => '.');
    $name = strtr($name, $encodeMap);
    return $name . '@' . $domain;
}

/**
 *  decrypt_email($email)
 *  解密邮箱
 * @param $email 邮箱
 * @return string 解密后的邮箱
 */
function decrypt_email($email)
{
    list($name, $domain) = explode('@', $email);
    $decodeMap = array(//编码映射表
         '-' => '+',
        '_' => '=',
        '.' => '/');
    $name = strtr($name, $decodeMap);
    //base64还原
    $name = base64_decode($name);
    //aes解解码
    aes_decode($name, C('ENCRYPT_EMAIL'));
    return $name . '@' . $domain;
}
