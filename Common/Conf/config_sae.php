<?php
/*SAE配置*/
return array(
	'VERIFY_NKU' => 'Verify.NankaiEduOnline', 
	'SHOW_PAGE_TRACE' => 0, 
    'NKU_OPEN' => false,
	'FILE_UPLOAD_TYPE' => 'QINIU',
	 'TMPL_PARSE_STRING' => array(
	 	'__PUBLIC__' => '/Public', 
	 	'__CDNLIB__'=>' http://cdn.bootcss.com',
	 	'__UPLOAD__' => 'http://nkuprint-uploads.stor.sinaapp.com',
	 ),
);
