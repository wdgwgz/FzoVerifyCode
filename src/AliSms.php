<?php
namespace Fzo;

use Mrgoon\AliyunSmsSdk\Autoload;
use Mrgoon\AliyunSmsSdk\DefaultAcsClient;
use Mrgoon\AliyunSmsSdk\Profile\DefaultProfile;
use Mrgoon\Dysmsapi\Request\V20170525\SendSmsRequest;

class AliSms {
    public function sendSms($to, $template_code, $data, Array $config = null, $outId = '')
    {
        //此处需要替换成自己的AK信息
        if ($config) {
            $accessKeyId     = $config['access_key'];
            $accessKeySecret = $config['access_secret'];
            $signName        = $config['sign_name'];
        } else {
            $accessKeyId     = config('fzo_config.sms.access_key');
            $accessKeySecret = config('fzo_config.sms.access_secret');
            $signName        = config('fzo_config.sms.sign_name');
        }

        //短信API产品名
        $product = "Dysmsapi";
        //短信API产品域名
        $domain = "dysmsapi.aliyuncs.com";
        //暂时不支持多Region
        $region = "cn-hangzhou";

        //初始化访问的acsCleint
        Autoload::config();

        $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);
        DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", $product, $domain);
        $acsClient= new DefaultAcsClient($profile);

        $request = new SendSmsRequest();
        //必填-短信接收号码
        $request->setPhoneNumbers($to);
        //必填-短信签名
        $request->setSignName($signName);
        //必填-短信模板Code
        $request->setTemplateCode($template_code);
        //选填-假如模板中存在变量需要替换则为必填(JSON格式)
        if ($data) {
            $request->setTemplateParam(json_encode($data));
        }

        //选填-发送短信流水号
        if ($outId) {
            $request->setOutId($outId);
        }

        //发起访问请求
        return $acsClient->getAcsResponse($request);
    }
}