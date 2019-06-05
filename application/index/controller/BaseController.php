<?php
namespace app\index\controller;
use \think\Controller;
use \think\Db;

class BaseController extends Controller
{
    public function initialize()
    {
    	if(empty(session('userinfo'))) {
    		$this->redirect('/user/login');
    	}
    }
}