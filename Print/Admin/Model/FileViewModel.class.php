<?php namespace Admin\Model;
use Think\Model\ViewModel;

class FileViewModel extends ViewModel {
	public $viewFields = array(
		'file' => array(
			'id',
			'use_id',
			'pri_id',
			'name',
			'url',
			'time',
			'status',
			'copies',
			'double_side',
			'color',
			'ppt_layout',
			'requirements'
		),
		'printer' => array(
			'name' => 'pri_name',
			//'id',
			'_on' => 'printer.id=file.pri_id',
		),
		'user' => array(
			'name' => 'user_name',
			'student_number' => 'stu_num',
			'_on' => 'user.id=file.use_id'
		),
	);
}
