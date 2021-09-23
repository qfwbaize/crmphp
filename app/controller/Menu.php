<?php
declare (strict_types=1);

namespace app\controller;

use think\facade\Cache;
use think\Model;
use think\Request;
use app\common\controller\AdminController;
use EasyAdmin\annotation\ControllerAnnotation;
use EasyAdmin\annotation\NodeAnotation;
use think\App;

class Menu extends AdminController
{
    protected $rule = [
        ];
    use \app\traits\Curd;
    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->model = new \app\model\Menu();
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
        list($page, $limit, $where) = $this->buildTableParames();
        $count = $this->model
            ->where('status', '>', '0')
            ->where('pid',0)
            ->where($where)
            ->count();
        $list = $this->model
            ->where($where)
            ->where('status', '>', '0')
            ->field('id,pid,name,status,current,path,icon')
            ->select()
            ->toArray();
        $newNodeList = [];
        foreach ($list as $vo) {

            if ($vo['pid'] == 0) {


                $children = [];
                foreach ($list as $v) {
                    if ($v['status'] > 0 && $v['pid'] == $vo['id']) {


                        $children[] = $v;
                    }
                }

                !empty($children) && $vo['children'] = $children;
                $newNodeList[] = $vo;
            }
        }


        $data = [
            'code' => 200,
            'msg' => '成功　',
            'total' => $count,
            'data' => $newNodeList,
        ];
        return json($data);
    }

    /**
     * 根据角色获取菜单
     *
     * @param int $id 管理员id
     * @return \think\Response
     */
    public function menu($id)
    {


        if ($id == 1) {
            $list = $this->model->getAuthorizeNodeListByAdminId($id);
        } else {
            $list = $this->model->getAuthorizeNodeListByAdminmenuId($id);
        }

        $data = [
            'code' => 200,
            'msg' => '成功　',

            'data' => $list,
        ];
        return json($data);
    }
}
