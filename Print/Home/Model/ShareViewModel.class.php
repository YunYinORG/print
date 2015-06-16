<?php namespace Home\Model;
use Think\Model\ViewModel;

class ShareViewModel extends ViewModel {
	public $viewFields = array(
		'share' => array(
			'id',
			'fil_id',
			'name',
			'time',
			'anonymous',
		),
		'file' => array(
			'status' => 'status',
			'url',
			'_on'    => 'file.id=share.fil_id',
		),
		'user' => array(
			'name' => 'user_name',
			// 'number' => 'lost_number',
			'_on' => 'user.id=file.use_id',
		),
		// 'hastag' => array(
		// 	'_on' => 'hastag.share_id=share.id',
		// ),
		// 'tag' => array(
		// 	'name' => 'tag',
		// 	'_on'  => 'tag.id=hastag.tag_id',
		// ),
	);
}
