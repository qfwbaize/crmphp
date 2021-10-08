<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Authentication extends Model
{
    //
    protected $name = "company_authentication";
    protected $deleteTime = false;
}
