<?php
namespace app\admin\model;
use think\Model;
class Articles extends Model
{
	protected $table = 'articles';
	protected $createTime = 'create_time';
}