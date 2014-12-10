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
  "success": 1
}
成功注册：
{
  "success": 1,
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
  "success": 0,
  "data": {
    "msg": 'Email has been registered.',
    "code": -1001
  }
}</code></pre>
            </div>
            
            <li>示例：</li>
            
            <div class="highlight" >
              <pre><code>普通账号注册：/api/register?email=simonio@163.com&amp;password=123456&amp;uuid=hdjghur45hj
申请kindle人账号注册：/api/register?kindleren=true&amp;username=kandouwo&amp;password=kandouwo
确认kindle人账号注册：/api/register?kindleren_confirm=true&amp;username=kandouwo&amp;password=kandouwo&amp;email=simonio1024@163.com&amp;uuid=123</code></pre>
            </div>
            
            <li>错误码：</li>
            
            <div class="highlight" >
              <pre><code>-1001：申请kindle人账号注册的参数错误
-1002：确认kindle人账号注册的参数错误
-1003：无效的kindle人用户名或密码
-1004：注册参数错误
-1005：邮箱已经被注册</code></pre>
            </div>
          </ul>
          
          <!-- 登录 -->
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
              <pre><code>登录成功：
{
  "success": 1,
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
登录失败：
{
  "success": 0,
  "data": {
    "msg": 'Invalid email or password.',
    "code": -1
  }
}</code></pre>
            </div>
            
            <li>示例：</li>
            
            <div class="highlight" >
              <pre><code>/api/login?email=simonio@163.com&amp;password=123456&amp;uuid=hdjghur45hj</code></pre>
            </div>
            
            <li>错误码：</li>
            
            <div class="highlight" >
              <pre><code>-1001：参数错误
-1002：已经登录
-1003：用户名或密码错误
-1004：邮箱格式错误</code></pre>
            </div>
          </ul>
          
          <!-- 提交建议 -->
          <h2 id="api-proposal">提交建议</h2>
          <ul>
            <li>HTTP：POST</li>
            <li>认证：token</li>
            <li>URI：/api/proposal</li>
            <li>参数：</li>
            
            <div class="highlight" >
              <pre><code>uid 用户ID
ip ip地址
phone_num 手机号
phone_model 手机型号
sys_version 系统版本
app_version 应用版本
context 意见内容</code></pre>
            </div>
            
            <li>返回：json</li>
            
            <div class="highlight" >
              <pre><code>提交成功：
{
  "success": 1
}
提交失败：
{
  "success": 0,
  "data": {
    "msg": 'Invalid token.',
    "code": -1
  }
}</code></pre>
            </div>
            
            <li>示例：</li>
            
            <div class="highlight" >
              <pre><code>/api/proposal?token=sdhk4h54j...&amp;uid=344&amp;ip=102.222.123.10&amp;phone_num=18611111111&amp;phone_model=xiaomi&amp;sys_version=android4.1&amp;app_version=1.0&amp;context=我醉了</code></pre>
            </div>
            
            <li>错误码：</li>
            
            <div class="highlight" >
              <pre><code>-1001：参数错误
-1002：数据保存出错
-1003：提交过于频繁</code></pre>
            </div>
          </ul>
          
          
          <!-- 签到 -->
          <h2 id="api-sign_award">签到</h2>
          <ul>
            <li>HTTP：POST</li>
            <li>认证：token</li>
            <li>URI：/api/sign_award</li>
            <li>参数：</li>
            
            <div class="highlight" >
              <pre><code>uid 用户ID</code></pre>
            </div>
            
            <li>返回：json</li>
            
            <div class="highlight" >
              <pre><code>签到成功：
{
  "success": 1，
  "data": {
    “kdou_added": 3, // 增加的k豆
    "kdou": 29 // k豆总数
  }
}
签到失败：
{
  "success": 0,
  "data": {
    "msg": 'Invalid token.',
    "code": -1
  }
}</code></pre>
            </div>
            
            <li>示例：</li>
            
            <div class="highlight" >
              <pre><code>/api/sign_award?token=sdhk4h54j...&amp;uid=344</code></pre>
            </div>
            
            <li>错误码：</li>
            
            <div class="highlight" >
              <pre><code>-1001：参数错误
-2：令牌无效
-1002：uid不存在
-1003：签到日期异常
-1004：签到天数错误</code></pre>
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
            <li><a href="#api-proposal">提交建议</a></li>
            <li><a href="#api-sign_award">签到</a></li>
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
