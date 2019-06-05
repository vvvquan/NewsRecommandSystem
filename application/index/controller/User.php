<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use Log;
use think\facade\Request; //静态，直接用
//use app\index\BaseController;

class User extends Controller //extends BaseController
{
    //登录页面
    public function login(){
        return view('loginPage');
    }
    //注册页面
    public function reg(){
        return view('reg');
    }
    //用户个人管理页面
    public function info(){
        return view('info');
    }
    public function safe(){
        return view('safe');
    }
    public function publish(){
        return view('publish');
    }
    public function bind(){
        return view('bind');
    }
    //获取用户Id
    private function getUserId()
    {
        $userinfo = session('userinfo');
        $userId = $userinfo['userId'];
        if($userId == null) $userId = 0;
        return $userId;
    }
    // 接收注册提交的信息
    public function register()
    {
        $request = Request::instance();
        $info = $request->post();
        //接收用户名和密码，并将各个数据两边空格去掉
        $username = trim($info['username']);
        $password = trim($info['password']);
        // 判断用户名不为空，密码长度是否不小于6
        if (strlen($username) == 0 || strlen($password) < 6) {
            $result = array('msg'=>'name or pwd is invalid');
            return json_encode($result);
        }
        // 判断用户名是否已经被注册
        $username_data = Db::name('user')->where('username',$username)->select();
        if ($username_data) {
            $result = array('msg'=>'name is exist');
            return json_encode($result);
        }
        $salt = ""; 
        for ($i = 0; $i < 5; $i++) 
        { 
            $salt .= chr(mt_rand(33, 126)); 
        } 
        $data = [
        	'username' => $username,
            'password' => md5($password . $salt),
            'salt'     => $salt
        ];
        $status = Db::table('user')->insert($data);
        if ($status == 1) {
            $userId = Db::table('user')->where('username', $username)->value('userId');
            $result = array('msg'=>'success', 'userId'=>$userId, 'username'=>$username);
            session('userinfo', $result);
            return json_encode($result);
        }else{
            $result = array('msg'=>'fail');
            return json_encode($result);
        }
    }

    // 普通验证登录
    public function login_in()
    {
        $request = Request::instance();
        $info = $request->post();
        // 接收用户名和密码，并将各个数据两边空格去掉
        $username = $info['username'];
        $password = $info['password'];
        // 判断用户名是否存在
        $data = Db::table('user')->where('username', $username)->select();
        if($data == null){
            $result = array('msg'=>'username is not exist');
            return json_encode($result);
        }
        $salt = $data[0]['salt'];
        $userId = $data[0]['userId'];
        // 判断密码是否正确
        if ( $data[0]['password'] == md5($password.$salt) ) {
            //检查是否被封号
            if($this::islock($userId)){
                $result = array('msg'=>'user is lock');
                return json_encode($result);
            }
            session('userinfo', $data[0]);
            $result = array('userId'=>$userId, 'username'=>$username);
        }else{
            $result = array('msg'=>'login fail');
        }

        return json_encode($result);
    }

    // 短信验证登录
    public function login_in_phone()
    {
        $request = Request::instance();
        $info = $request->post();
        // 接收用户名和密码，并将各个数据两边空格去掉
        $username = $info['username'];
        $password = $info['password'];
        // 判断用户名是否存在
        $data = Db::table('user')->where('username', $username)->select();
        if($data == null){
            $result = array('msg'=>'username is not exist');
            return json_encode($result);
        }
        $salt = $data[0]['salt'];
        $userId = $data[0]['userId'];
        // 判断密码是否正确
        if ( $data[0]['password'] == md5($password.$salt) ) {
            //检查是否被封号
            if($this::islock($userId)){
                $result = array('msg'=>'user is lock');
                return json_encode($result);
            }
            session('userinfo', $data[0]);
            $result = array('userId'=>$userId, 'username'=>$username);
        }else{
            $result = array('msg'=>'login fail');
        }

        return json_encode($result);
    }

    // 邮箱验证登录
    public function login_in_email()
    {
        $request = Request::instance();
        $info = $request->post();
        // 接收用户名和密码，并将各个数据两边空格去掉
        $username = $info['username'];
        $password = $info['password'];
        // 判断用户名是否存在
        $data = Db::table('user')->where('username', $username)->select();
        if($data == null){
            $result = array('msg'=>'username is not exist');
            return json_encode($result);
        }
        $salt = $data[0]['salt'];
        $userId = $data[0]['userId'];
        // 判断密码是否正确
        if ( $data[0]['password'] == md5($password.$salt) ) {
            //检查是否被封号
            if($this::islock($userId)){
                $result = array('msg'=>'user is lock');
                return json_encode($result);
            }
            session('userinfo', $data[0]);
            $result = array('userId'=>$userId, 'username'=>$username);
        }else{
            $result = array('msg'=>'login fail');
        }

        return json_encode($result);
    }

    //用户帐号是否被锁
    public function isLock($userId)
    {
        $result = Db::table('blackList')->where('userId', $userId)->find();
        return $result != null;
    }

    // 退出帐号
    public function login_out(){
        session('userinfo', null);
        $this->redirect('index/index/index');
    }

    //修改密码
    public function modifyPassword()
    {
        $request = Request::instance();
        $info = $request->post();
        $password = trim($info['password']);
        $userId = $this::getUserId();
        if($userId == 0){
            return view('loginPage');
        }
        $salt = Db::table('user')
                ->where('$userId', $userId)
                ->value('salt');
        $crypt = md5(md5($password).$salt);
        Db::table('user')
            ->where('$userId', $userId)
            ->update(['password'=>$crypt]);
        return "ok";
    }


    //获取关注数最多的若干个新闻来源
    // public function recSource(){
    //     $arr = Db::table('guanzhu')
    //             ->field('source, count(source) as cnt')
    //             ->group('source')
    //             ->order('cnt desc')
    //             ->limit(9)
    //             ->select();                

    //     return json_encode($arr);
    // }
    public function recSource()
    {
        $arr = array(
            array('name'=>'中国新青年', 'pic'=>'face.gif'),
            array('name'=>'央视新闻', 'pic'=>'face2.png'),
            array('name'=>'广州日报', 'pic'=>'face3.png'),
            array('name'=>'新快报', 'pic'=>'face4.png'),
            array('name'=>'羊城晚报', 'pic'=>'face.gif'),
            array('name'=>'南方日报', 'pic'=>'face4.png'),
            array('name'=>'青年文摘', 'pic'=>'face.gif'),
            array('name'=>'意林', 'pic'=>'face.gif'),
            array('name'=>'读者', 'pic'=>'face.gif'),
            array('name'=>'青年文摘', 'pic'=>'face3.png'),
            array('name'=>'消息快报', 'pic'=>'face.gif')
        );

        return json_encode($arr);
    }

    //随机字符串
    private function createCode($length){
        $ch = '0123456789qwertyuiopasdfghjklzxcvbnm';
        $code = '';
        for($i = 0; $i < $length; $i++){
            $x = floor(rand() * 36);
            $code = $code . $ch[$x];
        }
        return $code;
    }
    //短信登录验证码
    public function msgValidCode(){        
        $msgCode = createCode(4);
        session('msgCode', $msgCode);
        $msg = '新闻推荐系统短信登录验证码['. $code . '],30分钟内有效';
        //发到手机
        //send($msg);
        return strval($code);        
    }

    //检查短信登录验证码
    public function checkMsgCode(){
        $code = ''; //post
        $msgCode = session('msgCode');
        if($msgCode == $code){
            return true;
        } else {
            return false;
        }
    }

}