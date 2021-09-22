<?php
declare (strict_types=1);

namespace app\controller;


use app\model\BusinessCard;
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
     * @NodeAnotation(title="列表")
     */
    public function index()
    {

        list($page, $limit, $where) = $this->buildTableParames();
        $count = $this->model
            ->where('company_id',$this->AdminId())
            ->where($where)
            ->count();
        $list = $this->model
            ->where($where)
            ->where('company_id',$this->AdminId())
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
        $list = $admin->where('auth_id', $id)->select();
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
     * @NodeAnotation(title="添加")
     */
    public function create()
    {

        $post = $this->request->post();

        $rule = ['name'=>'require'];
        $this->validate($post, $rule);
        try {
            $post['company_id']=$this->AdminId();
            $save = $this->model->save($post);
        } catch (\Exception $e) {
            $this->error('保存失败:'.$e->getMessage());
        }
        $save ? $this->success('保存成功') : $this->error('保存失败');

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
    public function authorize()
    {
        $business=new BusinessCard();
       $card_id=$this->CardId();
       $business=$business->where('card_id',$card_id)->find();
        empty($business) && $this->error('数据不存在');
       if($business['auth_id']==null){
           $this->error('没权限');
       }
        if($business['role_id']>0){
            $list = $this->model->getAuthorizeNodeListByAdminId('0');
        }else{
        $list = $this->model->getAuthorizeNodeListByAdminId($business['auth_id']);}

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
        $node = request()->param('node');
        $node=explode(',',$node);


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
                        'menu_id' => $vo,
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
