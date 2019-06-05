<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use Log;
use think\facade\Request; //静态，直接用

class Comment extends Controller
{
    public function all($newsId, $page) //全部一级评论
    {
        $num = 5;  //一次获取最多5条评论
        $row = ($page-1) * $num;
        $arr = Db::table('comment1')
            ->where('newsId', $newsId)
            ->where('state', 1)
            ->limit($row, $num)
            ->select();
        return json_encode($arr);
    }

    public function part($newsId)  //部分评论，最多3条
    {
        $arr = Db::table('comment1')
                ->where('newsId', $newsId)
                ->where('state', 1)
                ->limit(3)
                ->select();
        return json_encode($arr);
    }

    public function second($commentId) //某个一级评论下的全部二级评论
    {
        $arr = Db::table('comment2')
                ->where('commentId', $commentId)
                ->where('state', 1)
                ->select();
        return json_encode($arr);
    }

    public function write1()  //对新闻的直接评论（一级评论）
    {
        $request = Request::instance();
        $info = $request->post();
        $newsId = $info['newsId'];
        $username = $info['username'];
        $content = $info['content'];
        //要对$content中的特殊字符转义，防止xss攻击
        $time = date('Y-m-d h:m:s');
        
        $data = array('newsId'=>$newsId, 'username'=>$username ,'content'=>$content, 'time'=>$time);
        $commentId = Db::table('comment1')->insertGetId($data);        

        return $commentId;
    }

    public function write2()  //对评论的回复（二级评论）
    {
        $request = Request::instance();
        $info = $request->post();
        $commentId = $info['commentId'];
        $username = $info['username'];
        $content = $info['content'];
        //要对$content中的特殊字符转义，防止xss攻击
        $time = date('Y-m-d h:m:s');

        $data = array('commentId'=>$commentId, 'username'=>$username ,'content'=>$content, 'time'=>$time);
        $comment2Id = Db::table('comment2')->insertGetId($data);

        return $comment2Id;
    }

    public function del1($commentId)  //删除评论
    {
        Db::table('comment1')->where('id', $commentId)->delete();
        return "ok";
    }
    public function del2($comment2Id)  //删除对某个评论的回复
    {
        Db::table('comment2')->where('id',$comment2Id)->delete();
        return "ok";
    }
}