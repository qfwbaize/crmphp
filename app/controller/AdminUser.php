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
        $company_id=['company_id'=>$this->AdminId()];

        $count = $this->model
            ->where($where)
            ->where($company_id)
            ->count();
        $list = $this->model
            ->where($where)
            ->where($company_id)
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
    public function edit($card_id)
    {
        $row = $this->model->where('card_id',$card_id)->find();
        empty($row) && $this->error('数据不存在');

        $post = $this->request->post();
        $rule = [];
        $this->validate($post, $rule);
        try {
            $save = $row->save($post);
        } catch (\Exception $e) {
            $this->error('保存失败');
        }
        $save ? $this->success('保存成功') : $this->error('保存失败');


    }
    /**
     * 显示指定的资源
     *
     * @param int $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
        $row = $this->model->find($id);

        if (!empty($row)) {

            $data = ['code' => 200, 'msg' => '成功', 'data' => $row,];


        } else {

            $data = ['code' => 0, 'msg' => '失败', 'data' => '',];


        }
        return json($data);
    }

    /**
     * @NodeAnotation(title="删除")
     */
    public function delete($card_id)
    {
        $row = $this->model->whereIn('card_id', $card_id)->select();
        $row->isEmpty() && $this->error('数据不存在');
        try {
            $save = $row->delete();
        } catch (\Exception $e) {
            $this->error('删除失败');
        }
        $save ? $this->success('删除成功') : $this->error('删除失败');
    }
    /**
     * 查询所有角色
     *
     * @param int $id
     * @return \think\Response
     */
    public function adminauth()
    {
        $company_id=$this->AdminId();

        $data = [
            'code' => 200,
            'msg' => '成功　',
            'data' => $this->model->getAuthList($company_id),
        ];
        return json($data);
    }
}
