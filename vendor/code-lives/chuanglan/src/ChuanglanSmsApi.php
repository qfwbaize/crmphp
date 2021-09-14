<?php

namespace Chuanglan;
/* *
 * 类名：ChuanglanSmsApi
 * 功能：创蓝接口请求类
 * 详细：构造创蓝短信接口请求，获取远程HTTP数据
 * 版本：1.3
 * 日期：2017-04-12
 * 说明：
 * 以下代码只是为了方便客户测试而提供的样例代码，客户可以根据自己网站的需要，按照技术文档自行编写,并非一定要使用该代码。
 * 该代码仅供学习和研究创蓝接口使用，只是提供一个参考。
 */

class ChuanglanSmsApi
{
	//参数的配置 请登录zz.253.com 获取以上API信息 ↑↑↑↑↑↑↑
	/**
	 * 发送短信
	 *
	 * @param string $mobile 		手机号码
	 * @param string $msg 			短信内容
	 * @param string $needstatus 	是否需要状态报告
	 */
//	protected $account;
//    protected $password;
//    protected $send_url;
//    protected $balnce_query_url;
//    protected $variable_url;

	public function __construct($config)
	{
		$this->account = $config['account']; //账号
		$this->password = $config['password']; //密码
		$this->send_url = $config['send_url']; //发送的url
		$this->balnce_query_url = $config['balnce_query_url'] ? $config['balnce_query_url'] : ''; //余额查询url
		$this->variable_url = $config['variable_url'] ? $config['variable_url'] : ''; //发送变量短信url

	}
	public function sendSMS($mobile, $msg, $needstatus = 'true')
	{
		
		//创蓝接口参数
		$postArr = array(
			'account' => $this->account,
			'password' => $this->password,
			'msg' => urlencode($msg),
			'phone' => $mobile,
			'report' => $needstatus,
		);
		$result = $this->curlPost($this->send_url, $postArr);
		return $result;
	}

	/**
	 * 发送变量短信
	 *
	 * @param string $msg 			短信内容
	 * @param string $params 	最多不能超过1000个参数组
	 */
	public function sendVariableSMS($msg, $params)
	{

		//创蓝接口参数
		$postArr = array(
			'account' => $this->account,
			'password' => $this->password,
			'msg' => $msg,
			'params' => $params,
			'report' => 'true',
		);

		$result = $this->curlPost($this->variable_url, $postArr);
		return $result;
	}

	/**
	 * 查询额度
	 *
	 *  查询地址
	 */
	public function queryBalance()
	{

		//查询参数
		$postArr = array(
			'account' => $this->account,
			'password' => $this->password,
		);
		$result = $this->curlPost($this->balnce_query_url, $postArr);
		return $result;
	}

	/**
	 * 通过CURL发送HTTP请求
	 * @param string $url  //请求URL
	 * @param array $postFields //请求参数
	 * @return mixed
	 *
	 */
	private function curlPost($url, $postFields)
	{
		$postFields = json_encode($postFields);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt(
			$ch,
			CURLOPT_HTTPHEADER,
			array(
				'Content-Type: application/json; charset=utf-8', //json版本需要填写  Content-Type: application/json;
			)
		);
		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4); //若果报错 name lookup timed out 报错时添加这一行代码
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$ret = curl_exec($ch);
		if (false == $ret) {
			$result = curl_error($ch);
		} else {
			$rsp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if (200 != $rsp) {
				$result = "请求状态 " . $rsp . " " . curl_error($ch);
			} else {
				$result = $ret;
			}
		}
		curl_close($ch);
		return $result;
	}
}
