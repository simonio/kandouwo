<?php

class AuthController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

  public function login()
  {
    return View::make('auth.login');
  }
  
  public function loginPost()
  {
    if (!Input::has('email') || !Input::has('password')) {
      return Redirect::to('login');
    }
    
    $email = Input::get('email');
    $password = Input::get('password');
    $remember = Input::get('remember', false);

    if (Auth::attempt(array('email' => $email, 'password' => $password))) {
      return Redirect::to('apps');
    }
    else {
      return Redirect::to('login')
        ->withInput(Input::except('password'))
        ->withErrors(array('error'=>'用户名或密码错误'));
    }
  }
  
  public function logout()
  {
    Auth::logout();
    return Redirect::route('android');
  }
}
