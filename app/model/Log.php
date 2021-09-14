<?php
declare (strict_types = 1);

namespace app\model;


use app\common\model\TimeModel;
/**
 * @mixin \think\Model
 */
class Log extends TimeModel
{
    //
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->name = 'system_log';
    }


    public function admin()
    {
        return $this->belongsTo('app\model\AdminUser', 'admin_id', 'id');
    }
}
