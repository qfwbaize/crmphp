<?php
declare (strict_types = 1);

namespace app\controller;

use think\App;
use think\Request;
use app\common\controller\AdminController;

class Messages extends AdminController
{
    use \app\traits\Curd;
    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->model = new \app\model\Messages();
    }
    /**
     * 根据不同得管理员看到不同得消息
     *
     * @return \think\Response
     */
    public function index()
    {
        list($page, $limit, $where) = $this->buildTableParames();
        $count = $this->model
            ->where($where)
            ->count();
        $list = $this->model
            ->where($where)
            ->select();
      return  json($list);
    }


}
