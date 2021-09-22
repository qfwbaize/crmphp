<?php
declare (strict_types=1);

namespace app\controller;

use app\model\BusinessCard;
use app\model\Company;
use app\model\Message;
use think\App;
use think\Request;
use app\common\controller\AdminController;

class Messages extends AdminController
{
    use \app\traits\Curd;

    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->model = new \app\model\Message();
    }

    /**
     * 根据不同得管理员看到不同得消息
     *
     * @return \think\Response
     */
    public function index()
    {
        list($page, $limit, $where) = $this->buildTableParames();
        $cardid = $this->CardId();
        $message = new \app\model\Messages();
        $count = $this->model
            ->where($where)
            ->where('to_id', $cardid)
            ->count();
        $list = $this->model
            ->where($where)
            ->where('to_id', $cardid)
            ->page($page, $limit)
            ->order($this->sort)
            ->select();
        $company = new Company();
        foreach ($list as $value) {
            $messages = $message->where('id', $value['messages_id'])->find();
            if ($messages['type'] == 1) {
                $value['type_name'] = '系统消息';
            } elseif ($messages['type'] == 2) {
                $company_name = $company->where('company_id', $value['from_id'])->field('company_name')->find();
                $value['type_name'] = $company_name['company_name'];


            } else {
                $value['type_name'] = '平台消息';
            }
            $value['content'] = $messages['content'];
            $value['type'] = $messages['type'];
        }
        $value['create_time']=$messages['create_time'];

        $data = [
            'code' => 200,
            'msg' => '成功',
            'total' => $count,
            'data' => $list,
        ];
        return json($data);
    }

    /**
     * @NodeAnotation(title="添加")
     */
    public function create()
    {

        $post = $this->request->post();
        $rule = ['content' => 'require',

        ];
        $this->validate($post, $rule);
        try {
            $messages = new \app\model\Messages();
            $post['type'] = 2;
            $companyid = $this->AdminId();

            if ($post['type_status'] == 2) {
                $busines = explode(',', $post['card_id']);
                $business = [];
                foreach ($busines as $k => $v) {
                    $business[] = [
                        'card_id' => $v
                    ];
                }

            } else {
                $business = new BusinessCard();
                $business = $business->where('company_id', $companyid)->field('card_id')->select();
            }

            $saveall = [];
            $save = $messages->save($post);
            foreach ($business as $value) {
                $saveall[] = [
                    'messages_id' => $messages->id,
                    'from_id' => $companyid,
                    'to_id' => $value['card_id']
                ];

            }
            $message = $this->model->saveAll($saveall);

        } catch (\Exception $e) {
            $this->error('保存失败:' . $e->getMessage());
        }
        $save ? $this->success('保存成功') : $this->error('保存失败');

    }
    public function  read($id){
        $row = $this->model->find($id);
        $message = new \app\model\Messages();
        $messages = $message->where('id', $row['messages_id'])->find();
        $row['content']=$messages['content'];
        if (!empty($row)) {
            $status['is_read']=1;
            $save=$row->save($status);
            $data = ['code' => 200, 'msg' => '成功', 'data' => $row,];


        } else {

            $data = ['code' => 0, 'msg' => '失败', 'data' => '',];


        }
        return json($data);

    }

}
