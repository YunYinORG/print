<?php
namespace Home\Controller;
use Think\Controller;

class BooksController extends Controller
{

	/**
	 * 自己分享的文件列表页
	 * @method index
	 * @author NewFuture[newfuture@yunyin.org]
	 */
	public function index()
	{
		// $uid = use_id();
		// if (!$uid)
		// {
		// 	$this->error('请登录！', U('/'));
		// }

		$books       = D('BookView')->Page(1, 20)->select();
		$this->books = $books;
		$this->display();
	}

	/**
	 * 分享文件搜
	 * @method search
	 * @param  输入 tid
	 * @author NewFuture[newfuture@yunyin.org]
	 */
	public function search()
	{
		// $uid = use_id();
		// if (!$uid)
		// {
		// 	$this->error('未登录！');
		// }

		$string    = I('q');           //字符搜索
		$page      = I('p', 1, 'int'); //翻页
		$Book      = D('BookView');
		$condition = array();

		if ($string)
		{
			//通过关键字搜索
			$condition['book.name'] = array('LIKE', '%' . strtr($string,' ','%') . '%');
			// $Share = $Share->where('share.name LIKE "%%%s%%"', $string);
			$Book->where($condition);
		}

		if ($books = $Book->page($page)->limit(20)->select())
		{
			$this->success($books);
		}
		else
		{
			$this->error('无查询结果╮(╯-╰)╭');
		}
	}

	public function detail($id = 0)
	{

		$pid = use_id();
		if ($id && $book = M('Book')->field('id,name,price,detail,image')->find($id))
		{
			$this->book = $book;
			$this->display();
		}
		else
		{
			$this->error('无权访问！');
		}
	}

}
