<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;
class Error extends controller
{
	public function index()
	{
		return $this->error('输入错误');
	}
	public function _empty()
	{
		return $this->error('输入错误');
	}
}