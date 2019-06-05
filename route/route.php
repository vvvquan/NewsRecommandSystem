<?php

//////////////////////////////////////////////////////////////
//普通用户
///////////////////////////////////////////////////////////////
//登录页面
Route::get('user/login', 'index/user/login');
//普通验证
Route::post('login_in', 'index/user/login_in');
//短信验证
Route::post('login_in_phone', 'index/user/login_in_phone');
//邮箱验证
Route::post('login_in_email', 'index/user/login_in_email');

//注册页面
Route::get('user/reg', 'index/user/reg');
Route::post('register', 'index/user/register');
//退出帐号
Route::get('login_out', 'index/user/login_out');

//获取推荐的新闻来源
Route::get('recSource', 'index/user/recSource');  //未实现来源和图像对应

//新闻推荐首页
Route::get(':sign/data/:num', 'index/index/data');

//新闻首页右侧的用户信息
Route::get('userinfo', 'index/index/userinfo');

//新闻正文页
Route::get('detail/:newsId', 'index/index/detail');
Route::get('newsData/:id', 'index/index/newsData');
//推荐新闻
Route::get('recommand/:newsId', 'index/article/recommand');

//删除评论
Route::get('del1/:comment2Id', 'index/comment/del1');
//删除回复
Route::get('del2/:comment2Id', 'index/comment/del2');

//阅读量加1
Route::get('read/:newsId', 'index/article/addRead');



//加载评论
Route::get('comment/:newsId/p/:page', 'index/comment/all');
//加载回复
Route::get('comment2/:commentId', 'index/comment/second');
//做出评论
Route::post('comment', 'index/comment/write1');
//做出回复
Route::post('comment2', 'index/comment/write2');

//收藏新闻
Route::get('addCollect/:newsId', 'index/article/addCollect');
//点赞新闻
Route::get('addGood/:newsId', 'index/article/addGood');
//关注新闻
Route::post('guanzhu', 'index/article/guanzhu');


//阅读数据统计
Route::get('stat/data', 'index/stat/data');
Route::get('stat/basicData', 'index/stat/basicData');

//阅读记录页面
Route::get('me/history', 'index/me/history');
//历史记录列表
Route::get('history/:userId/p/:pageNum/pNum/:pageListNum', 'index/me/historyData');

//用户个人信息页面
Route::get('manage/info', 'index/user/info');
Route::get('manage/bind', 'index/user/bind');
Route::get('manage/publish', 'index/user/publish');
Route::get('manage/safe', 'index/user/safe');


//////////////////////////////////////////////////////////////
//管理员
///////////////////////////////////////////////////////////////
//登录页面
Route::get('admin/login', 'admin/admin/login');
Route::post('admin/login_in', 'admin/admin/login_in');
//退出帐号
Route::get('admin/login_out', 'admin/admin/login_out');

//=======================
// 新闻管理
//=======================
//编辑类型
Route::get('admin/edit', 'admin/news/edit');
//获取所有的新闻类型
Route::get('admin/getAllNewsType', 'admin/news/getAllNewsType');
//添加新闻类型
Route::post('admin/addNewsType', 'admin/news/addNewsType');


//新闻列表
Route::get('admin/newsList', 'admin/news/newsList');
//新闻总数
Route::get('admin/getNewsTotal', 'admin/news/getNewsTotal');
//获取某页的新闻标题
Route::get('admin/getNewsTitle/:pageNum/:pageListNum', 'admin/news/getNewsTitle');
//保存新闻类型
Route::post('admin/saveNewsType/:newsId/:typeId', 'admin/news/saveNewsType');


//评论审核页
Route::get('admin/comment', 'admin/news/comment');
//评论和回复的列表
Route::get('admin/comments/:pageNum/:pageListNum', 'admin/news/commentList');
//保存评论审核结果
Route::post('admin/comment1', 'admin/news/chkComment1');
//保存回复审核结果
Route::post('admin/comment2', 'admin/news/chkComment2');

//系统公告页
Route::get('admin/sys', 'admin/sys/sysNotice');
//获取公告内容
Route::get('sys/notice', 'admin/sys/notice');
//保存公告
Route::post('admin/sysMsg/save', 'admin/sys/saveNotice');

//=======================
// 用户管理
//=======================
//用户列表
Route::get('admin/userList', 'admin/user/userList');
//用户权限页
Route::get('admin/userAccess', 'admin/user/access');
//用户权限列表
Route::get('admin/userAccessList', 'admin/user/accessList');
//模糊相关用户
Route::post('admin/search/users', 'admin/user/search');


//=======================
// 管理员管理
//=======================
//管理员列表
Route::get('admin/index', 'admin/admin/admin');
Route::get('admin/adminList', 'admin/admin/adminList');
//权限设置
//管理员权限页面
Route::get('admin/adminAccess', 'admin/admin/access');
//管理员角色列表
Route::get('admin/roles/:roleId', 'admin/admin/roleList');
//获取管理员角色拥有的权限的列表
Route::get('admin/adminAccessList/:roleId', 'admin/admin/accessList');
//保存权限
Route::post('admin/adminAccess/saveAccess', 'admin/admin/saveAccess');

//保存管理员权限
Route::post('admin/adminAccess', 'admin/admin/adminAccess');
//保存管理员角色
Route::get('admin/changeRole/:adminId/:roleId', 'admin/admin/changeRole');

//无权限页面
Route::get('admin/noAccess', 'admin/admin/noAccess');

return [

];