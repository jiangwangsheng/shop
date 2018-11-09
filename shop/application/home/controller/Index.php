<?php
namespace app\home\controller;
use think\Controller;
use think\Request;
use think\Db;
class Index extends Controller
{
	//查找是男表或者是女表
	public function index(Request $request)
	{
		//多表联查查男表或这女表
		// $id = $request->get('id');
		$data= Db::table('goods')->alias('g')
		->join('others o','g.id = o.gid')
		->join('series s','g.sid = s.id')
		->join('cases c','g.cid = c.id')
		->where('nid',1)
		->select();

		dd($data);
	}
	//文章首页
	public function wzsy()
	{
		$articles = Db::table('articles')->where('push',1)->select();
		dd($articles);
	}
	//筛选功能
	public function shai(Request $request)
	{
		$xz = $request -> get('xz');
		dump($xz);
		
		if($xz != null) {
			$data = Db::table('others')->where('xz','<>','圆形')->where('xz','<>','方形')->select();
		}else{
			$data = Db::table('others')->where('xz','圆形')->select();
		}
		dump($data);
	}
}