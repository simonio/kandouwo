@extends('layouts.default_min')

@section('styles')
  <link rel="stylesheet" href="../public/css/default.css">
  <link rel="stylesheet" href="../public/css/style.css">
@stop

@section('content')
  {{ Form::open(
    array(
      'route'=>'login-post', 
      'method'=>'post', 
      'class'=>'form-signin', 
      'id'=>'login-submit',
      'role'=>'form')
    )
  }}
    <span style="font-size: 72px; color: #444;">{{ lang('Kandouwo') }}</span>
    <p></p>
    
    <input type="email" name="email" class="form-control" value="{{ Input::old('email') }}" placeholder="{{lang('Email') }}（如：demo@kandouwo.com）" required autofocus>
    <br>
    <input type="password" name="password" class="form-control" value="{{ Input::old('password')}}" placeholder="{{lang('Password') }}" required>
    
    <div class="nav navbar-nav checkbox">
      <label>
        <input type="checkbox" name="remember" value="remember-me" {{ Input::old('remember')!=false ? 'checked="checked"':'' }}> {{lang('Remember me')}}
      </label>
    </div>
    
    <div class="navbar-right">
      <label class="navbar-right warning">{{ $errors->first('error') }}</label>
    </div>
    <button class="btn btn-lg btn-primary btn-block" type="submit">{{ lang('Login') }}</button>
  {{ Form::close() }}
@stop
