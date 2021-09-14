<?php
declare (strict_types=1);

namespace app\controller;


use think\App;
use app\common\controller\AdminController;

class Appraise extends AdminController
{
    protected $rule = [
    ];
    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->model = new \app\model\Appraise();

    }

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
        foreach ($list as $vo) {
            $userid = new \app\model\User();
            $user_name = $userid->field('nickname,telphone')->where('id', $vo['uid'])->find();
            $att_name = $userid->field('nickname,telphone')->where('id', $vo['att_id'])->find();
            $order= new \app\model\Order();
            $order = $order->where('id', $vo['order_id'])->find();
            empty($user_name) && $this->error('用户丢失');
            empty($att_name) && $this->error('律师丢失');
            empty($order) && $this->error('订单丢失');

            $vo['user_name'] = $user_name['nickname'];
            $vo['user_tel'] = $user_name['telphone'];
            $vo['order_no']=$order['order_no'];
            if ($att_name) {
                $vo['laywer_name'] = $att_name['nickname'];
                $vo['laywer_tel'] = $att_name['telphone'];
            }
        }
        $data = [
            'code' => 200,
            'msg' => '成功　',
            'total' => $count,
            'data' => $list,
        ];
        return json($data);


    }
}
