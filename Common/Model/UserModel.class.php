<?php
/**
 * Class and Function List:
 * Function list:
 * - _after_find()
 * Classes list:
 * - UserModel extends Model
 */
namespace Common\Model;
use Think\Model;

class UserModel extends Model {

	//字段
	protected $fields = array(
		'id', 'student_number', 'sch_id', 'password', 'name', 'gender', 'phone', 'email', 'status',
		'_pk' => 'id',
		'_type' => array(
			'id' => 'bigint',
			'student_number' => 'char(10)',
			'password' => 'char(32)',
			'name' => 'char(8)',
			'gender' => 'char(2)',
			'phone' => 'char(16)',
			'email' => 'char(64)',
			'status' => 'tinyint',
		));

	/**
	 *查询成功的回调方法
	 *自动对邮箱和手机号进行打码处理
	 */
	protected function _after_find(&$result, $options) {
		if (isset($result['sch_id'])) {
			$result['school'] = M('school')->getFieldById($result['sch_id'], 'name');
		}

		if (!isset($result['email'])) {

			//未读取email

		} elseif ($result['email']) {
			$at = strpos($result['email'], '@');
			$result['mask_email'] = substr_replace($result['email'], '***', 1, $at - 1);
		} else {
			$result['mask_email'] = null;
		}

		if (!isset($result['phone'])) {

			//未读取phone

		} elseif ($result['phone']) {
			$result['mask_phone'] = substr_replace($result['phone'], '********', -8);
		} else {
			$result['mask_phone'] = null;
		}
		return $result;
	}
}
?>