<?php

namespace app\controller;

use app\BaseController;
use think\facade\Cache;

class Index extends BaseController
{
   public function index(){
       $data = Cache::store('redis')->get("ONE_STAND:USER:login_token:0HQTCtpgNi_7013");
       //$data=unserialize($data);
       return $data;
   }
}
