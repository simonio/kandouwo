<?php

/* 运行结果

备注：
	token的结构：签名-base64(版本号)-base64(encrypt(data))
	data中的字段用换行符分割，字段的顺序约定好就行，目前是（用户名，密码，ip，时间戳，once）

data:
string 'ak_meng@126.com
123456
192.168.1.1
1413640916
1234567890123456' (length=62)
构造 Token
string 'E5NB9yFj7bdS/RKi+Gt7V/FGjkw=-MS4w-Z1h04uhfqEYJDIc0vI8zx9UUfNTtku6YglMO3SgzXcSUTe9+H8RCwUYhwTSfgmxgggrfmBncNDab4WQaA4oWFQ==' (length=122)
解析 Token
array (size=3)
  'sign' => string 'E5NB9yFj7bdS/RKi+Gt7V/FGjkw=' (length=28)
  'version' => string '1.0' (length=3)
  'data' => string 'ak_meng@126.com
123456
192.168.1.1
1413640916
1234567890123456' (length=62)
*/
 
class ApiController extends BaseController {

	protected $key = 'nrq!1d5t';
	protected static $nickname_prefix = '路人';
	protected static $expired_time = 1800;

	/**
	* 部署数据库
	*
	*/
	public function install() {
		if(file_exists(__DIR__.'/../kandouwo/database/install.lock')) {
			echo('<div>数据库已经部署完毕</div>');
		} else {
			echo '正在部署数据库...<br>';
			// new \Kandouwo\database\DB()
			$ret = App::make('\Kandouwo\database\DB')->create('127.0.0.1', 'root', 'jTC2xjnrqFVUd532', 'kandouwo');
			if ($ret == true) {
				\Kandouwo\Database\DB::writeFile(__DIR__.'/../kandouwo/database/install.lock', '');
				echo '<br>数据库部署结束。<br>';
			} else {
				echo '<br>数据库部署失败。<br>';
			}
		}
	}
	
	/**
	 * 注册.
	 *
	 * @return response(json)
	 */
	public function register() {
	
		// 必须的参数：email, password, uuid
		// 可附带参数：mail, nickname, sex, ...
		if (!Input::has('email') || !Input::has('password') || !Input::has('uuid')) {
			$ret = array('error' => array('msg'=>'','code'=>-1));
			return Response::json($ret);
		}
		
		
		// 判断是否已存在该用户
		// 若未存在，则创建新用户，并返回uid、token等相关信息
		// 否则返回错误信息
		$model = User::where('email', '=', Input::get('email'))->first();
		if ($model) {
			return Response::json(array('error' => array('msg'=>'email已经被注册','code'=>-2)));
		}
		
		
		$data = Input::only('email','password','uuid');
		$data['password'] = Hash::make($data['password']);
		$dataEx = $this->filldata($data, Input::all());
		$user = null;
		$errorInfo = array();
		
		try {
			$user = User::create($dataEx);
		}
		catch (Illuminate\Database\QueryException $e) {
			$errorInfo['msg'] = $e->errorInfo[0];
			$errorInfo['code'] = $e->errorInfo[1];
		}
		
		if (!$user) {
			return Response::json(array('error' => $errorInfo));
		}

		$tokenstring = $this->composeToken(
			array('uid'=>$user->getkey(),'password'=>$data['password']),
			$user->getkey()
		);
		$user->update(array('nickname'=>ApiController::$nickname_prefix.strval($user->getkey()+123456)));
		
		return Response::json(
			array('data'=>
				array('uid'=>$user->getkey(),
					'nickname'=>$user->nickname,
					'token'=>$tokenstring,
					'expired'=>ApiController::$expired_time)
			)
		);
	}
	
	/**
	 * 登录验证.
	 *
	 * @return response(json)
	 */
	public function login() {
		if (!Input::has('uid') || !Input::has('password') || !Input::has('uuid')) {
			$ret = array('error' => array('msg'=>'','code'=>-1));
			return Response::json($ret);
		}
		
		$uid = Input::get('uid');
		$password = Input::get('password');
		
		if (!Auth::validate(array('id' => $uid, 'password' => $password))) {
			return Response::json(array('error' => array('msg'=>'','code'=>-2)));
		}

		$tokenstring = $this->composeToken(
			array('uid'=>$uid,'password'=>$password), $uid
		);
		
		return Response::json(array('data' => 
			array('token'=>$tokenstring,
				'expired'=>ApiController::$expired_time)));
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
	
	/**
	*
	* 创建token，并保存session
	*
	*/
	protected function composeToken($data, $uid, $session=true) {
		$dataArray = array_values($data);
		$token = new \Kandouwo\Token\TokenManagerBase();
		$time = time();
		$tokenstring = $token->ComposeToken(null, '1.0', $time, '12d5j!df', $dataArray, $this->key);
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
	
	protected function isValidToken($uid, $token) {
		return (Session::has($uid.'_token') &&
			Session::has($uid.'_time') &&
			Session::get($uid.'_token') === $token &&
			(time() - Session::get($uid.'_time')) < ApiController::$expired_time);
	}
}