<?php
function getName($stuID,$pwd)
{
       
	$f = new SaeFetchurl();
	$f->setMethod('post');
	$logindata["IPT_LOGINUSERNAME"] = $stuID;
	$logindata["IPT_LOGINPASSWORD"] = $pwd;
	$f->setPostData($logindata);
	$f->setAllowRedirect(false);
	$f->fetch('http://222.30.60.9/meol/homepage/common/login.jsp');
	if ($f->errno())
	{
		$cookies = $f->responseCookies(false);
		$f->setCookies($cookies);
		$content = $f->fetch('http://222.30.60.9/meol/welcomepage/student/index.jsp');             
        $name = substr($content,(strlen('<li>'."   ") + strpos($content, '<li>')) + 3,(strlen($content) - strpos($content, '</li>')) * (-1));
   		return iconv('GBK','UTF-8//IGNORE',$name);
	}
	else
	{
           return false;
	}
   }
?>