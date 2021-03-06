<?php
/**
 * Created by PhpStorm.
 * User:  barry
 * Email: 530027054@qq.com
 * Date:  2019/3/20
 * Time:  15:31
 */

namespace app\home\controller;


use think\Controller;


class Base extends Controller
{
    protected $userInfo;
    protected $needUser = false;

    public function __construct()
    {
        parent::__construct();
        if ($this->needUser) {
            $this->userInfo = getUser();
            if ($this->userInfo === false){
                throwError( '请重新登录',401);
            }
        }

    }


    public function ErrorReturn($msg = 'error', $data = '', $code = 400)
    {
        $res['msg'] = $msg;
        $res['data'] = $data;
        $res['code'] = $code;
        return json($res);
    }

    public function SuccessReturn($msg = 'success', $data = '', $code = 200)
    {
        $res['msg'] = $msg;
        $res['data'] = $data;
        $res['code'] = $code;
        return json($res);
    }
}