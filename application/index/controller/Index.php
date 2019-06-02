<?php

//namespace app\index\controller;
//
//use app\admin\model\article;
//use think\Controller;
//
//class Index extends Controller
//{
//    public function sy()
//    {
//        //分类的id
//        $id = $this->request->param('id');
////        $this->category(2);
//        $info = article::where('category_id',$id)->find();
////        $info = article::where('category_id',$id)->find();
//
//        $this->assign('info',$info);
//
//        $this->assign('id',$id);
//        return $this->fetch();
//    }
//
//
//}


namespace app\index\controller;

use app\admin\model\article;
use app\admin\model\category;
use think\Controller;

class Index extends Controller
{
    public function sy()
    {

        $info = \think\Db::table('category')->select();
        $this->assign('info', $info);

        $list = article::with('category')->paginate(10);
        $this->assign('list', $list);

        return $this->fetch();
    }
    public function title()
    {
        $id = $this->request->param('id');

        $info = category::select();
        $this->assign('info', $info);

        $list = article::with('category')->where('category_id', $id)->paginate(10);
        $this->assign('list', $list);

        return $this->fetch();
    }
    public function content()
    {
        $list = category::select();

//        print_r($list);
//        exit;
        $this->assign('list',$list);

        //文章id
        $id=$this->request->param('id');

        $info=article::with('category')->find();

        $info->setInc('hits');
        $this->assign('info',$info);

        return $this->fetch();
    }
}

