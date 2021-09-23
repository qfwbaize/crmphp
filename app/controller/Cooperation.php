<?php
declare (strict_types = 1);

namespace app\controller;

use app\common\controller\AdminController;
use app\model\Company;
use think\App;
use think\Request;

class Cooperation extends AdminController
{
    protected $rule = [
    ];
    use \app\traits\Curd;
    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->model = new \app\model\Cooperation();
    }

    /**
     * @NodeAnotation(title="合作列表")
     */
    public function index()
    {

        list($page, $limit, $where) = $this->buildTableParames();
        $count = $this->model
            ->where($where)
            ->count();
        $list = $this->model
            ->where($where)
            ->page($page, $limit)
            ->order($this->sort)
            ->select();
        $company= new Company();
        foreach ($list as $v){
            $launch=$company->where('company_id',$v['launch_id'])->find();
            $receive=$company->where('company_id',$v['receive_id'])->find();
            $v['launch_name']=$launch['company_name'];
            $v['receive_name']=$receive['company_name'];

            if($v['status']==1){
                $v['status_name']='已审批';
            }elseif($v['status']==0){
                $v['status_name']='审批中';
            }else{
                $v['status_name']='拒绝';
            }
        }
        $data = [
            'code'  => 200,
            'msg'   => '成功',
            'total' => $count,
            'data'  => $list,
        ];
        return json($data);

    }
    public function company(){
        $company=new Company();
        $list = $company
            ->field("company_id,company_name")
            ->select();
        $data = [
            'code'  => 200,
            'msg'   => '成功',
            'data'  => $list,
        ];
        return json($data);
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
            $company= new Company();

                $launch=$company->where('company_id',$row['launch_id'])->find();
                $receive=$company->where('company_id',$row['receive_id'])->find();
            $row['launch_name']=$launch['company_name'];
            $row['receive_name']=$receive['company_name'];
                if($row['launch_id']==$this->AdminId()){
                    $row['type']=1;
                }else{
                    $row['type']=2;
                }
                if($row['status']==1){
                    $row['status_name']='已审批';
                }elseif($row['status']==0){
                    $row['status_name']='审批中';
                }else{
                    $row['status_name']='拒绝';
                }

            $data = ['code' => 200, 'msg' => '成功', 'data' => $row,];


        } else {

            $data = ['code' => 0, 'msg' => '失败', 'data' => '',];


        }
        return json($data);
    }
}
