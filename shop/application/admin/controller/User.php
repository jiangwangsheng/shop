<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
use app\admin\controller\validate;
use think\md5;
use think\Session;
class User extends Controller
{
	public function __construct()
	{
		if(session::get('username') == null) {
			return $this->error('清先登录');
		}
	}
	//跳转至注册页面的接口
	public function zhuce()
	{
		// dd(123);
		return view();
	}
	//用户注册信息验证及保存
    public function save(Request $request)
    {
        $data = $request->post();
        $result = $this->validate($data,'User');
        if($result !== true) {
        	return $this->error($result);
        	exit();
        }
        $file = $request -> file('image');
        if($file) {
        	$info =  $file->validate(['size'=>1567118,'ext'=>'jpg,png,jpeg,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads');
        	if($info) {
        		  // 成功上传后 获取上传信息
        		  // 
        	}
        }
    }

   

}