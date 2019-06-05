<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use Log;

class Me extends Controller
{
    public function history()
    {
        return view();
    }
    public function cnt($userId)
    {
        $cnt = Db::table('watch')
                ->where('userId', $userId)
                ->count();      
        return $cnt;
    }
    public function historyData($userId, $pageNum, $pageListNum)
    {
        $start = ($pageNum-1) * $pageListNum;
        $limit = $start . ',' . $pageListNum;
        $record = Db::table('watch')
                ->alias('w')
                ->join('news n', 'n.id = w.id')
                ->where('userId', $userId)
                ->order('time desc')
                ->limit($limit)
                ->field('n.id, n.topic, w.time')
                ->select();

        $cnt = $this::cnt($userId);
        $pageTotal = ceil($cnt / $pageListNum);

        $arr = array('pageTotal'=>$pageTotal, 'record'=>$record);
        return json_encode($arr);
    }


}