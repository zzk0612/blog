<?php
/**
 * Created by PhpStorm.
 * User: qingyun
 * Date: 19/5/20
 * Time: 下午2:46
 */

namespace app\admin\controller;

use app\admin\model\category;
use think\Controller;

class Index extends Controller
{
    public function index()
    {
        return $this->fetch();
    }

    public function console()
    {
        return $this->fetch();
    }


    public function addCategory()
    {

        $re = $this->request;

        if($re->isGet()){
            $pid = $re->param('id', 0);

            if (empty($pid)){

                $this->assign('parentName', '顶级分类');  //模板变量赋值，顶级分类赋值给 'parentName'
            }else{

                //到数据表查 id 对应的 pid，然后查 'name'
                $parentName = category::where('id', $pid)->value('name');
                if (!$parentName){
                    $this->error('非法操作');
                }
                $this->assign('parentName', $parentName);
            }
            $this->assign('pid', $pid);
            return $this->fetch();
        }

        if ($re->isPost()){

            /**
             * // 接受 add_category.html 文件中的 分类名称的 name属性，在37行
             * {"name":"字符串"}
             */
//            return json($re->param());


            $name = $re->param('name');
            $pid = $re->param('pid', 0);

            if (mb_strlen($name, 'utf-8') > 10 || mb_strlen($name, 'utf-8') < 2){
                $this->error('分类名称长度应在2-10位之间');
            }
            //同一个父级下不能重名
            $where = ['pid'=>$pid, 'name'=>$name];
            if (category::where($where)->find()){
                $this->error('该分类已存在');
            }

            if ($pid == 0){

                //顶级分类的处理
                $level = 0;
                $path = '0-';

            }else{
                $parent = category::where('id', $pid)->find();
                if (empty($parent)){
                    //如果通过用户传的pid找不到信息，说明是一个非法操作
                    $this->error('非法操作');
                }
                $level = $parent->level + 1;
                $path = $parent->path . $pid.'-';
            }


            //入库
            $data = [
                'name'=>$name,
                'pid' => $pid,
                'level' => $level,
                'path' => $path
            ];
            if (category::create($data)){
                $this->success('成功');
            }else{
                $this->error('失败');
            }
        }
    }

    public function categoryList()
    {

        //如果使用的是ajax请求
        if ($this->request->isAjax()){
            $pid = $this->request->param('id', 0);
            $list = category::where('pid', $pid)->select();

            $str = '';
            foreach ($list as $v){
                $space = '';
                for ($i=0; $i< $v['level']; $i++){
                    $space .= '&nbsp;&nbsp;&nbsp;&nbsp;';
                }
                $url = url('admin/Index/addCategory', ['id'=>$v['id']]);
                $str .= <<<DDDD
                    <tr class="x{$pid}">
                        <td>{$v['id']}</td>
                        <td>{$space}|--{$v['name']}</td>
                        <td><a href="{$url}">添加</a></td>
                        <td><a data-id="{$v['id']}" class="point-e children" data-op="plus"><i class="fa fa-plus"></i></a></td>
                    </tr>
DDDD;
            }

            return $str;
        }else{
            $list = category::where('pid', 0)->select();
            $this->assign('list', $list);
            return $this->fetch();
        }
    }

    public function categoryTree()
    {


        $all = category::select()->toArray();

        /**
         * Array(
         *        [0] => Array ( [id] => 1 [name] => 中国新闻 [pid] => 0 [path] => 0- [level] => )
         *        [1] => Array ( [id] => 2 [name] => 朝鲜新闻 [pid] => 0 [path] => 0- [level] => )
         *        [2] => Array ( [id] => 3 [name] => 美国信息 [pid] => 0 [path] => 0- [level] => )
         *        [3] => Array ( [id] => 4 [name] => 德国新闻 [pid] => 0 [path] => 0- [level] => )
         *        [4] => Array ( [id] => 5 [name] => 法国新闻 [pid] => 0 [path] => 0- [level] => )
         *        [5] => Array ( [id] => 6 [name] => 河南新闻 [pid] => 1 [path] => 0-1- [level] => 1 )
         *        [6] => Array ( [id] => 7 [name] => 湖北新闻 [pid] => 1 [path] => 0-1- [level] => 1 )
         *        [7] => Array ( [id] => 8 [name] => 河北新闻 [pid] => 1 [path] => 0-1- [level] => 1 )
         *        [8] => Array ( [id] => 9 [name] => 郑州新闻 [pid] => 6 [path] => 0-1-6- [level] => 2 )
         *        [9] => Array ( [id] => 10 [name] => 开封新闻 [pid] => 6 [path] => 0-1-6- [level] => 2 )
         *       [10] => Array ( [id] => 11 [name] => 管城新闻 [pid] => 9 [path] => 0-1-6-9- [level] => 3 )
         *       [11] => Array ( [id] => 12 [name] => 金水新闻 [pid] => 9 [path] => 0-1-6-9- [level] => 3 )
         *       [12] => Array ( [id] => 13 [name] => 汉阳新闻 [pid] => 7 [path] => 0-1-7- [level] => 2 )
         *       [13] => Array ( [id] => 14 [name] => 武昌新闻 [pid] => 7 [path] => 0-1-7- [level] => 2 )
         *     )
         */
//        print_r($all);

        $new = $this->toTree($all);


//        print_r($new);     //树形数组（数组下有子数组）

        $this->assign('data', json_encode($new));
        return $this->fetch();
    }

    /**
     * 将一个记录层级结构的二维数组转成树形结构
     * @param array $data 记录有层级信息的二维数组
     * @param int $pid 从pid为哪个开始
     * @return array
     */
    protected function toTree($data, $pid = 0)
    {
        $newData = [];
//
        foreach ($data as $v){
//
            if ($v['pid'] == $pid){
                //找儿子们
                $v['text'] = $v['name'];
                $v['children'] = $this->toTree($data, $v['id']);
                $newData[] = $v;
            }
        }
        return $newData;
    }

}