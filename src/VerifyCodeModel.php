<?php
/**
 * Created by PhpStorm.
 * User: gemor
 * Date: 2020/4/20
 * Time: 14:38
 */
namespace Fzo;

use Illuminate\Database\Eloquent\Model;

class VerifyCodeModel extends Model{


    protected $fillable = ['phone', 'code', 'type', 'status'];


    # 发送成功
    const Status_Send_Success = 'Send_Success';
    const Status_Verify_Success = 'Verify_Success';
    # 发送失败
    const Status_Send_Error = 'Error';

    protected $table = 'verify_code';

}