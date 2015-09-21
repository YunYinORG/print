<?php
/*SAE配置*/
return array(
	// 'VERIFY_NKU'       => 'Verify.NankaiProxy',
	'SHOW_PAGE_TRACE'  => 0,
	'NKU_OPEN'         => 1,
	'FILE_UPLOAD_TYPE' => 'QINIU',
	'TMPL_PARSE_STRING' => array(
		'__PUBLIC__' => '/Public',
		'__CDNLIB__' => 'http://apps.bdimg.com/libs',
		'__UPLOAD__' => 'http://nkuprint-uploads.stor.sinaapp.com',
	),
);
