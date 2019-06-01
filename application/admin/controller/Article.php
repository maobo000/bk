<?php
/**
 * Created by PhpStorm.
 * User: qingyun
 * Date: 19/5/29
 * Time: 下午4:31
 */

namespace app\admin\Controller;

use app\admin\model\category;
use think\Controller;

class Article extends Controller
{
    public function add()
    {
        $re = $this->request;

        if ($re->isPost()){
            $data = $re->only(['title', 'category_id', 'author', 'content', 'status', 'thumb','minthumb']);
            //验证
            $rule = [
                'title' => 'require|length:1,50',
                'category_id' => 'require|min:1',
                'author' => 'length:2,10',
                'content' => 'require|length:10,65535',
                'status' => 'in:0,1',
            ];
            $msg = [
                'title.require' => '文章标题为必填项',
                'title.length' => '文章标题长度应在1-50个字符之间',
                'category_id.require' => '请选择正确的分类信息',
                'category_id.min' => '请选择正确的分类信息',
                'author.length' => '署名长度应在2-10个字符之间',
                'content.require' => '文章内容为必填项',
                'content.length' => '文章内容过短或者过长',
                'status.in' => '文章状态有误'

            ];
            $check = $this->validate($data, $rule, $msg);
            if ($check !== true){
                $this->error($check);
            }

            $data['aid'] = session('adminLoginInfo')->id;
            //入库

            if (\app\admin\model\article::create($data)){
                $this->success('添加成功', url('admin/Article/lists'));
            }else{
                $this->error('添加失败');
            }
        }
        if ($re->isGet()){
            //获取分类信息
            $all = category::where('pid', 0)->all();
            $this->assign('all', $all);
            return $this->fetch();
        }
    }



    public function ajaxCategory()
    {
        $pid = $this->request->param('id', 0);
        $data = category::where('pid', $pid)->select();
        return json($data);
    }


    /**
     * 文章列表
     */
    public function lists()
    {

        $list = \app\admin\model\article::with('category')->order('create_time DESC')->paginate(1);
        $this->assign('list', $list);
        return $this->fetch();
    }

    public function changeStatus()
    {
        $id = $this->request->param('id');
        if (empty($id)){
            return $this->error('非法操作');
        }

        $obj = \app\admin\model\article::get($id);
        if (empty($obj)){
            return $this->error('非法操作');
        }

        $obj->status = abs($obj->status - 1);

        if ($obj->save()){
            return $this->success('成功', '', $obj->status);
        }else{
            return $this->error('失败');
        }


    }

    public function uploadImage()
    {
        $image = $this->request->file('file');
        $res = $image->validate(['size'=>1048576, 'ext'=>'jpg,png,gif,jpeg'])->move('static/upload/');

        if ($res){
            //获取文件的保存路径
            //$res->getPath();
            //获取文件的保存文件名
            //$res->getFilename();

            //含有路径信息的文件名
            $path = $res->getPathname();
            //缩略图保存路径
            $min = $res->getPath().'/min'.$res->getFilename();

            $im = \think\Image::open($path);
            //裁剪
            //$im->crop(60, 60, \think\Image::THUMB_CENTER)->save($res->getPath().'/min'.$res->getFilename());
            //生成缩略图
            $im->thumb(60, 60, \think\Image::THUMB_CENTER)->save($min);
            return json(['code'=>1, 'thumb'=> $path, 'min'=> $min]);
        }else{
            return json(['code'=>0, 'info'=>$image->getError()]);
        }
    }

    public function ueUpload()
    {
        $configData = file_get_contents("static/ui/library/ue/config.json");
        $config = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", $configData), true);

        if ($this->request->isGet()){
            return json_encode($config);
        }
        if ($this->request->isPost()){
            $image = $this->request->file('upfile');
            $allow = 'png,jpg,jpeg,gif,bmp,flv,swf,mkv,avi,rm,rmvb,mpeg,mpg,ogg,ogv,mov,wmv,mp4,webm,mp3,wav,mid,rar,zip,tar,gz,7z,bz2,cab,iso,doc,docx,xls,xlsx,ppt,pptx,pdf,txt,md';
            $res = $image->validate(['size'=>1048576, 'ext'=>$allow])->move('static/upload/');
            if ($res){
                $info =  [
                    "originalName" => $res->getFilename() ,
                    "name" => $res->getSaveName() ,
                    "url" => $res->getPathname() ,
                    "size" => $res->getSize() ,
                    "type" => $res->getExtension() ,
                    "state" => 'SUCCESS'
                ];
                return json_encode($info);
            }
        }else{
            return [
                'state' => 'ERROR'
            ];
        }

    }

}