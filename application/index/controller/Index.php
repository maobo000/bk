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

    public function add_say()
    {
        $user = session('userLoginInfo');
        if (empty($user)){
            $this->error('你需要登陆后才能进行操作',url('index/Sign/in'));
        }
        $data['content'] = $this->request->param('content');
        $data['company_id'] = $this->request->param('id');
        $plan = [
            'content'   =>'require|min:10|max:65535',
            'company_id'=>'require'
        ];
        $slip = [
            'content.require'    =>'评论内容详情为必填项',
            'content.min'        =>'评论内容最少需要10个字符',
            'content.max'        =>'评论内容最大只能有65535个字符',
            'company_id.require' =>'文章内容不全'
        ];
        if ($this->validate($data,$plan,$slip) == true){
            $company = \think\Db::table('article')->where('id',$data['company_id'])->find();

            if (empty($company)){
                $this->error('文章不存在');
            }
            $data['create_time'] = date('Y-m-d H:i:s');

            if (\think\Db::table('interview')->data($data)->insert()){
                $this->success('评论成功');
            }else{
                $this->error('评论失败');
            }
        }else{
            $this->error('评论失败');
        }

    }

    public function ding(){
        $user = session('userLoginInfo');
        if (empty($user)){
            $this->error('你需要登录才能操作');
        }
        $id = $this->request->param('id');
        if (empty($id)){
            $this->error('非法操作');
        }

        $info = \think\Db::table('interview')->where('id',$id)->find();

        if (empty($info)){
            $this->error('非法操作');

        }
        if ($info['uid'] == $user->id){
            $this->error('自己不能给自己点赞');
        }

        if (\think\Db::table('interview_ding')->where(['interview_id'=>$id,'uid'=>$user->id])->find()){
            if (\think\Db::table('interview')->where('id',$id)->setDec('ding')){
                \think\Db::table('interview_ding')->where(['interview_id'=>$id,'uid'=>$user->id])->delete();

                $this->success('取消成功',null,1);
            }else{
                $this->error('操作失败');
            }
        }else{
            if (\think\Db::table('interview')->where('id',$id)->setInc('ding')){
                \think\Db::table('interview_ding')->data(['interview_id'=>$id,'uid'=>$user->id])->insert();

                $this->success('点赞成功',null,0);
            }else{
                $this->error('操作失败');
            }
        }
    }

    public function study(){

        $res = $this->request;
        $id = $res->param('id');
        if (empty('id')){
            $this->error('您所查看的文章内容不存在');
        }
        $info = \think\Db::table('article')->where('id',$id)->find();
        if (empty($info)){
            $this->error('您所查看的企业信息不存在');
        }
        $this->assign('info',$info);
        $list = interview::with('author')->where('company_id',$id)->paginate(2);

        $user = session('userLoginInfo');
        foreach ($list as $k=>$v){
            if ($user) {
                $where = ['interview_id' => $v->id, 'uid' => $user->id];
                if (\think\Db::table('interview_ding')->where($where)->find()) {
                    $v->ding = 1;
                } else {
                    $v->ding = 0;
                }
            }
        }
        $this->assign('list',$list);
        $this->assign('page',$list);

        return $this->fetch();
    }



}
