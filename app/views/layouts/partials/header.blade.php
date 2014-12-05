<!-- navbar navbar-default navbar-fixed-top -->
<header class="navbar navbar-static-top navbar-default kdw-nav" role="banner">
  <div class="container">
    <div class="navbar-header">
      <button class="navbar-toggle collapsed" type="button" data-toggle="collapse" data-target=".bs-navbar-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a href="{{ URL::route('home') }}" class="navbar-brand">{{ lang('Kandouwo') }}</a>
    </div>
    <nav class="collapse navbar-collapse bs-navbar-collapse" role="navigation">
      <ul class="nav navbar-nav">
        <li class="{{ (Request::is('apps*') ? ' active' : '') }}">
          <a href="{{ URL::route('android') }}">{{ lang('App') }}</a>
        </li>
        <li class="{{ (Request::is('docs*') ? ' active' : '') }}">
          <a href="{{ URL::route('api-docs') }}">{{ lang('Api Docs') }}</a>
        </li>
        <li class="{{ (Request::is('about*') ? ' active' : '') }}">
          <a href="{{ URL::route('about') }}">{{ lang('About') }}</a>
        </li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        @if (Auth::check())
          <li>
            <a class="text-warning" href="{{ URL::route('home') }}">
            </a>
          </li>
          <li>
            <a href="">
              {{{ $currentUser->email }}}
            </a>
          </li>
          <li>
            <a class="button" href="{{ URL::route('logout') }}" onclick=" return confirm('{{ lang('Are you sure want to logout?') }}')">
              {{ lang('Logout') }}
            </a>
          </li>
        @else
          <li>
            <a href="{{ URL::route('login') }}" id="login-btn">
              <span>{{ lang('Login') }}</span>
            </a>
          </li>
        @endif
        <!--
        <li><a href="" onclick="_hmt.push(['_trackEvent', 'docv3-navbar', 'click', 'doc-home-navbar-job'])" target="_blank">高薪工作</a></li>
        -->
      </ul>
    </nav>
  </div>
</header>