<?php

use Illuminate\Http\JsonResponse;

/*
  
*/

class ApiController extends BaseController {

	protected static $token_key = 'nrq!1d5t';
	protected static $nickname_prefix = '路人';
	protected static $nickname_base = 123456;
	protected static $token_expired_time = 1800;
	protected static $kindleRegisterUrl = '';

	/**
	* 部署数据库
	*
	*/
	public function install() {
		if(file_exists(__DIR__.'/../kandouwo/database/install.lock')) {
			
			echo('<div>数据库已经部署完毕</div>');
		} else {
			echo '正在部署数据库...<br>';
			
      $config = Config::get('database.connections');
			$config = $config['mysql'];
			
			// new \Kandouwo\database\DB()
			$ret = App::make('\Kandouwo\Database\DB')->create($config['host'], $config['username'], $config['password'], $config['database']);
			if ($ret == true) {
				\Kandouwo\Database\DB::writeFile(__DIR__.'/../kandouwo/Database/install.lock', '');
				echo '<br>数据库部署结束。<br>';
			} else {
				echo '<br>数据库部署失败。<br>';
			}
		}
	}
	
	/**
	 * 注册看豆窝账号.
	 *
	 * 备注：
	 *		kindle人用户注册时，kandouwo不保存其密码和credit数目，仅保存其昵称和Email
	 *		普通用户注册时，保存其密码
	 *
	 *		注册后kdou值均初始为0
	 *
	 * @return response(json)
	 */
	public function register() {
	
    // 申请kindle人账号注册
    if (Input::has('kindleren')) {
      if (!$this->isValidInput(array('username','password'))) {
        return $this->errorResponse('Invalid inputs.', -1);
      }
      
      $kindle = $this->getKindleInfo(Input::get('username'), Input::get('password'));
			if ($kindle == null) {
				return $this->errorResponse('Invalid kindleren account or password.', -2);
			}
      
      return Response::json(array('data' => array('login'=>1)));
    }
    
    // 确认kindle人账号注册
    $kindleren_confirm = Input::has('kindleren_confirm') ? true : false;
    $kindle = null;
    if ($kindleren_confirm == true) {
      if (!Input::has('username')) {
        return $this->errorResponse('Invalid kindleren confirm info.', -3);
      }
      
      $kindle = $this->getKindleInfo(Input::get('username'), Input::get('password'));
			if ($kindle == null) {
				return $this->errorResponse('Invalid kindleren account or password.', -2);
			}
    }

    
		// 必须的参数：email, password, uuid
    // 可选的参数：usrname
		if (!$this->isValidInput(array('email','password','uuid'))) {
			return $this->errorResponse('Invalid inputs.', -4);
		}
		
		// 判断是否已存在该用户
		// 若未存在，则创建新用户，并返回uid、token等相关信息
		// 否则返回错误信息
		if (User::where('email', '=', Input::get('email'))->first()) {
      return $this->errorResponse('Email has been registered.', -5);
		}
		
		$data = Input::only('email','password','uuid');
		$data['password'] = Hash::make($data['password']);
		$user = null;
		$errorInfo = array();
    
    if ($kindleren_confirm == true) {
      $data['kindleren'] = true;
      $data['nickname'] = Input::get('username');
    }
		
		// 创建用户
		try {
			$user = User::create($data);
		}
		catch (Illuminate\Database\QueryException $e) {
			$errorInfo['msg'] = $e->errorInfo[0];
			$errorInfo['code'] = $e->errorInfo[1];
		}
		if (!$user) {
			return Response::json(array('error' => $errorInfo));
		}

		// 构造token
		$tokenstring = $this->composeToken(
			array('uid'=>$user->getkey(),'password'=>$data['password']),
			$user->getkey()
		);
		
		return Response::json(
			array('data'=>
				array('uid'=>$user->getkey(),
					'nickname'=>$user->nickname,
          'kindle_dou'=>($kindle != null && isset($kindle['credit'])) ? $kindle['credit'] : 0,
					'token'=>$tokenstring,
					'expired'=>ApiController::$token_expired_time)
			)
		);
	}
	
	/**
	 * 登录验证.
	 *
	 * @return response(json)
	 */
	public function login() {
  
    // 输入：手机号|邮箱，密码，uuid
    // 根据账号的类型进行验证：
    //    纯数字的账号名验证为手机号的kandouwo登录
    //    符合邮箱命名规范的验证为普通kandouwo账号
    
    if (!$this->isValidInput(array('account','password','uuid'))) {
      return $this->errorResponse('Invalid inputs.', -1);
		}
		
		$account = Input::get('account');
		$password = Input::get('password');
    $uuid = Input::get('uuid');
    
    $kindle = null;
    $user = null;
    
    // 判断是否已经登录
    if ($this->hasLogin($account, $password, $uuid)) {
      return $this->errorResponse('Has login.', 0);
    }
    
    // 邮箱登录验证
    if (filter_var($account, FILTER_VALIDATE_EMAIL)) { 
      // 先验证普通kandouwo用户
      // 不成功则验证kindle人用户
      if (!Auth::validate(array('email' => $account, 'password' => $password))) {
        $user = User::where('email', '=', $account)->where('kindleren','=','true')->first();
        $kindle = $this->getKindleInfo($user->nickname, $user->password);
        if ($kindle == null) {
          return $this->errorResponse('Invalid email or password.', -2);
        }
      }
      else {
        $user = User::where('email', '=', $account)->first();
      }
    }
    else {
      return $this->errorResponse('Invalid Email format.', -3);
    }
    // 手机号登录验证
    //else if (preg_match("/^1[34578]\d{9}$/", $account)) { 
    //  if (!Auth::validate(array('phone' => $account, 'password' => $password))) {
    //    return $this->errorResponse('Invalid phone number or password.', -2);
    //  }
    //  $user = User::where('phone', '=', $account)->first();
    //}

    // 生成token
		$tokenstring = $this->composeToken(
			array('account'=>$account,'password'=>$password,'uuid'=>$uuid), $account
		);
		
		return Response::json(array(
      'data' => 
        array(
          'token'=>$tokenstring,
          'expired'=>ApiController::$token_expired_time,
          'uid'=>$user->id,
          'nickname'=>$user->nickname,
          'sex'=>$user->sex,
          'signature'=>$user->signature,
          'kdou'=>$user->kdou,
          'kindle_dou'=>isset($kindle['credit']) ? $kindle['credit'] : 0,
          'thumbnail'=>$user->thumbnail,
          'thumbnail_big'=>$user->thumbnail_big,
          'attend_date'=>$user->attend_date,
          'lastlogin_place'=>$user->lastlogin_place,
          'readed_book_num'=>$user->readed_book_num,
          'download_book_num'=>$user->download_book_num,
          'comment_num'=>$user->comment_num,
          'kindleren'=>$user->kindleren
        )
      )
    );
	}

	public function token_test() {
		if (!Input::has('t') || !Input::has('o')) {
			$ret = array('error'=>"lack of param: 't' or 'o'");
			return json_encode($ret);
		}
		
		$data = array_values(Input::except('t','o'));
		$token = new \Kandouwo\Token\TokenManagerBase();
		$tokenstring = $token->ComposeToken(null, '1.0', 
			Input::get('t'), 
			Input::get('o'), 
			$data, 
			'sdfsg44424'
		);
		return $tokenstring;
		//$tokenArray = $token->ParseToken($tokenstring, 'sdfsg44424');
		//return json_encode($tokenArray);
	}
	
	public function test() {

		//echo \Kandouwo\Libraries\CurlHelp::get("www.baidu.com");
		
		return Response::json(json_decode(\Kandouwo\Libraries\CurlHelp::post(
			ApiController::$kindleRegisterUrl,
			array('username'=>'kandouwo','password'=>'kandouwo'))));
		
		//return new JsonResponse(json_decode(\Kandouwo\Libraries\CurlHelp::post(
		//	ApiController::$kindleRegisterUrl,
		//	array('username'=>'kandouwo','password'=>'kandouwo'))), 200, array(), 0);
	}
	
  
  /**
	* 获取kindle人的登录信息
  *
  * @return json|null
  */
  protected function getKindleInfo($username, $password) {
		return json_decode(\Kandouwo\Libraries\CurlHelp::post(
			ApiController::$kindleRegisterUrl,
			array('username'=>$username,'password'=>$password)), true);
	}
	
  /**
	* 判断用户是否已经登录
  *
  * @return boolean
  */
  protected function hasLogin($account,$password,$uuid) {
    return false;
  }
  
  protected function errorResponse($msg, $code) {
    return Response::json(array('error' => array('msg'=>$msg,'code'=>$code)));
  }
  
  protected function isValidInput($param_array) {
    foreach ($param_array as $value) {
      if (!Input::has($value) || Input::get($value) === '') {
        return false;
      }
    }
    return true;
  }
  
	/**
	*
	* 创建token，并保存session
	*
	*/
	protected function composeToken($data, $uid, $session=true) {
		$dataArray = array_values($data);
		$token = new \Kandouwo\Token\TokenManagerBase();
		$time = time();
		$tokenstring = $token->ComposeToken(null, '1.0', $time, '12d5j!df', $dataArray, ApiController::$token_key);
		if ($session == true) {
			Session::put(strval($uid).'_token', $tokenstring);
			Session::put(strval($uid).'_time', $time);
		}
		return $tokenstring;
	}
	
	/**
	*
	* 填充users表的字段信息
	*
	*/
	protected function filldata($data, $input) {
		//$data['lastlogin_datetime'] = time();
		if (isset($input['nickname'])) $data['nickname'] = $input['nickname'];
		if (isset($input['sex'])) $data['sex'] = $input['sex'];
		if (isset($input['signature'])) $data['signature'] = $unput['signature'];
		if (isset($input['login_place'])) $data['lastlogin_place'] = $input['login_place'];
		return $data;
	}
	
	/**
	*
	* 判断token是否有效
	*
	*/
	protected function isValidToken($uid, $token) {
		return (Session::has($uid.'_token') &&
			Session::has($uid.'_time') &&
			Session::get($uid.'_token') === $token &&
			(time() - Session::get($uid.'_time')) < ApiController::$token_expired_time);
	}
}