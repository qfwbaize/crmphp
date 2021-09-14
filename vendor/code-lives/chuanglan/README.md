### 使用说明
```php
$clapi = new \Chuanglan\ChuanglanSmsApi($config);
$code = mt_rand(1000, 9999);
$string = '【创蓝科技】您好！验证码是:' . $code;
//设置您要发送的内容：其中“【】”中括号为运营商签名符号，多签名内容前置添加提交
$result = $clapi->sendSMS($phone, $string, true);
if (!is_null(json_decode($result))) {
  $output = json_decode($result, true);
  if (isset($output['code']) && $output['code'] == '0') {
    return array('status' => true, 'msg' => '发送成功');
  } else {
    return array('status' => false, 'msg' => '发送失败');
  }
} else {
  return array('status' => false, 'msg' => '发送失败');
}
```
