<?php
/**
 * Created by PhpStorm.
 * User: gemor
 * Date: 2020/4/20
 * Time: 14:02
 */


return [
    'key' => "value",  # 配置文件
    # 阿里云短信发送 配置参加
    "sms" => [
        'access_key'        => env('ALIYUN_SMS_AK'), // accessKey
        'access_secret'     => env('ALIYUN_SMS_AS'), // accessSecret
        'sign_name'         => env('ALIYUN_SMS_SIGN_NAME'), // 签名
        'day_num'           => 5, # 当天 同一个 type 发送最大条数
        'intervals'         => 60, # 两条短信间隔时间 单位：秒
        'expire_second'     => 300, # 过期时间
        'templete_code'     => 'SMS_181295085' , # 默认发送 模板
    ],
];