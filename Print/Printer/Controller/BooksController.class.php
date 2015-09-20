<?php
namespace Printer\Controller;
use Think\Controller;

class BooksController extends Controller
{

	/**
	 * 列表页
	 * @method index
	 * @author NewFuture[newfuture@yunyin.org]
	 */
	public function index()
	{
		$id = pri_id();
		if (!$id)
		{
			$this->error('请登录！', U('/Printer/Index/index'));
		}
		else
		{
			$page        = I('page', 1, 'int');
			$books       = M('Book')->where('pri_id=%d', $id)->field('id,name,price,detail,time')->page($page, 20)->select();
			$this->books = $books;
			$this->page  = $page > 0 ? $page : 1;
			$this->display();
		}
	}

	public function detail($id = 0)
	{
		$pid = pri_id();
		if ($pid && $id && $book = M('Book')->where('id=%d AND pri_id=%d', $id,$pid)->field('id,name,price,detail,image')->find())
		{
			$this->book = $book;
			$this->display();
		}
		else
		{
			$this->error('无权访问！');
		}
	}

	public function add()
	{
		if (pri_id(U('Index/index')))
		{
			$this->display();
		}
		else
		{
			$this->error('请登录！', U('/Printer/Index/index'));
		}
	}

	public function insert()
	{
		if ($pid = pri_id(U('Index/index')))
		{
			if ($books = I('post.books'))
			{
				$books = explode("\n", $books);
				$books = array_map('trim', $books);
				$books = array_filter($books);
				$data  = array();
				foreach ($books as $key => $name)
				{
					$data[] = array('pri_id' => $pid, 'name' => $name);
				}
				if (M('Book')->addAll($data))
				{
					return $this->success('添加成功');
				}
			}
			$this->error('添加失败');
		}
		else
		{
			$this->error('请登录');
		}

	}

	public function del($id = 0)
	{
		if ($pid = pri_id(U('Index/index')))
		{
			if (M('book')->where('id=%d AND pri_id=%d', $id, $pid)->delete())
			{
				return $this->success('删除成功');
			}
		}
		$this->success('删除失败');
	}

	public function save($id = 0)
	{
		if ($pid = pri_id(U('Index/index')))
		{
			if (M('book')->where('id=%d AND pri_id=%d', $id, $pid)->save($_POST))
			{
				return $this->success('保存成功');
			}
		}
		$this->success('保存失败');
	}
}
