<div role="navigation" class="navbar navbar-kandouwo navbar-static-top topnav-kandouwo">
  <div class="container">
  
    <div class="navbar-header">
      <a href="/" class="navbar-brand">{{lang('WebSiteName')}}</a>
    </div>
    
    <div id="top-navbar-collapse" class="navbar-collapse">
      <ul class="nav navbar-nav">
        <li class="{{ (Request::is('about*') ? ' active' : '') }}"><a href="{{ URL::route('home') }}">{{ lang('About') }}</a></li>
      </ul>

      <div class="navbar-right">
        @if (isset($search))
          {{ Form::open(array('route'=>'search', 'method'=>'get', 'class'=>'navbar-form navbar-left')) }}
            <div class="form-group">
            {{ Form::text('q', null, array('class' => 'form-control search-input mac-style', 'placeholder' => lang('Search'))) }}
            </div>
          {{ Form::close() }}
        @endif
        
        <ul class="nav navbar-nav kandouwo-login">
          @if (Auth::check())
            <li>
                <a href="{{ URL::route('home') }}" class="text-warning">
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
            <a href="{{ URL::route('login') }}" class="btn btn-primary" id="login-btn">
              <span>{{ lang('Login') }}</span>
            </a>
          @endif
        </ul>
      </div>
    </div>

  </div>
</div>
