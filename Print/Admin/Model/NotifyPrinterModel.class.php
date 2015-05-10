<?php namespace Admin\Model;
use Think\Model\ViewModel;

class NotifyPrinterModel extends ViewModel {
	public $viewFields = array(
		'file' => array(
			'status',
			'COUNT(file.status)' => 'count',
		),
		'printer' => array(
			'id' => 'pri_id',
			'name' => 'pri_name',
			'phone',
			'_on' => 'printer.id=file.pri_id'
		),
	);
}
