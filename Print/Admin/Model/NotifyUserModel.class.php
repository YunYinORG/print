<?php namespace Admin\Model;
use Think\Model\ViewModel;

class NotifyUserModel extends ViewModel {
	public $viewFields = array(
		'file' => array(
			'time',
			'status',
	//		'COUNT(file.status)'=>'count',
		),
		'user' => array(
			'name' => 'use_name',
			'_on' => 'user.id=file.use_id'
		),
	);
}
