<?php

namespace Home\Model;
use Think\Model\ViewModel;
class FileViewModel extends ViewModel
{
	public $viewFields = array(
		'file'            => array(
			'id',
			'use_id',
			'pri_id',
			'name',
			'url',
			'time',
			'status',
			'copies',
			'double_side',
		) ,
		'printer'            => array(
			'name'=>'pri_name',
			'id',
			'_on'            => 'printer.id=file.pri_id',
		) ,
	);
}
