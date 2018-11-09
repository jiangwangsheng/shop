<?php
namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;
class Users extends Model
{
	use SoftDelete;
	protected $table = 'user';
	protected $deleteTime = 'delete_time';

}