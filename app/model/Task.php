<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Task extends Model
{
    //
    protected $deleteTime = 'delete_time';
    protected $name = "company_task";
}
