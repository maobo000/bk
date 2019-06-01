<?php
/**
 * Created by PhpStorm.
 * User: qingyun
 * Date: 19/5/30
 * Time: 下午7:05
 */

namespace app\index\controller;


use think\Request;
use think\Controller;

class Sign extends Controller
{
    public function in(){
        $res = $this->request;

        if ($res->isGet()){
            return $this->fetch();
        }

        if ($res->isPost()){
            $a = $res->param('a');
            $password = $res->param('password');

            $m = new \app\index\model\User();

            if (preg_match('/^1[3578]\d{9}$/',$a,$math)){
                $res = $m->where('mobile',$a)->find();

            }else{
                $res =$m->where('email',$a)->find();
            }

            if ($res){
                if (password_verify($password,$res->password)){
                    session('userLoginInfo',$res);

                    $this->success('登陆成功',url('index/Index/index'));
                }else{

                    $this->error('您输入的账号或密码有误');
                }
            }else{

                $this->error('您输入的账号或密码有误');
            }
        }
    }

    public function up(){
        $res = $this->request;

        if ($res->isGet()){
            return $this->fetch();
        }
        if ($res->isPost()){
            $plan = [
                'agree'   =>  'require',
                'mobile'  =>  'require|mobile|unique:user',
                'password'=>  'require|confirm:repass|length:6,12'
            ];
            $slip = [
                'agree.require'   => '你需要同意注册协议',
                'mobile.require'  => '请输入你的手机号',
                'mobile.mobile'   => '你输入的手机有误',
                'mobile.unique'   => '该手机号已被注册',
                'password.require'=> '请输入密码',
                'password.confirm'=> '密码输入不一致',
                'password.length' => '密码应在6-12位之间'
            ];

            $info = $this->validate($res->param(),$plan,$slip);

            if ($info !==true){
                $this->error($info);
            }

            $m = new \app\index\model\User();
            $m->mobile = $res->param('mobile');
            $m->password =password_hash($res->param('password'),1);

            $m->nickname = '新用户'.random_int(1000,9999);

            if ($m->save()){
                $this->success('注册成功',url('index/Sign/in'));
            }else{
                $this->error('注册失败');
            }
        }
    }
}
