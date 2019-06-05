<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use Log;
use think\facade\Request;

class Article extends Controller
{
    public function addRead($newsId)
    {
        //获取该新闻的阅读量
        $readNum = Db::table('newsinfo')
                ->where('newsId', $newsId)
                ->value('readNum');
    
        if($readNum == null)
        {
            $readNum = 0;
        }
        $readNum = $readNum + 1;    
        $newsTypeId = $this::getNewsTypeId($newsId);
        $data = ['newsId'=>$newsId, 'typeId'=>$newsTypeId, 'readNum'=>$readNum];
        Db::table('newsinfo')->update($data);
        //记录该用户(包括游客)阅读过这篇新闻
        $userId = $this::getUserId();
        $isExist = Db::table('watch')
                    ->where('userId', $userId)
                    ->where('newsId', $newsId)
                    ->find();
        if($isExist == null){
            $data = ['userId'=>$userId, 'newsId'=>$newsId];
            Db::table('watch')->insert($data);
        } else {
            $data = ['time'=> date('Y-m-d h:m:s')];
            Db::table('watch')
            ->where('userId', $userId)
            ->where('newsId', $newsId)
            ->update($data);            
        }
    }
    
    public function addCollect($newsId)
    {
        $result = $this::collect($newsId);
        if($result != 0) return "error";        
            //新闻收藏量
            $collectNum = Db::table('newsinfo')
                    ->where('newsId', $newsId)
                    ->value('collectNum');
            if($collectNum === null){
                $newsTypeId = $this::getNewsTypeId($newsId);
                $data = ['newsId'=>$newsId, 'typeId'=>$newsTypeId, 'collectNum'=>1];
                Db::table('newsinfo')->insert($data);
            } else { 
                //收藏量加1
                Db::table('newsinfo')->where('newsId', $newsId)->update(['collectNum'=> $collectNum+1]);
            }
        
            //增加该用户对该类型文章的好感度
            //操作user_news表
            $this::add_val_user_to_news($newsId);
        
        return "ok";
    }

    public function addGood($newsId)
    {
        $result = $this::good($newsId);
        if($result != 0) return "error";        
            //新闻点赞量
            $likeNum = Db::table('newsinfo')
                ->where('newsId', $newsId)
                ->value('likeNum');
            if($likeNum === null){
                $newsTypeId = $this::getNewsTypeId($newsId);
                $data = ['newsId'=>$newsId, 'typeId'=>$newsTypeId, 'likeNum'=>1];
                Db::table('newsinfo')->insert($data);
            } else { 
                //点赞量加1
                Db::table('newsinfo')->where('newsId', $newsId)->update(['likeNum'=> $likeNum+1]);
            }
        
            //增加该用户对该类型文章的好感度
            //操作user_news表
            $this::add_val_user_to_news($newsId);
        
        return "ok";
    }

    public function guanzhu()  //对某个新闻的来源添加关注
    {
        $request = Request::instance();
        $info = $request->post();
        $source = $info['source'];
        $userId = $this::getUserId();

        Log::write($userId);

        if($userId <= 0){
            return "error";
        }

        $isGuanzhu = Db::table('guanzhu')
                ->where('userId', $userId)
                ->where('source', $source)
                ->find();

        if($isGuanzhu == null){
            $data = ['userId'=>$userId, 'source'=>$source];
            Db::table('guanzhu')->insert($data);
        }
    }

    public function getNewsTypeId($newsId)
    {
        $typeId = Db::table('newsinfo')
                    ->where('newsId', $newsId)
                    ->value('typeId');
        if($typeId == null)
        {
            $typeId = 1; //测试，默认返回 类型1
            $data = ['newsId'=>$newsId, 'typeId'=>$typeId];
            Db::table('newsinfo')->insert($data);
        }
        return $typeId;  
    }

    public function maxCollect($pageNum, $pageSize)
    {
        $str = strval($pageNum). ',' . strval($pageSize);
        //收藏量最大的新闻列表
        $newsList = Db::table('news')
                        ->join('newsinfo','news.id=newsinfo.newsId')
                        ->order('newsinfo.collectNum desc, news.date desc')
                        ->page($str)
                        ->field('id, topic, date, collectNum')
                        ->select();
        return json_encode($newsList);
    }

    public function maxRead($pageNum, $pageSize)
    {
        $str = strval($pageNum). ',' . strval($pageSize);
        //阅读量最大的新闻列表
        $newsList = Db::table('news')
                ->join('newsinfo','news.id=newsinfo.newsId')                
                ->order('readNum desc, date desc')
                ->page($str)
                ->field('id, topic, date, readNum')               
                ->select();
        return json_encode($newsList);
    }

    public function getUserId()
    {
        $userinfo = session('userinfo');
        $userId = $userinfo['userId'];
        return $userId;
    }

    public function collect($newsId)
    {
        $userId = $this::getUserId();
        if($userId <= 0)
        {
            return -1;
        }       
        $data = ['userId'=>$userId, 'newsId'=>$newsId];
        $isExist = Db::table('collect')
                        ->where('userId', $userId)
                        ->where('newsId', $newsId)
                        ->find();
        if($isExist != null)
        {
            return -1;
        }
        Db::table('collect')->insert($data);
        return 0;
    }

    public function good($newsId)
    {
        $userId = $this::getUserId();
        if($userId <= 0)
        {
            return -1;
        }       
        $data = ['userId'=>$userId, 'newsId'=>$newsId];
        $isExist = Db::table('good')
                        ->where('userId', $userId)
                        ->where('newsId', $newsId)
                        ->find();
        if($isExist != null)
        {
            return -1;
        }
        Db::table('good')->insert($data);
        return 0;
    }

    public function add_val_user_to_news($newsId)  //增加用户对某种新闻类型的兴趣量
    {
        $newsTypeId = $this::getNewsTypeId($newsId);
        $userId = $this::getUserId();
        //兴趣量
        $val = Db::table('user_news')
                ->where('userId', $userId)
                ->where('typeId', $newsTypeId)
                ->value('val');
        if($val == null){
            $data = ['userId'=>$userId, 'typeId'=>$newsTypeId, 'val'=>1];
            Db::table('user_news')->insert($data);
        } else { 
            //兴趣量加1
            Db::table('user_news')
                ->where('userId', $userId)
                ->where('typeId', $newsTypeId)
                ->update(['val'=> $val+1]);
        }
    }

    public function recommand($newsId)  //看过当前新闻的用户，所看过的其他最新新闻
    {
        $userIds = Db::table('watch')
                    ->where('newsId', $newsId)
                    ->column('userId');
        $newsIds = Db::table('watch')
                    ->whereIn('userId', $userIds)
                    ->column('newsId');
        $newsArr = Db::table('news')
                    ->whereIn('id', $newsIds)
                    ->order('date desc')
                    ->limit(6)
                    ->field('id, topic')
                    ->select();
        for($i = 0; $i < count($newsArr); $i++)
        {
            if($newsArr[$i]['id'] == $newsId) unset($newsArr[$i]);
        }
        return json_encode($newsArr);
    }
}