<?php

use \Kandouwo\Api\KdwApi;

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function home()
	{
		return View::make('hello');
	}

  /**
  * 搜索页面
  */
  public function search() {
    return 'search';
  }
  
  /**
  * Api文档页面
  */
  public function docs() {
    $api_docs = array();
    $api_docs_db = ApiDoc::where('type', '=', 0)->orderBy('level_main', 'asc')->orderBy('level_sub', 'asc')->get();
    foreach ($api_docs_db as $key=>$value)
    {
      $api_doc = new \stdClass();
      $api_doc->id = $value->id;
      $api_doc->context = $value->title;
      $api_doc->items = array();
      $api_doc->items[] = array('type'=>'default', 'title'=>'HTTP', 'context'=>$value->http, 'id'=>'http');
      $api_doc->items[] = array('type'=>'default', 'title'=>'认证', 'context'=>$value->token, 'id'=>'token');
      $api_doc->items[] = array('type'=>'default', 'title'=>'URI', 'context'=>$value->uri, 'id'=>'uri');
      $api_doc->items[] = array('type'=>'detail', 'title'=>'参数', 'context'=>$value->param, 'id'=>'param');
      $api_doc->items[] = array('type'=>'detail', 'title'=>'返回', 'context'=>$value->return, 'id'=>'return');
      $api_doc->items[] = array('type'=>'detail', 'title'=>'示例', 'context'=>$value->example, 'id'=>'example');
      $api_doc->items[] = array('type'=>'detail', 'title'=>'错误码', 'context'=>$value->error_code, 'id'=>'error_code');
      $api_docs[] = $api_doc;
    }
    
    $admin = Auth::check();//$admin = KdwApi::check_token() == 0;
    return View::make('api_docs.index', compact('admin', 'api_docs'));
  }
  
  /**
  * About页面
  */
  public function about() {
    return 'about';
  }
}
