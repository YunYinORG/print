<?php
// ===================================================================
// | FileName:      FileModel.class.php
// ===================================================================
// | Discription：   FileModel 文件模型
//      <命名规范：>
// ===================================================================
// +------------------------------------------------------------------
// | 云印南天
// +------------------------------------------------------------------
// | Copyright (c) 2014-2015 云印南天团队 All rights reserved.
// +------------------------------------------------------------------
/**
 * Class and Function List:
 * Function list:
 * - _after_find()
 * Classes list:
 * - FileModel extends Model
 */
namespace Common\Model;
use Think\Model;

class FileModel extends Model {

	//字段
	protected $fields = array(
		'id', 'pri_id', 'use_id', 'name', 'url', 'time', 'requirements', 'copies', 'double_side', 'status', 'color', 'ppt_layout', 'sended',
		'_pk' => 'id',
	);

	/**
	 *创建数据，重载基类create
	 * @method create
	 * @param  array  $addition [description]
	 * @return [type]           [description]
	 * @author NewFuture[newfuture@yunyin.org]
	 */
	public function create($addition = array())
	{

		$this->data['status'] = 1;

		/*获取打印店id*/
		$pid = I('post.pri_id', 0, 'int');
		if ($pid <= 0)
		{
			$this->error = 'unkown printer id';
			return false;
		}
		else
		{
			$this->data['pri_id'] = $pid;
		}

		/*非到店打印*/
		if (I('post.wait'))
		{
			$this->data['copies'] = 0;
		}
		else
		{
			//打印份数
			$copies = I('post.copies', 0, 'int');
			if ($copies <= 0)
			{
				$this->data['copies'] = 0;
			}
			else
			{
				$this->data['copies']=$copies;
				$this->data['double_side'] = I('post.double_side', 0, 'int');
				$this->data['color'] = I('post.color', 0, 'int');
				$this->data['ppt_layout'] = I('post.ppt_layout', 0, 'int');

				/*打印要求*/
				$requirements = I('post.requirements', 0, 'htmlspecialchars');
				if ($requirements)
				{
					$this->data['requirements'] = $requirements;
				}
			}

		}
		$this->data = array_merge($this->data, $addition);
		return $this->data;
	}

}
?>