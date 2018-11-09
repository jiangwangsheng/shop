<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;
use app\admin\model\Casess;
class Cases extends Controller
{
	public function __construct()
	{
		if(session::has('id') != 1 ) {
			return $this->error('没有权限访问','login/index');
		}
	}
	//跳转至添加类别页面
	public function case()
	{
		return view('case/case');
	}
	//添加分类信息
	public function add_case(Request $request)
	{
		$data = $request -> post();
		$n = Db::table('cases')->insert($data);
		if($n){
			return $this->success('添加成功','info');
		}else{
			return $this->error('添加失败');
		}
	}
	//查看当前分类是否存在
	public function case_info(Request $request)
	{
		$case = $request -> get('case');
		$n = Db::table('cases')->where('case',$case)->find();
		if($n != null) {
			return '1';  //不为空代表已存在
		}else{
			return '2'; //为2代表不存在可添加
		}
	}
	//修改
	public function update_case(Request $request,$id)
	{
		$data = $request -> post();
		$n = Db::table('cases')->where('id',$id)->update($data);
		if($n){
			return $this->success('修改成功','info');
		}else{
			return $this->error('修改失败');
		}
	}
	//分类列表
	public function info()
	{
		$data = Casess::paginate(5);
		$page = $data -> render();
		// $page->rollpage = 1;
		return view('case/info',['data'=>$data,'page'=>$page]);
	}
	//分类修改
	public function edit(Request $request,$id)
	{
		$data = Db::table('cases')->find($id);
		return view('case/edit_case',['data'=>$data]);
	}
	//删除分类
	public function delete(Request $request,$id)
	{
		if($id == 1 || $id == 2 || $id == 3) {
			return $this->error('删除失败，该类为主要类。');
		}
		$n = Db::table('cases')->where('id',$id)->delete();
		if($n) {
			return $this->success('删除成功','info');
		}else{
			return $this->error('删除失败','info');
		}
	}
	//进入添加产品系列页面
	public function series()
	{
		return view('case/series');
	}
	//添加功能
	public function add_series(Request $request)
	{
		$data = $request -> post();
		if(count($data) == 0) {
			return $this->error('添加失败，请填写数据');
		}
		$n = Db::table('series')->insert($data);
		if($n) {
			return $this->success('添加成功！','select_series');
		}else{
			return $this->error('添加失败');
		}
	}
	//查看当前所有产品系列
	public function select_series()
	{
		$data = Db::table('series')->paginate(5);
		return view('case/select_series',['data'=>$data]);
	}
	//删除series系列表数据
	public function destroy(Request $request)
	{
		$id = $request -> get('id');
		$n = Db::table('series')->delete($id);
		if($n) {
			return '1';
		}else{
			return '2';
		}
	}
	//进入修改 页面
	public function edit_series(Request $request,$id)
	{
		$data = Db::table('series')->where('id',$id)->find();
		return view('case/edit_series',['data'=>$data]);
	}
	//修改产品系列
	public function update_series(Request $request,$id)
	{
		$data = $request -> post();
		$n = Db::table('series')->where('id',$id)->update($data);
		if($n) {
			return $this->success('修改成功','/admin/cases/select_series');
		}else{
			return $this->error('修改失败');
		}
	}
}