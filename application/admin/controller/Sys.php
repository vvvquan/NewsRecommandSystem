<?php
namespace app\admin\controller;

use \think\Controller;
use \think\Db;
use think\facade\Request;
use Log;

class Sys extends Controller
{
    public function sysNotice()  //系统页
    {
        return view('news/sys');
    }

    public function userNotice()
    {
        return "ok";
    }

    public function notice()
    {
        $content = Db::table('notice')->where('id', 1)->value('content');
        if($content == null){
            $content = '';
        }
        $create_at = Db::table('notice')->where('id', 1)->value('create_at');
        if($create_at == null){
            $create_at = '';
        }
        $arr = array('create_at' => $create_at, 'content'=> $content);
        return json_encode($arr);
    }
    public function saveNotice()
    {
        $request = Request::instance();
        $info = $request->post();
        $content = $info['content'];
        $data = array('content'=>$content, 'create_at'=> date("Y-m-d",time()));    
        Db::table('notice')->where('id', 1)->update($data);
        return "ok";
    }

}