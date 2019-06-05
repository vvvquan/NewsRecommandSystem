<?php
namespace app\admin\controller;
use think\Controller;
use \think\Db;
use \think\facade\Request;
use Log;

class BaseController extends Controller
{
    public function initialize()
    {
        $request = Request::instance();
        $ctrl = $request->controller();
        $act = $request->action();
        $op = strtolower($ctrl.'/'.$act);
        //不要权限的操作(小写)
        $white = array(
            'admin/login','admin/login_in', //登录
            'admin/login_out',  //退出
            'admin/noaccess'    //无权限
        );
        //该操作是否不用权限
        if(in_array($op, $white)){
            return;
        }
        //检查是否已经登录
    	if(empty(session('admininfo'))){
        //    $this->redirect('/admin/login');
        }
        $admininfo = session('admininfo');
        $adminId = $admininfo['adminId'];
        //检查权限
        if(!$this::rbac($op, $adminId)){
            $this->redirect('/admin/noAccess');
        }
    }

    public function rbac($op, $adminId)
    {
        //获取用户的角色
        $role = $this::getRole($adminId);
        //检查该角色是否有这个操作的权限
        return $this::hasRight($role, $op);
    }

    public function getRole($adminId)
    {
        $roleId = Db::table('admin')->where('adminId', $adminId)->limit(1)->value('role');
        return $roleId;
    }

    public function hasRight($role, $op)
    {
        //获取该角色的所有权限操作
        $accessIds = Db::table('roleAccess')
                    ->where('roleId', $role)
                    ->where('has', 1)
                    ->column('accessId');
        
        $arr = Db::table('access')
                ->whereIn('id', $accessIds)
                ->field('ctrl, act')
                ->select();

        $ops = array();
        $i = 0;
        $len = count($arr);
        for($i = 0; $i < $len; $i++)
        {
            $ops[$i] = $arr[$i]['ctrl'] . '/' . $arr[$i]['act'];
        }

        return in_array($op, $ops);
    }
}