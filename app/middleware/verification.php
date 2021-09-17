<?php
declare (strict_types = 1);

namespace app\middleware;

use think\facade\Validate;

class verification
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        //
        $header = Request()->header();

        $validate = Validate::rule([

            'time|时间' => 'require',
        ]);
        if (!$validate->check($header)) {
            $datas = ['code' => 0, 'msg' =>  $validate->getError(),];
            return json($datas);
        }
        $validates = Validate::rule(
            [

                'key|密钥' => 'require|'.base64_encode(md5($header['time'].env('LOGINPATH.LOGIN_KEY'))),

            ]
        );
        if (!$validates->check($header)) {
            $datas = ['code' => 0, 'msg' =>  $validates->getError(),];
            return json($datas);
        }
        return $next($request);
    }
}
