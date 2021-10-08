<?php
// 应用公共文件

use app\common\service\AuthService;
use think\facade\Cache;
use Firebase\JWT\JWT;
use think\facade\Config;
use OSS\OssClient;
use OSS\Core\OssException;


if (!function_exists('__url')) {

    /**
     * 构建URL地址
     * @param string $url
     * @param array $vars
     * @param bool $suffix
     * @param bool $domain
     * @return string
     */
    function __url(string $url = '', array $vars = [], $suffix = true, $domain = false)
    {
        return url($url, $vars, $suffix, $domain)->build();
    }
}

if (!function_exists('password')) {

    /**
     * 密码加密算法
     * @param $value 需要加密的值
     * @param $type  加密类型，默认为md5 （md5, hash）
     * @return mixed
     */
    function password($value)
    {
        $value = sha1('blog_') . md5($value) . md5('_encrypt') . sha1($value);
        return sha1($value);
    }

}

if (!function_exists('xdebug')) {

    /**
     * debug调试
     * @param string|array $data 打印信息
     * @param string $type 类型
     * @param string $suffix 文件后缀名
     * @param bool $force
     * @param null $file
     */
    function xdebug($data, $type = 'xdebug', $suffix = null, $force = false, $file = null)
    {
        !is_dir(runtime_path() . 'xdebug/') && mkdir(runtime_path() . 'xdebug/');
        if (is_null($file)) {
            $file = is_null($suffix) ? runtime_path() . 'xdebug/' . date('Ymd') . '.txt' : runtime_path() . 'xdebug/' . date('Ymd') . "_{$suffix}" . '.txt';
        }
        file_put_contents($file, "[" . date('Y-m-d H:i:s') . "] " . "========================= {$type} ===========================" . PHP_EOL, FILE_APPEND);
        $str = (is_string($data) ? $data : (is_array($data) || is_object($data)) ? print_r($data, true) : var_export($data, true)) . PHP_EOL;
        $force ? file_put_contents($file, $str) : file_put_contents($file, $str, FILE_APPEND);
    }
}

if (!function_exists('sysconfig')) {

    /**
     * 获取系统配置信息
     * @param $group
     * @param null $name
     * @return array|mixed
     */
    function sysconfig($group, $name = null)
    {
        $where = ['group' => $group];
        $value = empty($name) ? Cache::get("sysconfig_{$group}") : Cache::get("sysconfig_{$group}_{$name}");
        if (empty($value)) {
            if (!empty($name)) {
                $where['name'] = $name;
                $value = \app\admin\model\SystemConfig::where($where)->value('value');
                Cache::tag('sysconfig')->set("sysconfig_{$group}_{$name}", $value, 3600);
            } else {
                $value = \app\admin\model\SystemConfig::where($where)->column('value', 'name');
                Cache::tag('sysconfig')->set("sysconfig_{$group}", $value, 3600);
            }
        }
        return $value;
    }
}

if (!function_exists('array_format_key')) {

    /**
     * 二位数组重新组合数据
     * @param $array
     * @param $key
     * @return array
     */
    function array_format_key($array, $key)
    {
        $newArray = [];
        foreach ($array as $vo) {
            $newArray[$vo[$key]] = $vo;
        }
        return $newArray;
    }

}
//生成验签
function signToken($uid)
{
    $key = '!@#$%*&';         //这里是自定义的一个随机字串，应该写在config文件中的，解密时也会用，相当    于加密中常用的 盐  salt
    $token = array(
        "iss" => $key,        //签发者 可以为空
        "aud" => '',          //面象的用户，可以为空
        "iat" => time(),      //签发时间
        "nbf" => time() + 3,    //在什么时候jwt开始生效  （这里表示生成100秒后才生效）
        "exp" => time() + 200, //token 过期时间
        "data" => [           //记录的userid的信息，这里是自已添加上去的，如果有其它信息，可以再添加数组的键值对
            'uid' => $uid,
        ]
    );
    //  print_r($token);
    $jwt = JWT::encode($token, $key, "HS256");  //根据参数生成了 token
    return $jwt;

}
function checkToken($token){
    $key='!@#$%*&';
    $status=array("code"=>2);
    try {
        JWT::$leeway = 60;//当前时间减去60，把时间留点余地
        $decoded = JWT::decode($token, $key, array('HS256')); //HS256方式，这里要和签发的时候对应
        $arr = (array)$decoded;
        $res['code']=1;
        $res['data']=$arr['data'];
        return $res;

    } catch(\Firebase\JWT\SignatureInvalidException $e) { //签名不正确
        $status['msg']="签名不正确";
        return $status;
    }catch(\Firebase\JWT\BeforeValidException $e) { // 签名在某个时间点之后才能用
        $status['msg']="token失效";
        return $status;
    }catch(\Firebase\JWT\ExpiredException $e) { // token过期
        $status['msg']="token失效";
        return $status;
    }catch(Exception $e) { //其他错误
        $status['msg']="未知错误";
        return $status;
    }
}

if (!function_exists('auth')) {

    /**
     * auth权限验证
     * @param $node
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    function auth($node = null)
    {
        $authService = new AuthService(session('admin.id'));
        $check = $authService->checkNode($node);
        return $check;
    }

}
if(!function_exists('new_oss')){
    //获取配置项，并赋值给对象$config
    function new_oss(){
        //获取配置项，并赋值给对象$config
        $config=Config::get('aliyun_oss.config');
        //实例化OSS

        $oss=new \OSS\OssClient($config['accessId'],$config['accessSecret'],$config['endpoint']);
        return $oss;
    }
}
if(!function_exists('moveFile')) {
    function moveFile($from_bucket, $from_object, $to_bucket,$to_object)
    {

        try {
            //没忘吧，new_oss()是我们上一步所写的自定义函数
            $ossClient = new_oss();

            $a=$ossClient->copyObject($from_bucket, $from_object, $to_bucket, $to_object);
            return $a;
        } catch (OssException $e) {

            return $e->getMessage();
        }

    }
}
    if(!function_exists('chatrecord')) {
        function chatrecord($bucket, $object, $filePath)
        {

            try {
                //没忘吧，new_oss()是我们上一步所写的自定义函数
                $ossClient = new_oss();

                $ossClient->uploadFile($bucket, $object, $filePath);
            } catch (OssException $e) {

                return $e->getMessage();
            }
            //否则，完成上传操作
            return true;
        }
    }
if(!function_exists('https_request')) {
    function https_request($url,$data = null){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
}
if(!function_exists('isFile')) {
    function isFile($bucket, $object)
    {

        try {
            //没忘吧，new_oss()是我们上一步所写的自定义函数
            $ossClient = new_oss();

            $a=$ossClient->doesObjectExist(env('FILESYSTEM.FROM_BUCKET'),$bucket. $object);
            return $a;
        } catch (OssException $e) {

            return $e->getMessage();
        }
        //否则，完成上传操作

    }

}
if(!function_exists('uploads')) {
    function uploads()
    {
return date('Ym') . '/' .mt_rand(100, 999);

    }

}
if(!function_exists('upload')) {
    function upload()
    {
        return mt_rand(100, 999);

    }

}
