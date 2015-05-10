<?php namespace Admin\Model;
use Think\Model\ViewModel;

class NotifyUserModel extends ViewModel {
	public $viewFields = array(
		'file' => array(
			'name'=>'file_name',
			'time',
			'status',
	//		'COUNT(file.status)'=>'count',
		),
		'user' => array(
			'id' => 'user_id',
			'student_number' => 'stu_num',
			'name' => 'use_name',
			'phone',
			'_on' => 'user.id=file.use_id'
		),
	);
}
