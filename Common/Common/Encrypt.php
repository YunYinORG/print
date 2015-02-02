<?php
// ===================================================================
// | FileName:  /Common/Common/Encrypt.php
// |  加密函数库 使用前需对数据格式进行检查防止异常
// |  必须开启mcrypt扩展（兼容任意版本）
// ===================================================================
// # 手机号格式保留加密
// ## 核心思想是格式保留加密，核心加密算法AES可逆加密
// ## 加密步骤：
// #### 尾号四位全局统一加密（通过相同尾号查找手机号或者去重）
// #### 中间六位单独混淆加密前两位基本保留,后四位加密 （每个人的密码表唯一）
//
// # 邮箱加密邮箱
//   只对邮箱用户名加密（@字符之前的）并保留第一个字符
//   邮箱长度限制：63位（保留一位）
//   邮箱用户名（@之前的）长度限制：17位（加密后25位）
//   邮箱域名（@之后）长度限制：37位
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
    $table = cipher_table($key);
    //拆成两部分进行解密
    $midNum += $id;
    $mid2 = substr($midNum, 2, 4);
    //后4位加密
    $mid2 = array_search(aes_encode($mid2, $key), $table);
    $mid2 = sprintf('%04s', $mid2);
    if (false === $mid2) {
        //前密码表查找失败
        E('中间加密异常!');
    } else {
        return substr_replace($midNum, $mid2, 2);
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
 * @return string(6)/int 解密后的6位数字
 */
function decrypt_mid($midEncode, $snum, $id)
{
    //获取密码表
    $key = C('ENCRYPT_PHONE_MID');
    $key = substr($snum . $key, 0, 32);
    $table = cipher_table($key);
    //解密
    $mid2 = (int)substr($midEncode, 2, 4);
    $mid2 = $table[$mid2];
    $mid2 = sprintf('%04s', aes_decode($mid2, $key));
    //还原
    $num = substr_replace($midEncode, $mid2, 2);
    $num -= $id;
    return $num;
}

/**
 * cipher_table($key)
 *  获取密码表
 *  现在缓存中查询,如果存在,则直接读取,否则重新生成
 * @param $key 加密的密钥
 * @return array 密码映射表
 */
function cipher_table($key)
{
    $tableName = $key; //缓存表名称
    $table = F($tableName); //读取缓存中的密码表
    if (!$table) {
        //密码表不存在则重新生成
        //对所有数字,逐个进行AES加密生成密码表
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        mcrypt_generic_init($td, $key, '0000000000000000');
        for ($i = 0; $i < 10000; ++$i) {
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
    mcrypt_generic_init($td, $key, '0000000000000000');
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
    mcrypt_generic_init($td, $key, '0000000000000000');
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
    $name2 = substr($name, 1);
    if ($name2) {
        aes_encode($name2, C('ENCRYPT_EMAIL'));
        $name2 = base64_encode($name2); //base64转码
        //特殊字符编码
        $encodeMap = array(
            '+' => '-',
            '=' => '_',
            '/' => '.');
        $name2 = strtr($name2, $encodeMap);
    } else {
        //对于用户名只有一个的邮箱生成随机数掩盖
        $name2 = rand();
    }
    return $name[0] . $name2 . '@' . $domain;
}

/**
 *  decrypt_email(&$email)
 *  解密邮箱
 * @param $email 邮箱
 * @return string 解密后的邮箱
 */
function decrypt_email(&$email)
{
    list($name, $domain) = explode('@', $email);
    $name2 = substr($name, 1);
    if (strlen($name2) < 24) {
        //长度小于24为随机掩码直接去掉
        $name2 = '';
    } else {
        //解密 并base64还原
        $decodeMap = array(
            '-' => '+',
            '_' => '=',
            '.' => '/');
        $name2 = strtr($name2, $decodeMap);
        $name2 = base64_decode($name2);
        //aes解解码
        aes_decode($name2, C('ENCRYPT_EMAIL'));
    }
    $email = $name[0] . $name2 . '@' . $domain;
    return $email;
}
