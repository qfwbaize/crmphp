<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * @mixin \think\Model
 */
class Contract extends Model
{
    //
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $name = "company_contract";
}
