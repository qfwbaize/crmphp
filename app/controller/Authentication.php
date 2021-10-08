<?php
declare (strict_types = 1);

namespace app\controller;

use app\common\controller\AdminController;
use think\App;
use think\Request;


class Authentication extends AdminController
{
    use \app\traits\Curd;
    protected $rule = [

        'name'=>'require',
        'license'=>'require',
        'person'=>'require',
        'cert_positive'=>'require',
        'cert_side'=>'require',
        'agree'=>'in:1',
    ];
    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->model = new \app\model\Authentication();

    }
    /**
     * 查看是否已经实名.
     *
     * @param int $company_id
     * @return \think\Response
     */
    public function read(){
        $company_id=$this->AdminId();
        $row=$this->model->where('company_id',$company_id)->find();
        empty($row) && $this->error('没有认证过');

        if($row['status']=='-1'){
            $data = ['code' => -1, 'msg' => '审核失败', 'data' => $row,];
        }elseif($row['status']==0){
            $data = ['code' => 1, 'msg' => '审核中', 'data' =>'' ,];
        }else{
            $data = ['code' => 200, 'msg' => '审核成功', 'data' =>'' ,];
        }
        return json($data);
    }
    /**
     * 提交审核.
     *
     * @param int $company_id
     * @return \think\Response
     */
    public function create()
    {
        $post = $this->request->post();
        $rule = $this->rule;
        $this->validate($post, $rule);
        try {
            $post['company_id']=$this->AdminId();
            $save = $this->model->save($post);
        } catch (\Exception $e) {
            $this->error('保存失败:'.$e->getMessage());
        }
        $save ? $this->success('保存成功') : $this->error('保存失败');
    }

}
