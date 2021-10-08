<?php
declare (strict_types = 1);

namespace app\controller;

use app\common\controller\AdminController;
use app\model\Company;
use think\App;
use think\Request;

class Pdf extends AdminController
{
    use \app\traits\Curd;

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->model = new \app\model\TaskEvidence();
        $adminid=$this->AdminId();
    }
    public function evidence(){
        list($page, $limit, $where) = $this->buildTableParames();
        $company_id = 1;

        $list = $this->model
            ->where($where)
            ->page($page, $limit)
            ->order($this->sort)
            ->select();

        $company=new Company();
        $company_name=$company->where('company_id',$company_id)->find();
        $name=$company_name['company_name'];
        $logo=$company_name['company_logo'];

   foreach ($list as $v){
       $content.=$this->table_evidence($v);
   }


        $this->pdf($content);die;
    }

}
