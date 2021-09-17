<?php
declare (strict_types=1);

namespace app\controller;


use app\model\SystemAdmin;


use Chuanglan\ChuanglanSmsApi;
use think\facade\Cache;
use think\captcha\facade\Captcha;
use think\facade\Config;
use think\facade\Env;
use think\App;
use think\Request;
use app\common\controller\AdminController;
use app\common;
use Firebase\JWT\JWT;


class Login extends AdminController
{
    public function __construct(App $app)
    {


        $this->model = new \app\model\SystemAdmin();

    }

    /**
     * 初始化方法
     */
    public function initialize()
    {
        parent::initialize();
        $action = $this->request->action();
        if (!empty(session('admin')) && !in_array($action, ['out'])) {
            $adminModuleName = config('app.admin_alias_name');
            $this->success('已登录，无需再次登录', [], __url("@{$adminModuleName}"));
        }
    }

    public function code()
    {
        $codes = Config::get('code.config');
        $clapi = new ChuanglanSmsApi($codes);
        $code = mt_rand(1000, 9999);
        $string = '【律师帮帮】您好！验证码是:' . $code;
//设置您要发送的内容：其中“【】”中括号为运营商签名符号，多签名内容前置添加提交
        $post = request()->param();
        $phone = $post['username'];
        $admin = SystemAdmin::where(['phone' => $post['username']])->find();
        if (empty($admin)) {
            $this->error('用户不存在');
        }
        $token = md5($phone);
        $result = $clapi->sendSMS($phone, $string, true);

        if (!is_null(json_decode($result))) {
            $output = json_decode($result, true);

            if (isset($output['code']) && $output['code'] == '0') {
                Cache::store('redis')->set("$token", $code, 600);
                $sms = new \app\model\Sms();
                $sms_name = [];
                $sms_name['phone'] = $phone;
                $sms_name['content'] = $string;
                $sms_name['code'] = $code;
                $sms_name['type'] = 2;
                $sms_name['end_time'] = time() + 600;

                $sms->save($sms_name);
                return array('code' => '200', 'status' => true, 'msg' => '发送成功');
            } else {
                // var_dump($output);die;

                return json(array('code' => '0', 'status' => false, 'msg' => '发送失败'));
            }
        } else {
            return array('code' => '-1', 'status' => false, 'msg' => '发送失败');
        }

    }

    /**
     * 用户登录
     * @return string
     * @throws \Exception
     */
    public function index()
    {

        if (request()->param()) {
            $post = request()->param();
            $rule = [
                'username|手机号' => 'require',
                'password|密码' => 'require',
                'keep_login|是否保持登录' => 'require',

            ];

            $this->validate($post, $rule);
            $admin = SystemAdmin::where(['phone' => $post['username']])->find();
            if (empty($admin)) {
                $this->error('用户不存在');
            }
            $phone = $post['username'];
            $token = md5($phone);
            $code = Cache::store('redis')->get("$token", $admin);
            if ($post['password'] != $code) {

                $this->error('验证码有误');
            }
            if ($post['password'] == $code) {
                $sms = new \app\model\Sms();
                $status = [];
                $status['status'] = 1;
                $sms->where('phone', $phone)->where('code', $code)->where('status', '0')->save($status);

            }
            if ($admin->status == 0) {
                $this->error('账号已被禁用');
            }
            $admin->login_num += 1;
            $admin->save();
            $admin = $admin->toArray();
            //unset($admin['password']);
            $admin['expire_time'] = $post['keep_login'] == 1 ? true : time() + 72000;
            $admin['token'] = signToken($phone);
            $admin['secret'] = $token;
            $serect = $admin['secret'];
            //$redis = new Redis();die;
            //$datas = $redis->get('admin'.$admin);

            Cache::store('redis')->set("Login$serect", $admin, $admin['expire_time']);
            $datas = Cache::store('redis')->get("Login$phone", $admin);

            //session('admin', $admin);
            $data = [
                'code' => 200,
                'msg' => '登陆成功',

                'data' => $datas,
            ];
            return json($data);

        }



    }

    public function Userlogin()
    {
        if (request()->param()) {
            $post = request()->param();
            $sereact = $post['secret'];
            $token = $post['token'];
            $data = Cache::store('redis')->get("Login$sereact");
            //dump($data);die;
            if ($data == NULL) {
                $datas = ['code' => 0, 'msg' => '您还没有登陆',];
                return json($datas);
            }
            if ($data['token'] == null) {
                $datas = ['code' => 0, 'msg' => 'token过期',];
                return json($datas);
            }
            if ($data['token'] != $token) {
                $datas = ['code' => 0, 'msg' => 'token错误',];
                return json($datas);

            }
            $data = [
                'code' => 200,
                'msg' => '登陆成功',

                'data' => $data,
            ];
            return json($data);
        }
    }
    /**
     * 用户登录
     * @return string
     * @throws \Exception
     */
    public function LoginOut()
    {
        $serect = request()->header();
        $rule = [
            'secret|账号' => 'require',

        ];
        $this->validate($serect, $rule);
        $secret=$serect['secret'];
        Cache::store('redis')->delete("Login$secret");
    }

}
