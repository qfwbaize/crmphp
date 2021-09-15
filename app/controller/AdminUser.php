<?php
declare (strict_types=1);

namespace app\controller;

use think\facade\Cache;
use think\Facade\Db;
use think\facade\View;
use think\Request;
use think\App;
use app\common\controller\AdminController;

class AdminUser extends AdminController
{
    protected $rule = [

    ];
    use \app\traits\Curd;
    protected $sort = [
        'sort' => 'desc',
        'id' => 'desc',
    ];

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->model = new \app\model\AdminUser();
    }
    public function index()
    {

        list($page, $limit, $where) = $this->buildTableParames();
        $count = $this->model
            ->where($where)
            ->count();
        $list = $this->model
            ->where($where)
            ->page($page, $limit)
            ->select();
        $auth= new \app\model\Auth();
        foreach ($list as $v){
            $auth_name=$auth->where('id',$v['auth_ids'])->find();
            $v['auth_name']=$auth_name['title'];
        }

        $data = [
            'code'  => 200,
            'msg'   => '成功',
            'total' => $count,
            'data'  => $list,
        ];
        return json($data);

    }

    /**
     * 查询所有角色
     *
     * @param int $id
     * @return \think\Response
     */
    public function adminauth()
    {

        $data = [
            'code' => 200,
            'msg' => '成功　',
            'data' => $this->model->getAuthList(),
        ];
        return json($data);
    }
}
