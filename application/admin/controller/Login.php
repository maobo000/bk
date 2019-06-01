<?php
/**
 * Created by PhpStorm.
 * User: qingyun
 * Date: 19/5/29
 * Time: 下午3:39
 */

namespace app\admin\controller;


use app\admin\model\admin;
use think\Controller;

class Login extends Controller{
    public function out(){
        session('adminLoginInfo',null);
        $this->redirect('admin/Login/in');

    }

    public function in(){
        $res = $this->request;

        if ($res->isPost()){
            $data = $res->only(['mobile','password']);

            $rule = [
                'mobile'  =>'require|mobile',
                'password'=>'require|length:6,12'
            ];
            $msg = [
                'mobile.require'  =>'手机号为必填项',
                'mobile.mobile'    =>'手机号填写有误',
                'password.require' =>'请您输入密码',
                'password.length'  =>'密码长度违规'
            ];
            $info = $this->validate($data,$rule,$msg);

            if ($info !== true){
                return $this->error($info);
            }

            $a = admin::where('mobile',$data['mobile'])->find();
            if (!$a){
                $this->error('您输入的手机号或密码有误');
            }
            if (password_verify($data['password'],$a->password)){
                session('adminLoginInfo',$a);
                $this->success('成功',url('admin/Index/index'));
            }else{
                $this->error('您输入的手机号或密码有误');
            }
        }

        if ($res->isGet()){
            return $this->fetch();
        }


    }

}
