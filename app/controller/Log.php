<?php
declare (strict_types = 1);

namespace app\controller;


use think\App;
use think\Request;
use app\common\controller\AdminController;

class Log extends AdminController
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->model = new \app\model\Log();
    }
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
        [$page, $limit, $where] = $this->buildTableParames();



        // todo TP6框架有一个BUG，非模型名与表名不对应时（name属性自定义），withJoin生成的sql有问题

        $count = $this->model

            ->with('admin')
            ->where($where)
            ->count();
        $list = $this->model
            ->with('admin')
            ->where($where)
            ->page($page, $limit)
            ->order($this->sort)
            ->select();

        $data = [
            'code'  => 200,
            'msg'   => '成功',
            'total' => $count,
            'data'  => $list,
        ];
        return json($data);
    }

}
