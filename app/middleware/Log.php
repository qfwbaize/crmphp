<?php
declare (strict_types = 1);

namespace app\middleware;

use app\model\Menu;
use app\service\SystemLogService;
use app\common\controller\AdminController;

class Log extends AdminController
{

    /**
     * 敏感信息字段，日志记录时需要加密
     * @var array
     */
    protected $sensitiveParams = [
        'password',
        'password_again',
    ];

    public function handle($request, \Closure $next)
    {

            $method = strtolower($request->method());
            if (in_array($method, ['post', 'put', 'delete'])) {
                $url = $request->url();
                $ip = request()->ip();

                $params = $request->param();
                if (isset($params['s'])) {
                    unset($params['s']);
                }
                foreach ($params as $key => $val) {
                    in_array($key, $this->sensitiveParams) && $params[$key] = password($val);
                }


                $menu=new Menu();
                $title=$menu->where('path',$url)->find();
                if(!empty($title)){
                    $title=$title['name'];
                }

                $data = [
                    'admin_id'    => $this->AdminId(),
                    'url'         => $url,
                    'title'       =>$title,
                    'method'      => $method,
                    'ip'          => $ip,
                    'content'     => json_encode($params, JSON_UNESCAPED_UNICODE),
                    'useragent'   => $_SERVER['HTTP_USER_AGENT'],
                    'create_time' => time(),
                ];
                \app\service\SystemLogService::instance()->save($data);
            }

        return $next($request);
    }
}
