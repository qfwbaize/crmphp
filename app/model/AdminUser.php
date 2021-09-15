<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class AdminUser extends Model
{
    //
    protected $name = "business_card";
    protected $deleteTime = false;
    public function getAuthList()
    {
        $list = (new SystemAuth())
            ->where('status', 1)
            ->select();
        return $list;
    }
}
