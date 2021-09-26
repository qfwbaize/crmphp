<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class TaskEvidence extends Model
{
    //
    protected $deleteTime = 'delete_time';
    protected $name = "company_task_evidence";
}
