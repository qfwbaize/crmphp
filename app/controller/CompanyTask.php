<?php
declare (strict_types = 1);

namespace app\controller;

use app\common\controller\AdminController;
use app\model\BusinessCard;
use app\model\Company;
use app\model\Cooperation;
use app\model\Reward;
use app\model\TaskContent;
use app\model\TaskEvidence;
use app\model\TaskPeople;
use app\model\TaskReceive;
use think\App;
use think\Request;

class CompanyTask extends AdminController
{
    use \app\traits\Curd;

    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->model = new \app\model\Task();
    }

    /**
     * 查看发布的任务
     *
     * @return \think\Response
     */
    public function release_index()
    {
        //
        list($page, $limit, $where) = $this->buildTableParames();
        $company_id = $this->AdminId();
        $count = $this->model
            ->where($where)
            ->where('company_id', $company_id)
            ->count();
        $list = $this->model
            ->where($where)
            ->where('company_id', $company_id)
            ->page($page, $limit)
            ->order($this->sort)
            ->select();
        $task_id = $this->model
            ->distinct(true)
            ->where('company_id', $company_id)
            ->field('id')
            ->buildSql(true);
        $pid = $this->model
            ->distinct(true)
            ->where("pid IN {$task_id}")
            ->select();
        $newNodeList = [];
        $company = new Company();
        $business = new BusinessCard();
        $content = new TaskContent();
        $receive= new TaskReceive();

        foreach ($list as $vo) {
            $business_name = $business->where('card_id', $vo['card_id'])->find();
            if (!empty($business_name)) {
                $vo['name'] = $business_name['name'];
            }
            $receive_id=$receive->where('task_id',$vo['id'])->find();
            if(!empty($receive_id)){
                $company_names = $company->where('company_id', $receive_id['company_task_id'])->find();
                $vo['company_task_name']=$company_names['company_name'];
            }
            $company_name = $company->where('company_id', $vo['company_id'])->find();
            if (!empty($company_name)) {
                $vo['company_name'] = $company_name['company_name'];
            }
            $task_content = $content->where('task_id', $vo['id'])->field('money')->find();
            if (!empty($task_content)) {
                $vo['money'] = $task_content['money'];
            }
            if ($vo['pid'] == 0) {
                $children = [];
                foreach ($pid as $v) {
                    if ($v['pid'] == $vo['id']) {
                        $business_task_name = $business->where('card_id', $v['card_id'])->find();
                        if (!empty($business_task_name)) {
                            $v['name'] = $business_task_name['name'];
                        }
                        $company_task_name = $company->where('company_id', $v['company_id'])->find();
                        if (!empty($company_name)) {
                            $v['company_name'] = $company_task_name['company_name'];
                        }
                        $v['start_time'] = date('Y-m-d', $v['start_time']);
                        $v['end_time'] = date('Y-m-d', $v['end_time']);
                        $children[] = $v;
                    }
                }
                !empty($children) && $vo['children'] = $children;
                $newNodeList[] = $vo;


                $vo['start_time'] = date('Y-m-d', $vo['start_time']);
                $vo['end_time'] = date('Y-m-d', $vo['end_time']);
            }
        }

        $data = [
            'code' => 200,
            'msg' => '成功',
            'total' => $count,
            'data' => $newNodeList,
        ];
        return json($data);

    }

    /**
     * 查看接受的任务
     *
     * @return \think\Response
     */
    public function accept_index()
    {
        $revice = new TaskReceive();
        $company_id = $this->AdminId();
        $task_id = $revice
            ->distinct(true)
            ->where('company_task_id', $company_id)
            ->field('task_id')
            ->buildSql(true);
        $pid = $this->model
            ->distinct(true)
            ->where("pid IN {$task_id}")
            ->select();
        list($page, $limit, $where) = $this->buildTableParames();
        $count = $this->model
            ->where($where)
            ->where("id IN {$task_id}")
            ->count();
        $list = $this->model
            ->distinct(true)
            ->where("id IN {$task_id}")
            ->page($page, $limit)
            ->order($this->sort)
            ->where($where)
            ->select();
        $newNodeList = [];
        $company = new Company();
        $business = new BusinessCard();
        $content = new TaskContent();
        foreach ($list as $vo) {
            $task_content = $content->where('task_id', $vo['id'])->field('money')->find();
            if (!empty($task_content)) {
                $vo['money'] = $task_content['money'];
            }
            $business_name = $business->where('card_id', $vo['card_id'])->find();
            if (!empty($business_name)) {
                $vo['name'] = $business_name['name'];
            }
            $company_name = $company->where('company_id', $vo['company_id'])->find();
            if (!empty($company_name)) {
                $vo['company_name'] = $company_name['company_name'];
            }
            if ($vo['pid'] == 0) {
                $children = [];
                foreach ($pid as $v) {
                    if ($v['pid'] == $vo['id']) {
                        $business_task_name = $business->where('card_id', $v['card_id'])->find();
                        if (!empty($business_task_name)) {
                            $v['name'] = $business_task_name['name'];
                        }
                        $company_task_name = $company->where('company_id', $v['company_id'])->find();
                        if (!empty($company_name)) {
                            $v['company_name'] = $company_task_name['company_name'];
                        }
                        $v['start_time'] = date('Y-m-d', $v['start_time']);
                        $v['end_time'] = date('Y-m-d', $v['end_time']);
                        $children[] = $v;
                    }
                }
                !empty($children) && $vo['children'] = $children;
                $newNodeList[] = $vo;

                $vo['start_time'] = date('Y-m-d', $vo['start_time']);
                $vo['end_time'] = date('Y-m-d', $vo['end_time']);
            }
        }
        $data = [
            'code' => 200,
            'msg' => '成功',
            'total' => $count,
            'data' => $newNodeList,
        ];
        return json($data);
    }

    /**
     * 查看正在工作得员工
     *
     * @return \think\Response
     */
    public function task_people($task_id)
    {


        list($page, $limit, $where) = $this->buildTableParames();
        $taskpeople = new TaskPeople();
        $count = $taskpeople
            ->where($where)
            ->where('task_id', $task_id)
            ->count();
        $list = $taskpeople
            ->where($where)
            ->where('task_id', $task_id)
            ->page($page, $limit)
            ->order($this->sort)
            ->select();
        $business=new BusinessCard();
        foreach ($list as $value){
            $card_name=$business->where('card_id',$value['card_id'])->find();
            $value['name']=$card_name['name'];
        }
        $data = [
            'code' => 200,
            'msg' => '成功',
            'total' => $count,
            'data' => $list,
        ];
        return json($data);


    }
    /**
     * 对工作得员工进行审批
     *
     * @return \think\Response
     */
    public function task_people_edit($id){
        $taskpeople = new TaskPeople();
        $row = $taskpeople->find($id);
        empty($row) && $this->error('数据不存在');

        $post = $this->request->post();
        $rule = [
            'status'=>'in:1,2,3,4'
        ];
        $this->validate($post, $rule);
        try {
            $save = $row->save($post);
        } catch (\Exception $e) {
            $this->error('保存失败');
        }
        $save ? $this->success('提交成功') : $this->error('提交失败');
    }
    /**
     * 查看每个员工得证据
     *
     * @return \think\Response
     */
    public function task_people_evidence($card_id){
        list($page, $limit, $where) = $this->buildTableParames();
        $get = $this->request->get();
        $rule = [
            'task_id'=>'require'
        ];
        $this->validate($get, $rule);
        $taskevidence = new TaskEvidence();
        $count = $taskevidence
            ->where($where)
            ->where('card_id', $card_id)
            ->where('task_id',$get['task_id'])
            ->count();
        $list = $taskevidence
            ->where($where)
            ->where('card_id', $card_id)
            ->where('task_id',$get['task_id'])
            ->page($page, $limit)
            ->order($this->sort)
            ->select();
        $business=new BusinessCard();
        foreach ($list as $value){
            $card_name=$business->where('card_id',$value['card_id'])->find();
            $value['name']=$card_name['name'];
        }
        $data = [
            'code' => 200,
            'msg' => '成功',
            'total' => $count,
            'data' => $list,
        ];
        return json($data);

    }


    /**
     * 查看任务详情
     *
     * @return \think\Response
     */
    public function read($id)
    {
        $row = $this->model->find($id);
        empty($row) && $this->error('数据不存在');
        $content = new TaskContent();
        $company = new Company();
        $business=new BusinessCard();
        $task_content = $content->where('task_id', $row['id'])->find();
        empty($task_content) && $this->error('任务详情丢失');
        $business_name=$business->where('card_id',$row['card_id'])->find();
        $company_task_name = $company->where('company_id', $row['company_id'])->find();
        if (!empty($company_task_name)) {
            $row['company_name'] = $company_task_name['company_name'];
        }
        $receive= new TaskReceive();
        $receive_id=$receive->where('task_id',$row['id'])->find();
        if(!empty($receive_id)){
            $company_names = $company->where('company_id', $receive_id['company_task_id'])->find();
            $row['company_task_name']=$company_names['company_name'];
        }
        $row['name'] = $business_name['name'];
        $row['logo'] = $business_name['logo'];
        $row['content'] = $task_content['content'];
        $row['num'] = $task_content['num'];
        $row['pro_id'] = $task_content['pro_id'];
        $row['contract'] = $task_content['contract'];
        $row['money'] = $task_content['money'];
        $row['reward'] = $task_content['reward'];
        $row['explain'] = $task_content['explain'];
        $row['start_time'] = date('Y-m-d', $row['start_time']);
        $row['end_time'] = date('Y-m-d', $row['end_time']);
        $data = ['code' => 200, 'msg' => '成功', 'data' => $row,];
        return json($data);

    }

    /**
     * 修改任务状态
     *
     * @return \think\Response
     */
    public function task_update($id)
    {
        $row = $this->model->find($id);
        empty($row) && $this->error('数据不存在');

        $post = $this->request->post();
        $rule = [
            'status' => 'in:1,2,3,4'
        ];
        $this->validate($post, $rule);
        try {
            $save = $row->save($post);
        } catch (\Exception $e) {
            $this->error('保存失败');
        }
        $save ? $this->success('操作成功') : $this->error('操作失败');

    }

    /**
     * 查看合作得机构
     *
     * @return \think\Response
     */
    public function company()
    {
        $company = new Company();
        $company_per = new Cooperation();
        $company_id = $this->AdminId();
        $company_launch = $company_per->whereOr('launch_id', $company_id)->whereOr('receive_id', $company_id)->field('launch_id')
            ->buildSql(true);
        $company_receive = $company_per->whereOr('launch_id', $company_id)->whereOr('receive_id', $company_id)->field('receive_id')
            ->buildSql(true);
        $list_id = $company
            ->distinct(true)
            ->where('status','=','1')
            ->whereor("company_id IN {$company_launch}")
            ->whereor("company_id IN {$company_receive}")
            ->select();
        $list = [];


        foreach ($list_id as $v) {
            if ($v['company_id'] != $company_id) {

                $list[] = ['company_id' => $v['company_id'], 'company_name' => $v['company_name'], 'company_logo' => $v['company_logo']];
            }
        }
        $data = ['code' => 200, 'msg' => '成功', 'data' => $list,];
        return json($data);


    }

    /**
     * 机构任务发布.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
        $post = $this->request->post();
        $rule = [
            'pid' => 'require',
            'type' => 'require',
            'pattern' => 'require',
            'title' => 'require',

            'content' => 'require',

            'pro_id' => 'require',
            'contract' => 'require',
            'money' => 'require',
            'start_time' => 'require',
            'end_time' => 'require',
        ];
        $this->validate($post, $rule);
        try {
            $content = new TaskContent();
            $post['company_id'] = $this->AdminId();
            $task = [
                'pid' => $post['pid'],
                'type' => $post['type'],
                'pattern' => $post['pattern'],
                'company_id' => $post['company_id'],
                'title' => $post['title'],
                'card_id' => $this->CardId(),
                'status' => 0,
                'start_time' => strtotime($post['start_time']),
                'end_time' => strtotime($post['end_time']),
            ];

            $save = $this->model->save($task);
            $id = $this->model->id;
            $taskcontent = [
                'task_id' => $id,
                'title' => $post['title'],
                'content' => $post['content'],
                'num' => $post['num'],
                'pro_id' => $post['pro_id'],
                'contract' => $post['contract'],
                'money' => $post['money'],
                'reward' => $post['reward'],
                'explain' => $post['explain']

            ];
            $content->save($taskcontent);
            switch ($post['type']) {
                case "1":
                    $rule=['company_task_id' => 'require',];
                    $this->validate($post, $rule);
                    $receive = new TaskReceive();
                    $taskrecive = [
                        'company_task_id' => $post['company_task_id'],
                        'task_id' => $id,

                    ];
                    $receive->save($taskrecive);
                    break;
                case "2":
                    $rule=['card_id' => 'require',];
                    $this->validate($post, $rule);
                    $people = new TaskPeople();
                    $card_id = explode(',', $post['card_id']);
                    $saveAll = [];
                    foreach ($card_id as $v) {
                        $saveAll[] = [
                            'task_id' => $id,
                            'card_id' => $v,
                            'status' => 1,
                            'company_id' => $this->AdminId()
                        ];

                    }
                    $people->saveAll($saveAll);


                    break;
            }


        } catch (\Exception $e) {
            $this->error('保存失败:' . $e->getMessage());
        }
        $save ? $this->success('保存成功') : $this->error('保存失败');
    }
    /**
     * 奖励钱.
     *
     * @return \think\Response
     */
    public function reward(){

        $post = $this->request->post();
        $rule = [
            'card_id|员工客户'=>'require',
            'task_id|任务id'=>'require',
            'money|金额'=>'require',
        ];
        $post['task_card_id']=$this->CardId();
        $this->validate($post, $rule);
        try {
            $reward=new Reward();
            $save = $reward->save($post);
        } catch (\Exception $e) {
            $this->error('保存失败:'.$e->getMessage());
        }
        $save ? $this->success('成功') : $this->error('失败');
    }

}
