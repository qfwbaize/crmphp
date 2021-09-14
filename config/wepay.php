<?php
use think\facade\Env;
return [
    //支付
    'config'  => [

        'app_id' =>Env::get('WXPAYJSAPI.APP_ID'),
        'mch_id' => Env::get('WXPAYJSAPI.MCH_ID'),
        'mchapp_id' => Env::get('WXPAYJSAPI.MCHAPP_ID'),
        'app_key' => Env::get('WXPAYJSAPI.KEY'),
        'app_secret' => Env::get('WXPAYJSAPI.APP_SECRET'),
        //仅退款、撤销订单时需要
        'sslcert_path' => Env::get('WXPAYJSAPI.SLLCERT_PATH'),
        'sslkey_path' => Env::get('WXPAYJSAPI.SLLKEY_PATH'),
    ],
];