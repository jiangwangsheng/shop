<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;
use app\admin\model\Xinwen;
use app\admin\model\Articles;
use think\Session;
use think\Db;
class News extends Controller
{
	public function __construct()
	{
		if(session::has('id') != 1 ) {
			return $this->error('没有权限访问','login/index');
		}
	}
	public function _empty()
	{
		return $this->error('方法不存在');
	}
	//发现故事
	public function fxgs()
	{
		$data = Articles::where('nid',1)->paginate(5);
		return view('news/index',['data'=>$data,'name'=>'发现故事']);
	}
	//手表百科
	public function sbbk()
	{
		$data = Articles::where('nid',2)->paginate(5);
		return view('news/index',['data'=>$data,'name'=>'手表百科']);
	}
	public function add()
	{
		return view('news/add');
	}
	private function chakan()
	{
		$sum = Articles::where('push','1')->select();
		if(count($sum) >= 3) {
				return $this->error('添加失败，文章推送已经满额');
			}
	}
	public function save(Request $request)
	{
		$articles = new Articles;
		$file = $request->file('image');
		if($request->post('push') == 1) {
			$this->chakan();
			$articles -> push = 1;
		}
		$articles -> image = upload($file);
		$articles -> nid = $request->post('nid');
		$articles -> miaosu = $request->post('miaosu');
		$articles -> text = $request->post('text');
		$articles -> title = $request->post('title');
		$articles -> auth = $request->post('auth');
		$articles -> time = date('Ymd H',time());
		$n = $articles->save();
		if($n){
			return $this->success('添加成功');
		}
	}
	//文章详情
	public function edit($id)
	{
		$data = Articles::get($id);
		return view('news/edit',['data'=>$data]);
	}
	//执行修改
	public function update(Request $request,$id)
	{
		//获取所有数据
		$data = $request->post();
		//查看当前推送
		if($request->post('push') == 1) {
			//查询所有为为推送的内容
			$name = Articles::where('push',1)->select();
			//定义空数组
			$arr = [];
			//将推送的id存入数组中
			foreach($name as $k=>$v) //查询当前文章是不是处于推送状态
			{
				$arr[] = $v['id'];
			}
			//判断当前id是否处于推送数组中。如果处于则不变化
			if(!in_array($id, $arr)){
				$this->chakan();
			}
		}
		//如果当前推送为空则设置为空
		if($request->post('push') == null) {
			$data['push'] = 0;
		}
		//获取图片
		$file = $request -> file('image');
		//当前有上传封面图片
		if($file != null){
			//调用上传方法
			$data['image'] = upload($file);
			//获取当前id的图片内容 从文件夹中找到并删除
			$img = Articles::get($id);
			@unlink(ROOT_PATH . 'public' . DS . 'uploads/'.$img['image']);
		}
		//获取当前新闻id看处于哪个栏目
		$zzz = Articles::where('id',$id)->field('nid')->find();
		if($zzz['nid'] == 1) {
			$x = 'fxgs';
		}else{
			$x = 'sbbk';
		}
		$n = Articles::where('id',$id)->update($data);
		if($n) {
			return $this->success('修改成功',$x);
		}else{
			return $this->error('未修改');
		}
	}
	//删除文章
	public function delete($id)
	{
		$img = Articles::get($id);
		@unlink(ROOT_PATH . 'public' . DS . 'uploads/'.$img['image']);
		if(Articles::get($id)->delete()) {
			return $this->success('删除成功');
		}else{
			return $this->error('删除失败');
		}
	}
	//推送页面
	// public function tuisong()
	// {
	// 	$new = Db::table('news')->select();
	// 	return view('news/tuisong',['new'=>$new]);
	// }
	//值改变事件
	// public function song(Request $request)
	// {
	// 	$nid = $request->get('nid');
	// 	$data = Articles::where('nid',$nid)->field('id')->select();
	// 	//别问我为什么不把title放上面，我试过了没用
	// 	$arr = [];
	// 	foreach ($data as $k=>$v) {
	// 		$arr[$k]['id'] = $v['id'];
	// 		$title = Articles::where('id',$v['id'])->field('title')->find();
	// 		$arr[$k]['title'] = $title['title'];
	// 	}
	// 	return $arr;
	// }
	//文章加入推送
	// public function push(Request $request)
	// {
	// 	$sum = Db::table('pushs')->select();
	// 	if(count($sum) == 3 ) {
	// 		return $this->error('推送失败，当前推送文章已经满额');
	// 	}
	// 	$a = Db::table('pushs')->insert($request->post('aid'));
	// 	if($a) {
	// 		return $this->success('推送成功');
	// 	}else{
	// 		return $this->error('推送失败');
	// 	}
	// }
}