<?php
declare (strict_types = 1);

namespace app\middleware;

use app\common\service\AuthService;
use think\facade\Cache;
use think\facade\Config;

class Auths
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
        $adminConfig = Config::get('code');
        $sereact = Request()->header('secret');
        $data=Cache::store('redis')->get("Login$sereact");
        $adminId=$data['auth_ids'];

        /** @var AuthService $authService */
        $authService = app(AuthService::class, ['adminId' => "$adminId"]);

        $currentNode = $authService->getCurrentNode();

        $currentController = parse_name($request->controller());

// 验证权限

        if (!in_array($currentController, $adminConfig['no_auth_controller']) &&
            !in_array($currentNode, $adminConfig['no_auth_node'])) {
            $check = $authService->checkNode($currentNode);


            if($check==false){
                $datas = ['code' => 0, 'msg' => '无权限访问',];
                return  json($datas);

            }




        }

        return $next($request);
    }
}
