<?php
declare (strict_types=1);

namespace app\controller;


use think\Model;
use think\Request;
use app\common\controller\AdminController;
use EasyAdmin\annotation\ControllerAnnotation;
use EasyAdmin\annotation\NodeAnotation;
use think\App;
use app\common\service\AuthService;


class Auth extends AdminController
{
    protected $sort = [
        'sort' => 'desc',
        'id' => 'desc',
    ];
    protected $rule = [

    ];

    use \app\traits\Curd;
    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->model = new \app\model\Auth();
    }



    /**
     * 删除指定资源
     *
     * @param int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
        //
        $admin = new \app\model\AdminUser();
        $list = $admin->where('auth_ids', $id)->select();
        empty($list) && $this->error('管理员没这权限');



            $row = $this->model->whereIn('id', $id)->select();
        empty($row) && $this->error('数据不存在');
            try {
                $save = $row->delete();
            } catch (\Exception $e) {
                $this->error('删除失败');
            }
            $save ? $this->success('删除成功') : $this->error('删除失败');


    }

    /**
     * 根据角色查询授权.
     *
     * @param int $id
     * @return \think\Response
     */
    public function authorizeid($id)
    {


        $authService = app(AuthService::class);

        $currentNode = $authService->getAdminNodeId();
        // dump($currentNode);die;
        $newNodeList = [];
        foreach ($currentNode as $vo) {
            $newNodeList[] = $vo['id'];

        }
        $data = [
            'code' => 200,
            'msg' => '成功　',

            'data' => $newNodeList,
        ];
        return json($data);


    }

    /**
     * @NodeAnotation(title="授权")
     */
    public function authorize($id)
    {
        $row = $this->model->find($id);

        empty($row) && $this->error('数据不存在');

        $list = $this->model->getAuthorizeNodeListByAdminId($id);

        $data = [
            'code' => 200,
            'msg' => '成功　',

            'data' => $list,
        ];
        return json($data);


    }

    /**
     * @NodeAnotation(title="授权保存")
     */
    public function saveAuthorize()
    {
        $id = request()->param('id');
        $node = request()->param('node', "[]");

        $row = $this->model->find($id);

        empty($row) && $this->error('数据不存在');
        try {
            $authNode = new \app\model\AuthNode();
            $authNode->where('auth_id', $id)->delete();
            if (!empty($node)) {
                $saveAll = [];
                foreach ($node as $vo) {
                    $saveAll[] = [
                        'auth_id' => $id,
                        'node_id' => $vo,
                    ];
                }

                $authNode->saveAll($saveAll);
            }

        } catch (\Exception $e) {
            $this->error('保存失败');
        }
        $this->success('保存成功');
    }

}
