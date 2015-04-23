<?php namespace Home\Model;
use Think\Model\ViewModel;

class CardlogViewModel extends ViewModel {
	public $viewFields = array(
		'cardlog' => array(
			'id',
			'find_id',
			'lost_id',
			'time',
			'status',
		),
		'find' => array(
			'_table' => 'user',
			'name'   => 'find_name',
			'student_number' => 'find_number',
			'_on'    => 'find.id=cardlog.find_id',
		),
		'lost' => array(
			'_table' => 'user',
			'name'   => 'lost_name',
			'student_number' => 'lost_number',
			'_on'    => 'lost.id=cardlog.lost_id',
		),
	);
}
