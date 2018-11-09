<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function upload($img)
{
	 if($img){
		 	    $info = $img->validate(['size'=>1567118,'ext'=>'jpg,png,jpeg,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads');
                    if($info){
                    	  $a = $info->getFilename();
                    	  //返回图片名+日期
                    	  return(date('Ymd').'/'.$a);
                    	 }else{
                        // 上传失败获取错误信息
                        echo $file->getError();
                 }
           }
}