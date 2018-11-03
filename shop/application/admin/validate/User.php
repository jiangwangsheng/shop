<?php
namespace app\admin\validate;
use think\Validate;
class User extends Validate
{
	protected $rule = [
		'username'=>'require|max:50',
		'email' => 'require|max:50|min:6',
		'phone'=>'require|max:15',
	];
	protected $message = [
		'username.require' => '用户名账号不能为空',
		'username.max'=> '用户名长度过长',
		'email.require'=> '邮箱不能为空',
		'email.max' => '请填写正确的邮箱',
		'email.min'=>'请填写正确的邮箱',
		'phone.require'=>'手机号不能为空',
		'phone.max'=>'请填写正确的手机号',
	];
}