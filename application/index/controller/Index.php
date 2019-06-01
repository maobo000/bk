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

        $id = $this->request->param('id');

        $this->categoryList(21);

        $info = article::where('category_id',$id)->find();
        $company =Interview::select();
        $this->assign('company',$company);
        $this->assign('info',$info);

        $this->assign('id',$id);
        return $this->fetch();
    }

    public function add_say(){
        $user = session('userLoginInfo');
        if (empty($user)){
            $this->error('您需要登陆后才能操作',url('index/Sign/in'));
        }
        $data['content'] = $this->request->param('content');
        $data['company_id'] = $this->request->param('company_id');

        $plan = [
            'content'   =>'require|min:10|max:65535',
            'company_id'=>'require'
        ];

        $slip = [
            'content.require'    =>'面试经历详情为必填项',
            'content.min'        =>'面试经历最少需要10个字符',
            'content.max'        =>'面试经历最大只能有65535个字符',
            'company_id.require' =>'企业信息不全'
        ];

        if ($this->validate($data,$plan,$slip) == true){
            $company = \think\Db::table('company')->where('id',$data['company_id'])->find();

            if (empty($company)){
                $this->error('文章内容不存在');
                $data['uid'] = $user->id;
                $data['create_time'] = data('Y-m-d H:i:s');
                if (\think\Db::table('interview')->data($data)->insert()){
                    $this->success('添加成功');
                }else{
                    $this->error('添加失败');
                }
            }else{
                $this->error('添加失败');
            }
        }
    }


    public function pl(){
        return $this->fetch();
    }



}
