<?php
declare (strict_types = 1);

namespace app\controller;

use app\common\controller\AdminController;
use think\App;
use think\facade\Filesystem;
use think\Request;

class Contract extends AdminController
{
    protected $rule = [
    ];
    use \app\traits\Curd;
    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->model = new \app\model\Contract();
    }
    /**
     * @NodeAnotation(title="列表")
     */
    public function index()
    {

        list($page, $limit, $where) = $this->buildTableParames();
        $company_id=1367;
        $count = $this->model
            ->whereOr('company_id',$company_id)
            ->whereOr('company_id','0')
            ->where($where)
            ->count();
        $list = $this->model
            ->whereOr('company_id',$company_id)
            ->whereOr('company_id','0')
            ->where($where)
            ->page($page, $limit)
            ->order('company_id','ASC')
            ->select();


        $data = [
            'code'  => 200,
            'msg'   => '成功',
            'total' => $count,
            'data'  => $list,
        ];
        return json($data);

    }
    public function upload(){
        $file = $this->request->file('file');

        if($file==NULL){
        $this->error('没上传文件');
        }
        $temp=explode(".",$_FILES['file']['name']);
        $extension =end($temp);
        if(!in_array($extension,array("pdf","jpg","png","docs","doc"))){

            $this->error('不合法');
        }
        $saveName = Filesystem::disk('aliyun')->putFile('uploads',$file,'uploads');

        return json(['code'=>200,'msg'=>'成功','data'=>['uploads'=>$saveName]]);
    }
}
