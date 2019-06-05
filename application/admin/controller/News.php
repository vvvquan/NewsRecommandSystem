<?php
namespace app\admin\controller;

use think\Db;
use think\facade\Request;
use Log;

class News extends \think\Controller
{
    //编辑类型
    public function edit()
    {
        return view('edit');
    }
    public function getAllNewsType()
    {
        $newsType = Db::table('newsType')->select();
        $arr = array('types' => $newsType);
        return json_encode($arr);
    }
    public function addNewsType()  //添加新闻类型
    {
        $request = Request::instance();
        $info = $request->post();
        $newsType = $info['newsType'];
        $data = array('typeName'=>$newsType);
        Db::table('newsType')->insert($data);
        return "ok";
    }
    
    //新闻列表
    public function newsList()
    {
        return view('newsList');
    }
    public function getNewsTotal()
    {
        $cnt = Db::table('news')->count();
        $arr = array('newsTotal' => $cnt);
        return json_encode($arr);
    }
    public function getNewsTitle($pageNum, $pageListNum)
    {
        $start = ($pageNum-1) * $pageListNum;
        $limit = $start . ','. $pageListNum;
        $arr = Db::table('news')
                 ->alias('a')
                 ->join('newsinfo b', 'b.newsId = a.id', 'LEFT')
                 ->order('a.id desc')
                 ->field('a.id, a.topic, b.typeId')
                ->limit($limit)
                ->select();

        return json_encode($arr);
    }
    public function saveNewsType()  //为新闻选择的新闻类型
    {
        $request = Request::instance();
        $info = $request->post();
        $typeId = $info['typeId'];
        $newsId = $info['newsId'];
        $isExist = Db::table('newsinfo')
                ->where('newsId', $newsId)
                ->value('typeId');
        if($isExist != null){
            $data = array('typeId'=>$typeId);
            Db::table('newsinfo')
                ->where('newsId', $newsId)
                ->update($data);
        } else {
            $data = array('newsId'=> $newsId,'typeId'=>$typeId);
            Db::table('newsinfo')
                ->where('newsId', $newsId)
                ->insert($data);            
        }
        return "ok";
    }

    //评论审核
    public function comment()
    {
        return view('comment');
    }
    public function chkComment1() //对某些评论进行审核
    {
        $request = Request::instance();
        $info = $request->post();
        $id = $info['id'];
        $state = $info['state'];  //0 待审核  1 通过  2 不通过
        $data = array('state' => $state);
        Db::table('comment1')->where('id', $id)->update($data);
        return "ok";
    }
    public function chkComment2() //对某些回复进行审核
    {
        $request = Request::instance();
        $info = $request->post();
        $id = $info['id'];
        $state = $info['state'];  //0 待审核  1 通过  2 不通过
        $data = array('state' => $state);
        Db::table('comment2')->where('id', $id)->update($data);
        return "ok";
    }

    public function cnt($pageListNum)
    {
        $cnt1 = Db::table('comment1')
                ->where('state', 0)
                ->count(); 
        $cnt2 = Db::table('comment2')
                ->where('state', 0)
                ->count(); 
        $cnt = ceil(($cnt1 + $cnt2) / $pageListNum);
        return $cnt;  
    }

    public function getComment1()
    {
        $arr = Db::table('comment1')
                ->where('state', 0)
                ->order('time desc')
                ->field('id, content')
                ->select();

        return $arr;
    }
    public function getComment2()
    {
        $arr = Db::table('comment2')
                ->where('state', 0)
                ->order('time desc')
                ->field('id, content')
                ->select();

        return $arr;
    }

    public function commentList($pageNum, $pageListNum)
    {
        $start = ($pageNum-1) * $pageListNum + 1;
        $arr1 = $this::getComment1();
        $arr2 = $this::getComment2();

        $len1 = count($arr1);
        $len2 = count($arr2);
        $comments = array();
        $j = 0;
        for($i = 0; $i < $len1; $i++)
        {
            $comments[$j] = array('id'=> $arr1[$i]['id'], 'content'=> $arr1[$i]['content'], 'tb'=> 1, 'state'=> 0);
            $j++;
        }
        for($i = 0; $i < $len2; $i++)
        {
            $comments[$j] = array('id'=> $arr2[$i]['id'], 'content'=> $arr2[$i]['content'], 'tb'=> 2, 'state'=> 0);
            $j++;
        }

        $pageTotal = $this::cnt($pageListNum);
        $arr = array('comments'=> $comments, 'pageTotal'=> $pageTotal);
        return json_encode($arr);
    }

}