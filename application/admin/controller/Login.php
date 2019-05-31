<?php
/**
 * Created by PhpStorm.
 * User: qingyun
 * Date: 2019/5/31
 * Time: 8:57 AM
 */


namespace  app\admin\controller;

use think\Controller;

class Login extends Controller
{
    /**
     * 退出登录
     */
    public function out()
    {
        session('adminLoginInfo', null);  // 清除session中名字叫adminLoginInfo
        $this->redirect('admin/Login/in');  //跳转到 admin/Login/in ，redirect不是php内置的函数。而是thinkphp框架里的
    }


    /**
     * 登录操作
     */
    public function in()
    {
        $re = $this->request;


        //处理post请求
        if ($re->isPost()) {


//            return $re->param();   //返回console.log(e){mobile: "13245678910", password: "123456"}   __proto__: Object


            //接受数据
            $data = $re->only(['mobile', 'password']);

            //自定规则

            $rule = [
                'mobile' => 'require|mobile',
                'password' => 'require|length:6,12'
            ];

            $msg = [
                'mobile.require' => '手机号为必填项',
                'mobile.mobile' => '手机号填写有误',
                'password.require' => '请输入密码',
                'password.length' => '密码长度过长或者过短',
            ];

            //验证数据

            $info = $this->validate($data, $rule, $msg);

            if ($info !== true) {

                //如果输入数据类型格式不对，返回 前端index.html文件的 console.log(e)
                //返回的数据为 {code: 0, msg: "手机号填写有误", data: "", url: "", wait: 3}
                //输入正确，返回 undefined
                return $this->error($info);
            }

            //调用数据表admin中的mobile等于前端data传来的mobile
            $admin = admin::where('mobile', $data['mobile'])->find();
            /**
            app\admin\model\admin Object
            (
            [data] => Array
            (
            [id] => 1
            [mobile] => 13245678910
            [password] => $2y$10$tBeeCB8ZkslsvMCjcm8FB.IIZ4H6pg3V0365PlcuHjiLfvc.1EQ/e
            )

            [relation] => Array
            (
            )

            )
             * return print_r($admin,true);   返回的是个数组对象
             * 前端文件in.html 第92行 console.log(e)不能注释掉
             */
//            return print_r($admin,true);

            if (!$admin) {
                $this->error('你输入的手机或密码有误');
            }

            //哈希验证密码，前端data的password与数据表admin中的password验证，验证通过，登录成功
            if (password_verify($data['password'], $admin->password)) {
                //登录成功

                session('adminLoginInfo', $admin);  // 把$admin写入session中，并给它起个名字adminLoginInfo
                $this->success('成功', url('admin/Index/index'));   //登录成功，跳转到 admin/Index/index
            } else {
                $this->error('你输入的手机号或密码有误');
            }
        }


        //处理GET请求
        if ($re->isGet()) {

            return $this->fetch();
        }

    }

}