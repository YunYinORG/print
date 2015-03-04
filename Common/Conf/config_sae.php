<?php
/*SAE配置*/
return array(
	'VERIFY_WAY' => 'Verify.NankaiEduOnline', 
	'SHOW_PAGE_TRACE' => 0, 
    'REG_OPEN' => false,
	'FILE_UPLOAD_TYPE' => 'QINIU',
	 'TMPL_PARSE_STRING' => array(
	 	'__PUBLIC__' => '/Public', 
	 	'__CDNLIB__'=>' http://cdn.bootcss.com',
	 	'__UPLOAD__' => 'http://nkuprint-uploads.stor.sinaapp.com',
	 ),
);
