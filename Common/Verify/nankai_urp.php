<?php

function parseName($html, $start, $end)
{
	//截取$start右边的
	$html = substr(strstr($html, $start), strlen($start));
	$name = strstr($html, $end, true); // 截取$end左边的
	return trim($name);
}

function getName($number, $password)
{
	$data['svpn_name']     = $number;
	$data['svpn_password'] = $password;
	$base                  = 'https://221.238.246.69:443';
	$url                   = 'https://221.238.246.69:443/por/login_psw.csp';
	$ch                    = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_USERAGENT, 'AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1');

#获取cookie
	$content = curl_exec($ch);
	if (preg_match('/Set-Cookie:(.*);/iU', $content, $matchs))
	{
		$cookie = $matchs[1];
	}

#设置cookie
	curl_setopt($ch, CURLOPT_COOKIE, $cookie);

#登录VPN
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
	unset($data);
	$content = curl_exec($ch);

#登录urp
	$urp['Login.Token1'] = $number;
	$urp['Login.Token2'] = $password;
	curl_setopt($ch, CURLOPT_URL, 'https://221.238.246.69:443/web/1/http/0/urp.nankai.edu.cn/userPasswordValidate1.portal');
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($urp));
	$content = curl_exec($ch);

#获取信息
	curl_setopt($ch, CURLOPT_URL, 'https://221.238.246.69:443/web/1/http/0/urp.nankai.edu.cn/index.portal');
	$content = curl_exec($ch);
#关闭curl
	curl_close($ch);
	return parseName($content, '您好，', '欢迎来到南开大学信息门户');
}