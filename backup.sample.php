<?php
/*数据库备份*/
define('DOMAIN', 'DB_BACK');
//define('', '');

$stor = new SaeStorage(SAE_ACCESSKEY, SAE_SECRETKEY);

/*当天数据备份*/
$date = date('Y-m-d');
$filename = $date.'sql.zip';
if (!$stor->fileExists(DOMAIN, $filename))
{
	$dj = new SaeDeferredJob();
	$taskID = $dj->addTask('export', 'mysql', DOMAIN, $filename, SAE_MYSQL_DB, '', '');
	if ($taskID === false)
	{
		var_dump($dj->errno(), $dj->errmsg());
	}
	else
	{
		echo $taskID;
	}
}

/*旧备份清理*/
/*保留最近7天和每周1和每月1日的数据备份*/
$week_ago = date('Y-m-d', strtotime('-1 week'));
$ninety_days_ago = date('Y-m-d', strtotime('-90 day'));
if (date('N', time()) != '1' && date('d') != '8' && $stor->fileExists(DOMAIN, $week_ago.'.sql.zip'))
{
	//if ( today != monday && today != 8.th), delete 7 days ago backup
	$stor->delete(DOMAIN, $week_ago.'.sql.zip');
}
if (date('d', strtotime('-90 day')) != '1' && $stor->fileExists(DOMAIN, $ninety_days_ago.'.sql.zip'))
{
	//if ( $90daysago != 1.st ), delete 3 months ago backup
	$stor->delete(DOMAIN, $ninety_days_ago.'.sql.zip');
}
?>