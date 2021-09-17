<?php
declare (strict_types = 1);

namespace app\controller;

use think\Request;

class Pay
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
        $pay = new \Yurun\PaySDK\Weixin\SDK($params);
        // 支付接口
        $request = new \Yurun\PaySDK\Weixin\Native\Params\Pay\Request;
        $request->body = 'test'; // 商品描述
        $request->out_trade_no = 'test' . mt_rand(10000000,99999999); // 订单号
        $request->total_fee = 1; // 订单总金额，单位为：分
        $request->spbill_create_ip = '127.0.0.1'; // 客户端ip
        $request->notify_url = $GLOBALS['PAY_CONFIG']['pay_notify_url']; // 异步通知地址
// 调用接口
        $result = $pay->execute($request);
        $shortUrl = $result['code_url'];
        var_dump($result, $shortUrl);
    }

}
