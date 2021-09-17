<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class BusinessCard extends Model
{
    //
    protected $deleteTime = 'delete_time';
    protected $name = "business_card";
}
