<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use Log;

class Stat extends Controller
{
    public function data()   //获取统计页
    {
        return view('stat');
    }

    public function getUserId()  //获取用户Id
    {
        $userinfo = session('userinfo');
        $userId = $userinfo['userId'];
        if($userId == null) $userId = 1;   //////////////////方便测试而已，应该赋值为 0
        return $userId;
    }

    public function registerDay()   //注册天数
    {
        $now = time();
        $userId = $this::getUserId();
        $registerTime = Db::table('user')
                        ->where('userId', $userId)
                        ->value('create_at');

        $x = $now - strtotime($registerTime);
        $year = intval($x / 3600 / 24/ 365);
        $x = $x - $year * 3600 * 24 * 365;
        $month = intval($x / 3600 / 24 / 30);
        $x = $x - $month * 3600 * 24 * 30; 
        $day = intval($x / 3600 / 24);
        
        $arr = array('year'=>$year, 'month'=>$month, 'day'=>$day);
        return $arr;
    }
    public function getNewsType()   //用户经常阅读的新闻类型数组
    {
        $userId = $this::getUserId();
        $typeId = Db::table('user_news')
                ->where('userId', $userId)
                ->column('typeId');
        $typeName = Db::table('newstype')
                    ->whereIn('typeId', $typeId)
                    ->column('typeName');
        $newsTypeArr = $typeName;
        return $newsTypeArr;
    }
    public function getKeyword()   //用户经常阅读的新闻的关键词数组
    {
        $userId = $this::getUserId();
        $typeIdArr = Db::table('user_news')
                ->where('userId', $userId)
                ->column('typeId');
        $newsIdArr = Db::table('newsinfo')
                    ->whereIn('typeId', $typeIdArr)
                    ->order('readNum desc')
                    ->limit(10)     //限定取阅读量最大的10篇新闻
                    ->column('newsId');

        $keywordStr = Db::table('keyword')
                    ->whereIn('newsId', $newsIdArr)
                    ->column('word');
        
        $keywordArr = array();
        foreach($keywordStr as $str)
        {
            $tmp = explode('#', $str);
            $keywordArr = array_merge($keywordArr, $tmp);
            if(count($keywordArr) > 43)  //关键词限定为43个
            {
                break;
            }
        }
        return $keywordArr;        
    }

    public function get_read($daytime) { //获取某天的阅读量
        $userId = $this::getUserId();
        $nextdaytime = $daytime + 24 * 3600;
        $daystr = date('Y-m-d hh:mm:ss', $daytime);
        $nextdaystr = date('Y-m-d hh:mm:ss', $nextdaytime);
        $data = Db::table('watch')
                ->where('userId', $userId)
                ->where('time', '>=', $daystr)
                ->where('time', '<', $nextdaystr)
                ->count('id');
        return $data;
    }

    public function get_week_read() { //获取过去一周每天的阅读量
        $day = time();
        $data = array();
        for($i = 0; $i <= 6; $i++) {
            $read = $this::get_read($day);
            $date = date('Y-m-d', $day);
            $data[$date] = $read;
            $day = $day - 24 * 3600;
        }
        return $data;
    }
    public function basicData()
    {
        $registerTime = $this::registerDay();
        $newsType = $this::getNewsType();
        $keyword = $this::getKeyword();
        $weekly = $this::get_week_read();

        Log::write($weekly);

        $arr = array('registerTime'=> $registerTime, 'newsType'=>$newsType, 'keyword'=>$keyword, 'weekly'=>$weekly);
        return json_encode($arr);
    }
}