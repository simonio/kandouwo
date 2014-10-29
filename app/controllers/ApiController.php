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

use Illuminate\Http\JsonResponse;

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
			
			$config = Config::get('database.connections')['mysql'];
			
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
	 * 注册.
	 *
	 * @return response(json)
	 */
	public function register() {
	
		// 必须的参数：email, password, uuid
		// 可附带参数：mail, nickname, sex, ...
		if (!Input::has('email') || !Input::has('password') || !Input::has('uuid')) {
			return Response::json(array('error' => array('msg'=>'','code'=>-1)));
		}
		
		
		// 判断是否已存在该用户
		// 若未存在，则创建新用户，并返回uid、token等相关信息
		// 否则返回错误信息
		if (User::where('email', '=', Input::get('email'))->first()) {
			return Response::json(array('error' => array('msg'=>'email已经被注册','code'=>-2)));
		}
		
		$kindle = null;
		if (Input::has('username')) {
			$kindle = $this->getKindleInfo(Input::get('username'), Input::get('password'));
			if ($kindle == null) {
				return Response::json(array('error' => array('msg'=>'','code'=>-3)));
			}
		}
		
		$data = Input::only('email','password','uuid');
		$data['password'] = Hash::make($data['password']);
		$dataEx = $this->filldata($data, Input::all());
		$user = null;
		$errorInfo = array();
		
		// 创建用户
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

		// 构造token
		$tokenstring = $this->composeToken(
			array('uid'=>$user->getkey(),'password'=>$data['password']),
			$user->getkey()
		);
		
		// 更新用户信息：用户名，k豆
		$updateData = array('nickname'=>($kindle!=null && isset($kindle['username']) ? $kindle['username'] :
			ApiController::$nickname_prefix.strval($user->getkey()+ApiController::$nickname_base)));
		if ($kindle != null && isset($kindle['credit'])) {
			$updateData['kdou'] = $kindle['credit'];
		}
		$user->update($updateData);
		
		return Response::json(
			array('data'=>
				array('uid'=>$user->getkey(),
					'nickname'=>$user->nickname,
					'kdou'=>isset($updateData['kdou']) ? $updateData['kdou'] : 0,
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
		if (!Input::has('email') || !Input::has('password') || !Input::has('uuid')) {
			$ret = array('error' => array('msg'=>'','code'=>-1));
			return Response::json($ret);
		}
		
		$email = Input::get('email');
		$password = Input::get('password');
		
		if (!Auth::validate(array('email' => $email, 'password' => $password))) {
			return Response::json(array('error' => array('msg'=>'','code'=>-2)));
		}

		$tokenstring = $this->composeToken(
			array('email'=>$email,'password'=>$password), $email
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
	
	public function test() {

		//echo \Kandouwo\Libraries\CurlHelp::get("www.baidu.com");
		
		return Response::json(json_decode(\Kandouwo\Libraries\CurlHelp::post(
			ApiController::$kindleRegisterUrl,
			array('username'=>'kandouwo','password'=>'kandouwo'))));
		
		//return new JsonResponse(json_decode(\Kandouwo\Libraries\CurlHelp::post(
		//	ApiController::$kindleRegisterUrl,
		//	array('username'=>'kandouwo','password'=>'kandouwo'))), 200, array(), 0);
	}
	
	protected function getKindleInfo($username, $password) {
		return json_decode(\Kandouwo\Libraries\CurlHelp::post(
			ApiController::$kindleRegisterUrl,
			array('username'=>$username,'password'=>$password)), true);
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