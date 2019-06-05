<?php
namespace app\admin\controller;

use think\Db;
use think\facade\Request;
use Log;

class User extends \think\Controller
{
    public function userList()
    {
        return view('userList');
    }

    public function resetUserPass($userId)
    {
        $password = "123456";
        $salt = Db::table('user')->where('userId', $userId)->value('salt');
        $crypt = $this::modifyPassword($password, $salt);
        Db::table('user')->where('userId', $userId)->update(['password' => $crypt]);
    }

    public function resetAdminPass($adminId)
    {
        $password = "123456";
        $salt = Db::table('user')->where('userId', $userId)->value('salt');
        $crypt = $this::modifyPassword($password, $salt);
        Db::table('admin')->where('adminId', $adminId)->update(['password' => $crypt]);
    }

    public function modifyPassword($password, $salt)
    {
        $crypt = md5(md5($password) . $salt);
        return $crypt;
    }

    //搜索所有用户
    public function searchAll()
    {
        $users = Db::table('user')
                    ->field('userId, username, create_at')
                    ->select();
        return json_encode($users);
    }    

    //搜索特定用户
    public function search()
    {
        $request = Request::instance();
        $info = $request->post();
        $content = $info['content'];
        $users = Db::table('user')
                ->where('userId', 'LIKE', '%'.$content.'%')
                ->whereOr('username', 'LIKE', '%'.$content.'%')
                ->field('userId, username, create_at')
                ->select();        

        $i = 0;
        foreach($users as $user)
        {
            $users[$i]['lock'] = $this::isLock($users[$i]['userId']);
            $i++;
        }
        return json_encode($users);
    }

    //用户帐号是否被锁
    public function isLock($userId)
    {
        $result = Db::table('blackList')->where('userId', $userId)->find();
        return $result == null;
    }
    //封号
    public function lock($userId)
    {
        $data = array('userId' => $userId);
        Db::table('blackList')->insert($data);
        return "ok";
    }
    //解锁帐号
    public function unlock($userId)
    {
        Db::table('blackList')->where('userId', $userId)->delete();
        return "ok";
    }
    
    //权限页面
    public function access()
    {
        return view('access');
    }
    //权限列表
    public function accessList()
    {
        $arr = Db::table('userAccess')
                    ->field('id, has, content')
                    ->select();
        return json_encode($arr);
    }

    //保存用户的权限
    public function saveAccess()
    {
        $request = Request::instance();
        $info = $request->post();
        $access = $info['access'];
        foreach($access as $x)
        {
            $data = array('has'=>$x['has']);
            Db::table('userAccess')
                ->where('id', $x['id'])
                ->update($data);
        }
        return "ok";
    }

    //新增普通用户权限内容，默认选择
    public function addAccess($content)
    {
        $data = array('role'=> 0, 'has'=> 0, 'content'=>$content);
        $arr = Db::table('access')->insert($data);
        return "ok";
    }
}