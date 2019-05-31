<?php
/**
 * Created by PhpStorm.
 * User: qingyun
 * Date: 2019/5/31
 * Time: 10:32 AM
 */

namespace  app\index\controller;

use think\Controller;

class Sign extends Controller
{



    /**
     * 登录处理
     */
    public function in()
    {

        $request = $this->request;

        //当前用户使用post方式请求的时候
        if ($request->isPost()){

            //接收前端传来的数据
            $account = $request->param('account');
            $password = $request->param('password');


            //获取以一个User对象
            $m = new \app\index\model\User();

            //如果用户输入的是个手机号格式的账号
            if (preg_match('/^1[3-9]\d{9}$/', $account, $match)){

                //手机号登陆
                //以手机号为查询条件
                $res = $m->where('mobile', $account)->find();
            }else{
                //其他情况都按邮箱登录处理
                $res = $m->where('email', $account)->find();
            }

            if ($res){

                //查到用户信息后，验证密码输入的是否正确

                if (password_verify($password, $res->password)){
                    //登录成功， 将用户信息写入到session中
                    session('userLoginInfo', $res);
                    $this->success('登录成功', url('index/Index/index'));

                }else{
                    $this->error('您输入的用户名或者密码有误');
                }
            }else{
                $this->error('您输入的用户名或者密码有误');
            }
        }




        if ($request->isGet()){
            return $this->fetch();
        }

    }


    /**
     * 注册处理
     */
    public function up()
    {

        $request = $this->request;

//        print_r($this->request->method());
//        var_dump($this->request->isPost());
//        var_dump($this->request->isGet());
//        print_r($this->request->param());

        if($request->isGet()){
            return $this->fetch();
        }

        if ($request->isPost()) {

            $rule = [
                'agree' => 'require',
                'mobile' => 'require|mobile|unique:user',
                'password' => 'require|confirm:repass|length:6,12'
            ];
            $msg = [
                'agree.require' => '您需要同意注册协议',
                'mobile.require' => '手机号为必填项',
                'mobile.mobile' => '请输入正确的手机号',
                'mobile.unique' => '该手机号已被使用',
                'password.require' => '密码为必填项',
                'password.confirm' => '两次密码输入不一致',
                'password.length' => '密码的长度应在6-12位之间'
            ];

            $info = $this->validate($request->param(), $rule, $msg);
            if ($info !== true) {
//                return $info;
                $this->error($info);
//                $this->success($info, 'http://www.baidu.com');
            }

            //判断手机号是否已被注册

//            if (\think\Db::table('user')->where('mobile', $request->param('mobile'))->find()){
//                $this->error('该手机号已被使用');
//            }


            //使用模型进行数据的插入
            $m = new \app\index\model\User();

//            var_dump($m->where('mobile', $request->param('mobile'))->find());
//            exit();

            $m->mobile = $request->param('mobile');
            $m->password = password_hash($request->param('password'), PASSWORD_DEFAULT);
            $m->nickname = '新用户' . random_int(10000, 9999999);
            if ($m->save()) {
                $this->success('注册成功', url('index/Sign/in'));
            } else {
                $this->error('注册失败');
            }
        }
    }



}