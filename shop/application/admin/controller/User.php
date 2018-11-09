<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use app\admin\model\Users;
use think\Request;
use app\admin\controller\validate;
use think\md5;
use think\Session;
class User extends Controller
{
  public function _empty()
  {
      return $this->error('方法不存在');
  }
	public function __construct()
	{
		if(session::has('id') != 1 ) {
			return $this->error('没有权限访问','login/index');
		}
	}
	//跳转至用户注册页面的接口
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
    //用户信息接口
   public function user_info()
   {
        $data = Users::paginate(5);
        $page = $data->render();
        return view('index/user_info',['data'=>$data,'page'=>$page]);
   }
   //进入查看用户页面
   public function userinfos(Request $request,$id)
   {
      $data = Users::get($id);
      return view('index/userinfos',['data'=>$data]);
   }
   //进入修改密码页面
   public function user_password()
   {
        return view('index/user_password');
   }

   //更改密码接口
   public function repassword(Request $request)
   {
        $password = $request -> post('password');
        $newpassword = $request -> post('newpassword');
        if(strlen($newpassword) < 6) {
            return $this->error('密码至少为6位');
        }
        $mima = Db::table('user')->where('id',session::get('id'))->where('password',md5($password))->update(['password'=>md5($newpassword)]);

        if($mima) {
            session::clear();
            return $this->success('修改成功。请重新登录','login/index');
        }else {
            return $this->error('修改失败!');
        }
   }
   //用户信息页面
   public function info()
   {
        $data = Db::table('user')->where('id',session::get('id'))->find();
        return view('index/info',['data'=>$data]);
   }
   //实现用户信息更改
   public function modify(Request $request)
   {
        $data = $request -> post(); //接收除了头像的数据
        $file = $request -> file('image');
        if($file){
             //删除原有的文件节省资源
        $img = Db::table('user')->where('id',session::get('id'))->find();
        if($img['image'] != '') {
            @unlink(ROOT_PATH . 'public' . DS . 'uploads/'.$img['image']);
        }
                    $info = $file->validate(['size'=>1567118,'ext'=>'jpg,png,jpeg,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads');
                    if($info){
                        // 成功上传后 获取上传信息
                        // 输出 jpg
                        //echo $info->getExtension();
                        // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                        //echo $info->getSaveName();
                        // 输出 42a79759f284b767dfcb2a0197904287.jpg
                        $a = $info->getFilename(); 
                        $data['image'] = date('Ymd').'/'.$a;  //如果成功保存头像再压入
                    }else{
                        // 上传失败获取错误信息
                        echo $file->getError();
                    }
                }

        //根据当前登录id修改
        $name = Db::table('user')->where('id',session::get('id'))->update($data);
        if($name) {
            return $this->success('修改成功','user/user_info');
        }else{
            return $this->error('修改失败');
        }
    }
    //普通用户修改
    public function gong(Request $request,$id)
    {
        $data = $request -> post(); //接收除了头像的数据
        $file = $request -> file('image');
        if($file){
             //删除原有的文件节省资源
        $img = Db::table('user')->where('id',$id)->find();
        if($img['image'] != '') {
            @unlink(ROOT_PATH . 'public' . DS . 'uploads/'.$img['image']);
        }
                    $info = $file->validate(['size'=>1567118,'ext'=>'jpg,png,jpeg,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads');
                    if($info){
                        // 成功上传后 获取上传信息
                        // 输出 jpg
                        //echo $info->getExtension();
                        // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                        //echo $info->getSaveName();
                        // 输出 42a79759f284b767dfcb2a0197904287.jpg
                        $a = $info->getFilename(); 
                        $data['image'] = date('Ymd').'/'.$a;  //如果成功保存头像再压入
                    }else{
                        // 上传失败获取错误信息
                        echo $file->getError();
                    }
                }

        //根据当前登录id修改
        $name = Db::table('user')->where('id',$id)->update($data);
        if($name) {
            return $this->success('修改成功','user/user_info');
        }else{
            return $this->error('修改失败');
        }
    }
    //软删除
    public function delete($id){
      // $restore =new USers;
      // $a = $restore->restore(['id' => 2]);
      // dd($a);
      $m = Users::get($id);
        if($m['qx'] == 1) {
            return $this->error('管理员不能被屏蔽');
        }
        $n = Users::destroy($id);
        if($n) {
          return $this->success('屏蔽成功！');
        }else {
          return $this->error('屏蔽失败！');
        }
    }
    //屏蔽页面
    public function pingbi()
    {
      $res =  Users::onlyTrashed()->paginate(5);
      return view('index/pingbi',['data'=>$res]);
    }
    //取消屏蔽
    public function quxiao(Request $request,$id)
    {
      $restore =new Users();
      $res = $restore->restore(['id' => $id]);
      if ($res){
        $this->success('恢复成功');
     }
      $this->error('恢复失败');
    }
}