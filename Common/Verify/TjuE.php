<?php
//urp登陆验证
//使用示例
//import('Common.Urp',COMMON_PATH);
//$name = get_urp_name('1234556','*******');

/**
*function get_urp_name($stu_number,$pwd) 
*获取
*@param string $stu_number 学号
*@param string $pwd 登陆密码
*@return mixed 
*           string name 验证成功返回姓名
*           bool false /null 验证失败返回false或空值
*@author 赵雅慧
*/
header('Content-Type: text/html; charset=utf-8');

function getName($stu_number, $pwd) 
{
    $url = 'http://e.tju.edu.cn/Main/logon.do';
    $input['uid'] = $stu_number;
    $input['password'] = $pwd;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    list($header, $body) = explode("\r\n\r\n", $content);
    preg_match("/set\-cookie:([^\r\n]*)/i", $header, $matches);
    $cookie = $matches[1];
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $input); 
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION ,1);
    $contents = curl_exec($ch);
    $contents = mb_convert_encoding($contents, "UTF-8", "GBK");
    curl_close($ch);
    $get_name = function ($input, $start, $end) 
    {
        $middlename = substr($input, strlen($start) + strpos($input, $start) + 14, strlen($start) + strpos($input, $start) + 18);
        return substr(trim($middlename), 0, (strpos(trim($middlename), $end)));
    };
    $name  = $get_name($contents, "当前用户", ")");
    return $name;
}
