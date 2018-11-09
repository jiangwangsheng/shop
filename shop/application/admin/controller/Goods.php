<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;
use app\admin\model\Good;
use app\admin\model\Others;
use app\admin\model\Casess;
class Goods extends Controller
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
	//跳转到添加页面
	public function add_good()
	{
		$case = Db::table('cases')->where('id','in','1,2')->select();
		$cases = Db::table('cases')->where('id','notin','1,2')->select();
		$series = Db::table('series')->select();
		return view('good/add',['case'=>$case,'cases'=>$cases,'series'=>$series]);
	}
	//执行添加
	public function save(Request $request)
	{
		$goods = new Good;
		$others = new Others;
		$data = $request->post();
		$file = $request -> file('image');
		$a = $this->image($file); //调用图片上传方法
		$str = '';
		$files = $request->file('images');
		foreach($files as $file) {
			if(count($files) < 2){
				return $this->error('配图至少2张图片');
			}
			$str .= $this->image($file);
		}
		$tu = explode('.jpg',$str); //分隔成数组
		$tu = implode('.jpg,',$tu); //连接成字符串 以,号做鉴别
		$goods -> image = $a;
		$goods -> images = $tu;

		$this->tianjia($goods,$others,$data);
		$n = $goods -> save();
		$others -> gid = $goods -> id;
		$m = $others -> save();

		if($n && $m) {
			return $this->success('添加成功','index');
		}else{
			return $this->error('添加失败');
		}
	}
	//封装的图片上传方法
	protected function image($file)
	{
		 if($file){
		 	    $info = $file->validate(['size'=>1567118,'ext'=>'jpg,png,jpeg,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads');
                    if($info){
                    	  $a = $info->getFilename();
                    	  return(date('Ymd').'/'.$a);
                    	 }else{
                        // 上传失败获取错误信息
                        echo $file->getError();
                 }
           }

	}
	//封装的添加方法
	private function tianjia($goods,$others,$data)
	{
		$goods -> gname = $data['gname'];
		$goods -> model = $data['model'];
		$goods -> price = $data['price'];
		$goods -> sid = $data['sid'];   //系列id
		$goods -> nid = $data['nid'];  //男表还是女表
		$goods -> baoxiu = $data['baoxiu'];  //是否保修
		$goods -> cid = $data['cid'];  //类别id
		
		$others -> bk = $data['bk'];
		$others -> bl = $data['bl'];
		$others -> bpys = $data['bpys'];
		$others -> bkzj = $data['bkzj'];
		$others -> boli = $data['boli'];
		$others -> fsxn = $data['fsxn'];
		$others -> bejj = $data['bejj'];
		$others -> xz = $data['xz'];
		$others -> bd = $data['bd'];
		$others -> jx = $data['jx'];
		
	}
	//图片列表显示
	public function index()
	{
		$data = Good::paginate(5);
		foreach ($data as $k=>$v) {
			$cname = Db::table('series')->where('id',$v['sid'])->find();
			$v['sid'] = $cname['cname'];
		}
		foreach ($data as $k=>$v) {
			$case = Db::table('cases')->where('id',$v['cid'])->find();
			$v['cid'] = $case['case'];
		}
		foreach ($data as $k=>$v) {
			$case = Db::table('cases')->where('id',$v['nid'])->find();
			$v['nid'] = $case['case'];
		}
		return view('good/index',['data'=>$data]);
	}
	//删除图片；
	public function delete(Request $request,$id)
	{
		$n = Good::get($id);
		$img = $n->image;
		@unlink(ROOT_PATH . 'public' . DS . 'uploads/'.$img);
		$imgs = $n->images;
		$images = explode(',',$imgs);
		array_pop($images);
		foreach ($images as $k=>$v) {
			@unlink(ROOT_PATH . 'public' . DS . 'uploads/'.$v);
		}
		$m = Others::where('gid',$id)->delete();
		if($n->delete()) {
			return $this->success('删除成功');
		}else{
			return $this->error('删除失败');
		}
	}
	//修改图片
	public function edit(Request $request,$id)
	{
		$data = [];
		$data[] = Others::where('gid',$id)->find();
		$data[] = Good::get($id);
		$data['1']['images'] = self::images($data['1']['images']);
		$case = Db::table('cases')->where('id','in','1,2')->select();
		$cases = Db::table('cases')->where('id','notin','1,2')->select();
		$series = Db::table('series')->select();
		return view('/good/edit',['data'=>$data,'case'=>$case,'cases'=>$cases,'series'=>$series]);
	}
	//封装的多图片处理；
	static function images($img)
	{	
		$img = explode(',',$img);
		array_pop($img);
		return $img;
	}
	public function update(Request $request,$id)
	{
		$goods = Good::get($id);
		$others = Others::where('gid',$id)->find();
		$data = $request->post();
		$file = $request -> file('image');
			if($file != '')
			{
				$img = $goods->image;
				@unlink(ROOT_PATH . 'public' . DS . 'uploads/'.$img);
				$a = $this->image($file); //调用图片上传方法
				$goods -> image = $a;
			}
		$files = $request->file('images');
					$str = '';
					if($files != null) {
					if(count($files) < 2){
						return $this->error('配图至少2张图片');
					}
						$imgs = $goods->images;
						$images = explode(',',$imgs);
						array_pop($images);
						foreach ($images as $k=>$v)
						{
							@unlink(ROOT_PATH . 'public' . DS . 'uploads/'.$v);
						}
						foreach($files as $file) {
								$str .= $this->image($file);
						}
						$tu = explode('.jpg',$str); //分隔成数组
						$tu = implode('.jpg,',$tu); //连接成字符串 以,号做鉴别
						$goods -> images = $tu;
					}
		$this->tianjia($goods,$others,$data);
		$n = $goods -> save();
		$m = $others -> save();
		return $this->success('修改成功','index');
	}
}