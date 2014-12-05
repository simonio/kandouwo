<?php

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
    $api_doc = new \stdClass();
    $api_doc->id = 'docs-demo1';
    $api_doc->context = 'context';
    $api_doc->items = array(
      array('type'=>'default','context'=>'1'),
      array('type'=>'default','context'=>'2'),
      array('type'=>'detail','context'=>'3')
    );
    
    $api_docs[0] = $api_doc;
    return View::make('api_docs.index');//, compact('api_docs')
  }
  
  /**
  * About页面
  */
  public function about() {
    return 'about';
  }
}
