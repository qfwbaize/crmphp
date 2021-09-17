<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;
use app\common\controller\AdminController;

/**
 * @mixin \think\Model
 */
class AdminUser extends Model
{
    //
    protected $name = "business_card";
    protected $deleteTime = false;
    public function getAuthList($company_id)
    {

        $list = (new SystemAuth())
            ->where('status', 1)
            ->where('company_id',$company_id)
            ->select();
        return $list;
    }
}
