<?php namespace API\Model;
use Think\Model\ViewModel;

class FileViewModel extends ViewModel {
	public $viewFields = array(
		'file' => array(
			'id', 'pri_id', 'use_id', 'name', 'url', 'time', 'requirements', 'copies', 'double_side', 'status', 'color', 'ppt_layout', 'sended',
		),
		'user' => array(
			'name' => 'use_name',
			'student_number',
			'phone',
			'_on' => 'user.id=file.use_id',
		),
	);
}
