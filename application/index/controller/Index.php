<?php
namespace app\index\controller;

use \think\Db;
use Log;

class Index
{
    public function index()   //首页
    {
        return view("index");
    }

    public function detail($newsId)  //正文
    {        
        return view('detail');
    }

    public function newsData($id)  //获取正文和推荐
    {
        //新闻正文内容
        $obj0 = Db::table('news')
                ->where('id', $id)
                ->select();
        $obj = $obj0[0];
        
        //新闻正文收藏量和点赞量
        $obj0 = Db::table('newsinfo')
                ->where('newsId', $id)
                ->select();
        $obj2 = $obj0[0];

        $collectNum = 0;
        $likeNum = 0;
        if(count($obj0) > 0){
            $obj2 = $obj0[0];
            $collectNum = $obj2['collectNum'];
            $likeNum = $obj2['likeNum'];
        }

        $userId = $this::getUserId();
        //是否已收藏
        $tmp = Db::table('collect')
                ->where('userId', $userId)
                ->where('newsId', $id)
                ->find();
        $isCollect = (!$tmp ? false : true);
        //是否已点赞
        $isGood = Db::table('good')
                ->where('userId', $userId)
                ->where('newsId', $id)
                ->find();
        $isGood = (!$tmp ? false : true);
        //是否已关注该新闻来源
        $tmp = Db::table('guanzhu')
                ->where('userId', $userId)
                ->where('source', $obj['source'])
                ->find();
        $isGuanzhu = (!$tmp ? false : true);

        $arr = array('topic'   => $obj['topic'],
                    'date'     => $obj['date'],
                    'source'   => $obj['source'], 
                    'url'      => $obj['url'],
                    'content'  => $obj['content'],
                    'collect'  => $collectNum,
                    'good'     => $likeNum
                );
        //推荐新闻
        $arrNews = $this::news2news($id);
        $news = array('arr'=>$arr, 'arrNews'=>$arrNews, 'isCollect'=>$isCollect, 'isGood'=>$isGood, 'isGuanzhu'=>$isGuanzhu);
        header('Content-Type:application/json');
        return json_encode($news);
    }

    //获取推荐给用户的第$num个新闻的具体信息
    //$sign是选项卡的值,1是推荐,2是最新,3是关注
    public function data($sign, $num)
    {
        $newsId = $this::getNewsId($num, $sign);  //获取推荐的第num个新闻的newsId
        $arr = array();
        if($newsId != -1)
        {
            $obj = Db::table('news')
                    ->where('id', $newsId)
                    ->find();

            //返回二维数组，第1个下标是$newsId，第2个下标是key
            $arr = array(
                'newsId'=> $newsId,
                'topic' => $obj['topic'], 
                'date'  => $obj['date'], 
                'source'=> $obj['source'],
                'content'=> (mb_substr($obj['content'], 0, 120, 'utf-8').'......'),
            );
        }
        header('Content-Type:application/json');
        return json_encode($arr);
    }

    private function getUserId()
    {
        $userinfo = session('userinfo');
        $userId = $userinfo['userId'];
        if($userId == null) $userId = 0;
        return $userId;
    }

    public function getNewsId($num, $sign)
    {
        $userId = $this::getUserId();
        $idArr = $this::user2news($userId, $sign);
        if($num >= count($idArr))
        {
            return -1; //没有更多可以加载的新闻了
        }
        return $idArr[$num-1];
    }

    public function news2news($newsId)  //获取若干条跟newsId最相关的新闻的标题，按照时间距离现在近的降序排列
    {
        $newsIdArr = Db::table('news2news')
                    ->where('newsId',$newsId)
                    ->order('relation desc')
                    ->column('relateNewsId');

        // $obj = Db::table('news')
        //         ->whereIn('id', $newsIdArr)
        //         ->order('date desc')
        //         ->column('*');

        // $arr = array();
        // $len = count($newsIdArr);
        // for($i = 0; $i < $len; $i++)
        // {
        //     $news = $obj[$newsIdArr[$i]];
        //     $arr[$i] = array(
        //             'id'=>$news['id'], 
        //             'topic'=> $news['topic'], 
        //             'date' => $news['date']
        //         );
        // }

        $arr = Db::table('news')
                ->whereIn('id', $newsIdArr)
                ->order('date desc')
                ->field('id, topic, date')
                ->select();

        return $arr; 
    }

    public function user2news($userId, $sign)  //获取推荐给用户的新闻的id数组，userId为0表示游客
    {
        $obj = array();
        if($userId == 0 || $sign == 2)  //游客 或 最新 推荐最新的新闻
        {
            $obj = Db::table('news')
                    ->order('date desc')
                    ->limit(100)
                    ->column('id');
        }
        else 
        if($sign == 1) {
            $typeIdArr = Db::table('user_news') //获取喜欢的新闻类型
                    ->where('userId', $userId)
                    ->order('val desc')
                    ->column('typeId');
            foreach($typeIdArr as $typeId)
            {
                $newsIdArr = Db::table('newsinfo')
                            ->where('typeId', $typeId)
                            ->column('newsId');
                $obj = array_merge($obj, $newsIdArr);
            }
        } else {
            //找出用户关注了什么来源
            $sourceArr = Db::table('guanzhu')
                    ->where('userId', $userId)
                    ->column('source');
            //找出自这些来源的新闻id
            $newsIdArr = Db::table("news")
                    ->whereIn('source', $sourceArr)
                    ->column('id');
            $obj = $newsIdArr;
        }
        return $obj;
    }

    public function getNewsTypeId($newsId)
    {
        $typeId = Db::table('newsinfo')
                    ->where('newsId', $newsId)
                    ->value('typeId');
        if($typeId == null)
        {
            //////////////////////////
            $typeId = 1; //测试，默认返回 类型1
            //////////////////////////
            $data = ['newsId'=>$newsId, 'typeId'=>$typeId];
            Db::table('newsinfo')->insert($data);
        }
        return $typeId;  
    }

    public function userinfo()
    {
        $userId = $this::getUserId();
        $readNum = Db::table('watch')->where('userId', $userId)->count();
        $guanzhuNum = Db::table('guanzhu')->where('userId', $userId)->count();
        $level = 1;
        $followNum = 0;
        $arr = array('level'=> $level, 'followNum'=> $followNum, 'guanzhuNum'=> $guanzhuNum, 'readNum'=> $readNum);
        return json_encode($arr);
    }
}