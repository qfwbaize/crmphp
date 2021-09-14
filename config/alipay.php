<?php
    use think\facade\Env;
    return [
        //支付
        'config'  => [

            'app_id' =>Env::get('ALIPAY.ALI_PAY_APPID'),
            'ALI_PAY_NOTIFYURL' => Env::get('ALIPAY.ALI_PAY_NOTIFYURL'),
            'rsa_public_key' => Env::get('ALIPAY.ALI_PAY_KEY'),
            'rsa_private_key' => Env::get('ALIPAY.ALI_PAY_PKEY'),

        ],
    ];