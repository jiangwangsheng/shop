<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\md5;
use think\Session;
class Login extends Controller
{
    public function _empty()
    {
        return $this->error('没有该方法');
    }
	public function index()
    {
    	return view('index/login');
    }
	public function login(Request $request)
    {
    	$data = $request->post();
    	$data['password'] = md5($data['password']);
    	$login = Db::table('user')->where('username',$data['username'])->where('password',$data['password'])
    	->where('qx','1')->find();
    	if($login) {
    		session::set('username',$login['username']);
    		session::set('id',$login['id']);
    		// dd(session::get('username'));
    		return view('index/user_info',['name'=>session::get('username')]);
    	}else {
    		return $this->error('登录失败','index',3);
    	}
    }
    public function logout()
    {
    	session::set('username',null);
    	return $this->error('退出成功,请重新登录','index');
    }
}