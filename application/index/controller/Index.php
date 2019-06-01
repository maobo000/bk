<?php
namespace app\index\controller;



use app\admin\model\article;
use app\admin\model\category;
use app\index\model\Interview;
use think\Controller;
class Index extends Controller
{
    public function index(){
        return $this->fetch();
    }

    public function categoryList($id){
        $category = category::where('pid',$id)->select();
        $this->assign('category',$category);
        return $category;
    }

    public function about(){

        $id = $this->request->param('id');
        $this->categoryList(17);

        $info = article::where('category_id',$id)->find();


        $this->assign('info',$info);

        $this->assign('id',$id);

        return $this->fetch();
    }

    public function growup(){

    $info = article::where('category_id',24)->select();

    $this->assign('id',$info);

        return $this->fetch();
    }

    public function add_say(){
        $user = session('userLoginInfo');
        if (empty($user)){
            $this->error('您需要登陆后才能操作',url('index/Sign/in'));
        }
        $data['content'] = $this->request->param('content');
        $data['category_id'] = $this->request->param('category_id');

        $plan = [
            'content'   =>'require|min:1|max:65535',
            'company_id'=>'require'
        ];

        $slip = [
            'content.require'    =>'评论不能为空',
            'content.min'        =>'评论最少需要1个字',
            'content.max'        =>'评论最大只能有65535个字符',
            'company_id.require' =>'评论内容不全'
        ];

        if ($this->validate($data,$plan,$slip) == true){
            $article = \think\Db::table('article')->where('id',$data['category_id'])->find();

            if (empty($article)) {
                $this->error('文章内容不存在1');
            }
            $data['uid'] = $user->id;
            $data['create_time'] = data('Y-m-d H:i:s');
            if (\think\Db::table('interview')->data($data)->insert()){
                $this->success('评论成功');
            }else{
                $this->error('评论失败');
                }
        }else{
                $this->error('评论失败');
            }
    }




    public function study(){
//        $Info = category::where('pid',21)->select();
//        $this->assign('Info',$Info);


        $list =  article::where('category_id',24)->select();
        $this->assign('list',$list);

        return $this->fetch();
    }



}
