<?php
/**
 * Created by PhpStorm.
 * User: gemor
 * Date: 2020/4/20
 * Time: 12:03
 */


namespace Fzo;

use Carbon\Carbon;

class VerifyCode {

    private const Code_Success = 1; # 正确返回
    private const Code_Param_Error = 1001; # 参数非法
    private const Code_Send_Max_Num = 1002; # 超过最大发送数
    private const Code_Send_Intervals = 1003; # 两条短信间隔时间太短
    private const Code_Send_Expectation = 1004; # 数据保存异常
    private const Code_Send_Error = 1005; # 短信发送失败
    private const Code_Verify_Error = 1006; # 验证失败
    private const Code_Verify_Invalid = 1007; # 验证码已失效
    private const Code_Verify_CodeError = 1008; # 验证码错误
    private const Code_Verify_Expire = 1009; # 验证码已经过期

    private $config = [
        1    => 'success',
        1001 => '参数非法!',
        1002 => '超过最大发送数',
        1003 => '发送过于频繁',
        1004 => '数据保存异常',
        1005 => '短信发送失败',
        1006 => '验证码不存在',
        1007 => '验证码已失效',
        1008 => '验证码错误',
        1009 => '验证码已过期'
    ];

    /**
     * 发送 验证码
     * @param int $phone 手机号
     * @param string $type 自定义类型(业务1，业务2...)
     * @param string $template_code 短信模板
     * @return array
     */
    function send( int $phone, $type = 'Default', $template_code = '' ){
        if ( ! $template_code ) {
            $template_code = config('fzo_config.sms.templete_code');
        }

        //校验参数
        if ( ! is_numeric($phone) or strlen($phone) != 11 ) {
            return $this->_return( self::Code_Param_Error );
        }

        // 查询已发送列表
        $sendCount = VerifyCodeModel::where('phone', $phone)
            ->where('created_at', '>', Carbon::now()->startOfDay())
            ->where('created_at', '<', Carbon::now()->endOfDay())
            ->OrderBy('id', 'desc')
            ->get();

        // 如果发送列表有数据
        if ( count($sendCount) > config('fzo_config.sms.day_num') ){
            return $this->_return( self::Code_Send_Max_Num );
        }

        $last_send = $sendCount->first();
        // 最后一次发送时间不足两条短信最低间隔时间，返回发送过于频繁
        if((time() - strtotime($last_send['created_at'])) <  config('fzo_config.sms.intervals')){
            return $this->_return( self::Code_Send_Intervals );
        }

        # 生成验证码
        $code = rand(1111, 9999);

        # 数据库保存
        $resModel = VerifyCodeModel::create([
            'phone'  => $phone,
            'code'   => $code,
            'type'   => $type
        ]);
        if ( ! $resModel ) {
            return $this->_return( self::Code_Send_Expectation );
        }

        $alisms = new AliSms();
        $response = $alisms->sendSms( $phone, $template_code, ['code' => $code]);

        $resModel->response = json_encode( $response );
        if ( $response->Message != 'OK' ){
            $resModel->status = VerifyCodeModel::Status_Send_Error;

            $resModel->save();

            return $this->_return( self::Code_Send_Error );
        } else {

            $resModel->status = VerifyCodeModel::Status_Send_Success;
            $resModel->save();

            return $this->_return( self::Code_Success );
        }
    }

    /**
     * 短信 验证码 验证
     *
     * @param $phone 手机号
     * @param $code 验证码
     * @param $type 类型
     * @return array
     */
    function verify( int $phone, $code, $type = 'Default' ){
        if ( ! is_numeric($phone) or strlen($phone) != 11 ) {
            return $this->_return( self::Code_Param_Error );
        }

        $send = VerifyCodeModel::where('phone', $phone)->where('type', $type)->orderBy('id', 'desc')->first();
        //  不存在
        if ( ! $send ){
            return $this->_return( self::Code_Verify_Error );
        }

        // 已经失效
        if ( $send->status == VerifyCodeModel::Status_Verify_Success ){
            return $this->_return( self::Code_Verify_Invalid );
        }

        // 验证码不相当
        if ( $send->code != $code ){
            return $this->_return( self::Code_Verify_CodeError );
        }

        // 过期
        if ( time() - strtotime( $send->created_at) > config('fzo_config.sms.expire_second') ) {
            return $this->_return(( self::Code_Verify_Expire ));
        }

        $send->status = VerifyCodeModel::Status_Verify_Success;
        $send->save();

        return $this->_return( self::Code_Success );
    }

    private function _return( $code ){
        return ['code' => $code, 'msg' => $this->config[$code]];
    }

}