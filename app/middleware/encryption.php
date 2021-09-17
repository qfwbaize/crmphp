<?php
declare (strict_types = 1);

namespace app\middleware;

use app\Request;
use Firebase\JWT\JWT;
use think\facade\Cache;
use app\common\service\AuthService;
use think\facade\Config;
use think\facade\Validate;

class encryption
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next )
    {


        //

        //dump($adminConfig);die;
        $token = Request()->header('token');


        $data=Cache::store('redis')->get("ONE_STAND:USER:login_token:$token");

        //dump($data);die;
        if($data==NULL){
            $datas = ['code' => 0, 'msg' => '您还没有登陆',];
            return  json($datas);
        }
        $data=json_decode($data,true);
        if($data['token']==null){
            $datas = ['code' => 0, 'msg' => 'token过期',];
            return  json($datas);
        }
        if($data['token']!=$token){
            $datas = ['code' => 0, 'msg' => 'token错误',];
            return  json($datas);

        }
        return $next($request);
    }
}
