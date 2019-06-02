<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------


//前端用户注册的路由
Route::rule('up', 'index/Sign/up')->method('GET,POST');

//前端用户登录的路由
Route::rule('in','index/Sign/in')->method('GET,POST');






//后台用户登录的路由
Route::rule('login', 'admin/Login/in')->method('GET,POST');



Route::group('', function (){

    Route::get('logout', 'admin/Login/out');
//后台首页
Route::get('admin$', 'admin/Index/index');

//console页
Route::get('admin-console', 'admin/Index/console');

//添加分类
Route::rule('admin-add-category', 'admin/Index/addCategory')->method('GET,POST');

//分类列表
Route::get('admin-list-category', 'admin/Index/categoryList');

//树形分类展开
Route::get('admin-tree-category', 'admin/Index/categoryTree');

//添加文章
Route::rule('admin-article-add', 'admin/Article/add')->method('GET,POST');

//ajax获取文章分类
Route::post('admin-article-category', 'admin/Article/ajaxCategory');

//文章状态
Route::post('admin-article-change-status', 'admin/Article/changeStatus');

//上传图片
Route::post('admin-article-upload-image','admin/Article/uploadImage');

Route::rule('admin-article-umupload-image','admin/Article/umUploadImage')->method('GET,POST');


})->middleware('Login');






Route::get('sy/[:id]$','index/index/sy');
Route::get('content/[:id]$','index/index/content');
Route::get('title/[:id]','index/index/title');
//限制变量的规则，可选参数不收规则限制
//Route::get('news/:id','Index/index/news')->pattern(['id'=>'\d+']);
//Route::get('news/:id','Index/index/news',[],['id'=>'\d+']);
Route::get('content/index/[:id]','Index/index/content');
Route::get('about/:id','Index/index/about');
