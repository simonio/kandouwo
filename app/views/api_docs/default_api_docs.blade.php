@extends('layouts.default')
@section('title')
看豆窝-Api文档
@stop

@section('styles')
  <link rel="stylesheet" href="../public/css/api-docs-main.css">
  <link rel="stylesheet" href="../public/css/api-docs-docs.css">
  <script>
    var _hmt = _hmt || [];
  </script>
@stop

@section('script')
  <script src="../public/js/docs.min.js"></script>
@stop

@section('content_before')
  <a class="sr-only sr-only-focusable" href="#content" id="top">Skip to main content</a>
  <div class="kwd-container"></div>
@stop
    
@section('content')
  <div class="bs-docs-container">
    <div class="row">
      <div class="col-md-10" role="main">
        <div class="bs-docs-section">
          <h1 id="api" class="page-header">Api 接口说明</h1>
          
          <!-- 注册 -->
          <h2 id="api-register">注册</h2>
          <ul>
            <li>HTTP：POST</li>
            <li>认证：--</li>
            <li>URI：/api/register</li>
            <li>参数：</li>
            
            <div class="highlight" >
              <pre><code>email 邮箱
password 密码
uuid 设备id
username kindle人的用户名(可选，带此参数时，password参数即为kindle用户的密码，否则为新用户的密码)
kindleren kindle人账号申请注册（需同时提供参数：username，password）
kidnleren_confirm kindle人账号确认注册（需同时提供参数：username）</code></pre>
            </div>
            
            <li>返回：json</li>
            
            <div class="highlight" >
              <pre><code>成功申请kindle人账号注册：
{
    "data": {
        "login": 1
    }
}
成功注册：
{
    "data": {
        "uid": 34,
        "nickname": "路人123490",
        "kindle_dou": 240, // 确认kindle人账号注册时有用（kindle人的k豆值）
        "token": "6Bxuk0F3FOg9d6YUTIISIl5xPY0=-MS4w-z...",
        "expired": 1800
    }
}
失败:
{
    "error": {
        "msg": 'Email has been registered.',
        "code": 1800
    }
}</code></pre>
            </div>
            
            <li>示例：</li>
            
            <div class="highlight" >
              <pre><code>普通账号注册：/api/register?email=simonio@163.com&password=123456&uuid=hdjghur45hj
申请kindle人账号注册：/api/register?kindleren=true&username=kandouwo&password=kandouwo
确认kindle人账号注册：/api/register?kindleren_confirm=true&username=kandouwo&password=kandouwo&email=simonio1024@163.com&&uuid=123</code></pre>
            </div>
            
            <li>错误码：</li>
            
            <div class="highlight" >
              <pre><code>-1：申请kindle人账号注册的参数错误
-2：无效的kindle人用户名或密码
-3：确认kindle人账号注册的参数错误
-4：注册参数错误
-5：邮箱已经被注册</code></pre>
            </div>
          </ul>
          
          <!-- 登录-->
          <h2 id="api-login">登录</h2>
          <ul>
            <li>HTTP：POST</li>
            <li>认证：--</li>
            <li>URI：/api/login</li>
            <li>参数：</li>
            
            <div class="highlight" >
              <pre><code>account 邮箱/手机号
  password 密码
  uuid 设备id</code></pre>
            </div>
            
            <li>返回：json</li>
            
            <div class="highlight" >
              <pre><code>成功登录：
{
    "data": {
        "uid": 34,
        "nickname": "路人123490",
        "sex": 男,
        "signature": "",
        "thumbnail": "",
        "thumbnail_big": "",
        "attend_date": "",
        "lastlogin_place": "",
        "readed_book_num": "",
        "download_book_num": "",
        "comment_num": "",
        "kindleren": "true", // kindle人账号用户为true，否则为false
        "kdou": 240, // 看豆窝的k豆
        "kindle_dou": 0, // kindle人的k豆
        "token": "6Bxuk0F3FOg9d6YUTIISIl5xPY0=-MS4w-z...",
        "expired": 1800
    }
}
失败
{
    "error": {
        "msg": 'Invalid email or password.',
        "code": 1800
    }
}</code></pre>
            </div>
            
            <li>示例：</li>
            
            <div class="highlight" >
              <pre><code>/api/login?email=simonio@163.com&password=123456&uuid=hdjghur45hj</code></pre>
            </div>
            
            <li>错误码：</li>
            
            <div class="highlight" >
              <pre><code>-1：参数错误
 0：已经登录
-2：用户名或密码错误
-3：邮箱格式错误</code></pre>
            </div>
          </ul>
          
          @yield('docs')
        </div>
      </div>
      
      <!-- 右侧导航栏 -->
      <div class="col-md-2">
        <div class="bs-docs-sidebar hidden-print hidden-xs hidden-sm" role="complementary">
          <hr>
          <span>Api接口说明</span>
          <ul class="nav bs-docs-sidenav">
            <li><a href="#api-register">注册</a></li>
            <li><a href="#api-login">登录</a></li>
            @yield('docs-nav')
            <!--
            <li>
              <a href="#api">Api接口说明</a>
              <ul class="nav">
                <li><a href="#api-register">注册</a></li>
                <li><a href="#api-login">登录</a></li>
              </ul>
            </li>
            -->
          </ul>
          <a class="back-to-top" href="#top">
            返回顶部
          </a>
          
        </div>
      </div>
    </div>
  </div>
@stop
    

<!-- Analytics
================================================== 
    <script type="text/javascript">
    var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
    document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3Fbdb993b828cbe079a7fbc1a951f44726' type='text/javascript'%3E%3C/script%3E"));
    </script>
-->
