<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use Log;
use think\facade\Request;
use app\admin\controller\BaseController;

class Admin extends BaseController
{
    public function login()
    {
        return view();
    }
    // 验证登录
    public function login_in()
    {
        $request = Request::instance();
        $info = $request->post();
        // 接收用户名和密码，并将各个数据两边空格去掉
        $username = trim($info['username']);
        $password = trim($info['password']);
        // 判断用户名是否存在
        $data = Db::name('admin')
                ->where('username', $username)
                ->select();
        if (!$data) 
        {
          $arr = array('error'=>'username is not exist');
        }
        // 判断密码是否正确
        if ($data[0]['password'] == $password) 
        {
            // 把信息存入session，记录登录状态
            session('admininfo', $data[0]);
            $role = $data[0]['role'];
            $arr = array('username'=> $username, 'role'=> $role);
        } 
        else 
        {
            $arr = array('error'=>'login error');
        }
        return json_encode($arr);
    }

    // 退出操作，重定向到登录页
    public function login_out()
    {
        session('admininfo', null);
        $this->redirect('/admin/login');
    }

    //获取管理员页
    public function admin()
    {
        return view('adminList');
    }

    //获取管理员角色列表，只能获取自己及以下的管理员角色
    public function roleList($roleId)
    {
        $roles = Db::table('role')
                    ->where('roleId', '>=', $roleId)
                    ->select();
        return json_encode($roles);
    }
    //获取 管理员列表
    public function adminList()
    {
        $admins = Db::table('admin')
                ->alias('a')
                ->join('role r','a.role = r.roleId')
                ->field('a.adminId, a.username, r.roleName')
                ->select();
        return json_encode($admins);
    }    
    //新增管理员
    public function add()
    {
        $request = Request::instance();
        $info = $request->post();
        $username = trim($info['username']);
        $password = trim($info['password']);
        $role = trim($info['role']);
        // 判断用户名是否存在
        $data = Db::table('admin')
                ->where('username', $username)
                ->find();
        if($data != null)
        {
            return "no";
        }
        $data = array('username'=> $username, 'password'=>$password, 'role'=>$role);
        $adminId = Db::table('admin')
                    ->insertGetId($data);
        $roleId = Db::table('admin')
                    ->where('adminId', $adminId)
                    ->value('role');
        //把所有权限内容写进roleaccess表中，默认全部不拥有
        $data = array();
        $cnt = Db::table('access')->count();
        for($i = 1; $i < $cnt; $i++)
        {
            $data[$i] = array('role'=>$roleId, 'accessId'=>$i);
        }
        Db::table('roleAccess')->insert($data);
        return "ok";
    }
    //删除管理员
    public function delete($adminId)
    {
        Db::table('admin')->delete($adminId);
        return "ok";
    }

    //权限页面
    public function access()
    {
        return view('access');
    }

    //获取比自己低级的管理员角色的权限列表
    public function accessList($roleId)
    {
        $roles = Db::table('role')
                ->where('roleId', '>', $roleId)
                ->select(); 
        $ops = array();
        foreach($roles as $role)
        {
            $roleId = $role['roleId'];
            $op = Db::table('roleAccess')
                    ->alias('r')
                    ->join('access a', 'r.accessId = a.id')
                    ->where('r.roleId', $roleId)
                    ->field('a.id, r.has, a.content')
                    ->select();
            $ops[$roleId] = $op;
        }

        $arr = array('ops'=>$ops, 'roles'=>$roles);
        return json_encode($arr);
    }

    //保存角色拥有的权限
    public function saveAccess()
    {
        $request = Request::instance();
        $info = $request->post();
        $roleId = $info['roleId'];
        $ops = $info['ops'];
        foreach($ops as $x)
        {
            $data = array('has' => $x['has']);
            Db::table('roleAccess')
                ->where('roleId', $roleId)
                ->where('accessId', $x['id'])
                ->update($data);
        }
        return "ok";
    }

    //新增管理权限内容，默认选择
    public function addAccess($content, $ctrl, $act, $role)
    {
        $op = strtolower($ctrl.'/'.$act);
        $data = array('role'=> $role, 'has'=> 1, 'keyword'=> $op, 'content'=>$content);
        $arr = Db::table('access')->insert($data);
        return "ok";
    }

    //改变管理员角色
    public function changeRole($adminId, $roleId)
    {
        $data = array('role' => $roleId);
        Db::table('admin')->where('adminId', $adminId)->update($data);
        return "ok";
    }

    //无权限页面
    public function noAccess()
    {
        return view('noAccess');
    }
}
