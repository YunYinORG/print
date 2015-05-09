<?php namespace Admin\Model;
use Think\Model\ViewModel;

class NotifyPrinterModel extends ViewModel {
	public $viewFields = array(
		'file' => array(
			'status',
			'COUNT(file.status)' => 'count',
		),
		'printer' => array(
			'name' => 'pri_name',
			'_on' => 'printer.id=file.pri_id'
		),
	);
}
