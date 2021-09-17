<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class AuthNode extends Model
{
    //
    protected $name = "company_menu_node";
    protected $deleteTime = false;
}
