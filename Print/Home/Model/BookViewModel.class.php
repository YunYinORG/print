<?php namespace Home\Model;
use Think\Model\ViewModel;

class BookViewModel extends ViewModel {
	public $viewFields = array(
		'Book' => array(
			'id',
			'pri_id',
			'name',
			'price',
			'detail',
			'time'
		),

		'printer' => array(
			'name' => 'printer',
			'address' => 'address',
			'id'=>'pid',
			'_on' => 'printer.id=book.pri_id',
		),
	);
}
