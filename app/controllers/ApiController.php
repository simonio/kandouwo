<?php

use Illuminate\Http\JsonResponse;
use \Kandouwo\Api\KdwApi;
use \Kandouwo\Api\UserSigner;

class ApiController extends BaseController
{

  protected static $token_key = 'nrq!1d5t';
  protected static $nickname_prefix = '路人';
  protected static $nickname_base = 123456;
  protected static $kindleRegisterUrl = '';

  private static $error_info = array(
    'invalid_input_kindleren' => array('msg'=>'Invalid kindleren register input.','code'=>-1),
    'invalid_input_kindleren_confirm' => array('msg'=>'Invalid kindleren register confirm input.','code'=>-2),
    'invalid_kindleren' => array('msg'=>'Invalid kindleren account or password.','code'=>-3),
    'invalid_input_kandouwo' => array('msg'=>'Invalid kandouwo input.','code'=>-4),
    'email_registered' => array('msg'=>'Email has been registered.','code'=>-5),
    
    'invalid_login_input' => array('msg'=>'Invalid login input.','code'=>-1),
    'has_login' => array('msg'=>'Has login.','code'=>-2),
    'invalid_kdw' => array('msg'=>'Invalid email or password.','code'=>-3),
    'invalid_email' => array('msg'=>'Invalid email format.','code'=>-4),
    
    'invalid_proposal_input' => array('msg'=>'Invalid proposal input.','code'=>-1),
    'error_store_proposal' => array('msg'=>'Error in store proposal.','code'=>-2),
    
    'invalid_user_info_input' => array('msg'=>'Invalid user info input.','code'=>-1),
    'no_user' => array('msg'=>'No user of given uid.','code'=>-2));

  /**
   * 部署数据库
   * @return html
   *
   */
  public function install()
  {
    if (file_exists(__dir__ . '/../kandouwo/database/install.lock'))
    {

      echo ('<div>数据库已经部署完毕</div>');
    } else
    {
      echo '正在部署数据库...<br>';

      $config = Config::get('database.connections');
      $config = $config['mysql'];

      // new \Kandouwo\database\DB()
      $ret = App::make('\Kandouwo\Database\DB')->create($config['host'], $config['username'],
        $config['password'], $config['database']);
      if ($ret == true)
      {
        \Kandouwo\Database\DB::writeFile(__dir__ . '/../kandouwo/Database/install.lock',
          '');
        echo '<br>数据库部署结束。<br>';
      } else
      {
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
  public function register()
  {

    // 申请kindle人账号注册
    if (Input::has('kindleren'))
    {
      if (!$this->is_valid_input(array('username', 'password')))
      {
        return KdwApi::error_response(ApiController::$error_info['invalid_input_kindleren']);
      }

      $kindle = $this->get_kindle_info(Input::get('username'), Input::get('password'));
      if ($kindle == null)
      {
        return KdwApi::error_response(ApiController::$error_info['invalid_kindleren']);
      }
      return KdwApi::success_response();
    }

    // 确认kindle人账号注册
    $kindleren_confirm = Input::has('kindleren_confirm') ? true : false;
    $kindle = null;
    if ($kindleren_confirm == true)
    {
      if (!Input::has('username'))
      {
        return KdwApi::error_response(ApiController::$error_info['invalid_input_kindleren_confirm']);
      }

      $kindle = $this->get_kindle_info(Input::get('username'), Input::get('password'));
      if ($kindle == null)
      {
        return KdwApi::error_response(ApiController::$error_info['invalid_kindleren']);
      }
    }


    // 必须的参数：email, password, uuid
    // 可选的参数：usrname
    if (!$this->is_valid_input(array(
      'email',
      'password',
      'uuid')))
    {
      return KdwApi::error_response(ApiController::$error_info['invalid_input_kandouwo']);
    }

    // 判断是否已存在该用户
    // 若未存在，则创建新用户，并返回uid、token等相关信息
    // 否则返回错误信息
    if (User::where('email', '=', Input::get('email'))->first())
    {
      return KdwApi::error_response(ApiController::$error_info['email_registered']);
    }

    $data = Input::only('email', 'password', 'uuid');
    $data['password'] = Hash::make($data['password']);
    $user = null;
    $errorInfo = array();

    if ($kindleren_confirm == true)
    {
      $data['kindleren'] = true;
      $data['nickname'] = Input::get('username');
    }

    // 创建用户
    try
    {
      $user = User::create($data);
    }
    catch (Illuminate\Database\QueryException $e)
    {
      $errorInfo['msg'] = $e->errorInfo[0];
      $errorInfo['code'] = $e->errorInfo[1];
    }
    if (!$user)
    {
      return Response::json(array('error' => $errorInfo));
    }

    // 构造token
    $tokenstring = $this->compose_token(array('uid' => $user->getkey(), 'password' =>
        $data['password']), $user->getkey());

    return KdwApi::success_response(array(
      'uid' => $user->getkey(),
      'nickname' => $user->nickname,
      'kindle_dou' => ($kindle != null && isset($kindle['credit'])) ? $kindle['credit'] :
        0,
      'token' => $tokenstring,
      'expired' => KdwApi::$token_expired_time));
  }

  /**
   * 登录验证.
   *
   * @return response(json)
   */
  public function login()
  {

    // 输入：手机号|邮箱，密码，uuid
    // 根据账号的类型进行验证：
    //    纯数字的账号名验证为手机号的kandouwo登录
    //    符合邮箱命名规范的验证为普通kandouwo账号

    if (!$this->is_valid_input(array(
      'account',
      'password',
      'uuid')))
    {
      return KdwApi::error_response(ApiController::$error_info['invalid_login_input']);
    }

    $account = Input::get('account');
    $password = Input::get('password');
    $uuid = Input::get('uuid');

    $kindle = null;
    $user = null;

    // 判断是否已经登录
    if ($this->hasLogin($account, $password, $uuid))
    {
      return KdwApi::error_response(ApiController::$error_info['has_login']);
    }

    // 邮箱登录验证
    if (filter_var($account, FILTER_VALIDATE_EMAIL))
    {
      // 先验证普通kandouwo用户
      // 不成功则验证kindle人用户
      if (!Auth::validate(array('email' => $account, 'password' => $password)))
      {
        $user = User::where('email', '=', $account)->where('kindleren', '=', 'true')->
          first();
        if ($user !== null)
        {
          $kindle = $this->get_kindle_info($user->nickname, $user->password);
          
        }
        if ($kindle == null || $user == null)
        {
          return KdwApi::error_response(ApiController::$error_info['invalid_kdw']);
        }
      } else
      {
        $user = User::where('email', '=', $account)->first();
      }
    } else
    {
      return KdwApi::error_response(ApiController::$error_info['invalid_email']);
    }
    // 手机号登录验证
    //else if (preg_match("/^1[34578]\d{9}$/", $account)) {
    //  if (!Auth::validate(array('phone' => $account, 'password' => $password))) {
    //    return KdwApi::error_response('Invalid phone number or password.', -2);
    //  }
    //  $user = User::where('phone', '=', $account)->first();
    //}

    // 生成token
    $tokenstring = $this->compose_token(array(
      'account' => $account,
      'password' => $password,
      'uuid' => $uuid), $account);

    $user_info = $user->toArray();
    $user_info['token'] = $tokenstring;
    $user_info['expired'] = KdwApi::$token_expired_time;
    $user_info['kindle_dou'] = isset($kindle['credit']) ? $kindle['credit'] : 0;
    
    return KdwApi::success_response($user_info);
  }
  
  
  /**
   * 登出
   * 
   */
  public function logout()
  {
    
  }
  
  /**
   * 用户信息
   * 'type' = ['get','set']
   */
  public function user_info()
  {
    $type = Input::get('type');
    $uid = Input::get('uid');
    if ($type == null || ($type != 'get' && $type != 'set'))
    {
      return KdwApi::error_response(ApiController::$error_info['invalid_user_info_input']);
    }
    
    $user = User::find($uid);
    if ($user == null)
    {
      return KdwApi::error_response(ApiController::$error_info['no_user']);
    }
    
    if ($type == 'get')
    {
      return KdwApi::success_response($user->toArray());
    }
    elseif ($type == 'set')
    {
      $success = $user->update(array(Input::only('','','')));
      if ($success == true)
      {
        return KdwApi::success_response();
      } else
      {
        return KdwApi::error_response(ApiController::$error_info['save_error']);
      }
    }
  }

  /**
   * 反馈意见.
   * 参数：用户id，Ip，手机号，手机型号，系统版本，应用版本(必须)，反馈意见(必须)
   *
   */
  public function proposal()
  {
    if (!$this->is_valid_input(array(
      'app_version',
      'context')))
    {
      return KdwApi::error_response(ApiController::$error_info['invalid_proposal_input']);
    }

    $data = Input::only('uid', 'ip', 'phone_num', 'phone_model', 'sys_version',
      'app_version', 'context');

    $data['timestamp'] = date("Y-m-d H:i:s");

    $proposal = Proposal::create($data);
    if ($proposal != null)
    {
      return KdwApi::success_response();
    } else
    {
      return KdwApi::error_response(ApiController::$error_info['error_store_proposal']);
    }
  }

  /**
   * 用户签到
   * 
   * @return reponse(json)
   */
  public function sign_award()
  {
    $uid = Input::get('uid');
    $user_signer = new \Kandouwo\Api\UserSigner($uid);
    return $user_signer->sign();
  }

  public function sign_info()
  {
    $uid = Input::get('uid');
    $user_signer = new \Kandouwo\Api\UserSigner($uid);
    return $user_signer->sign_info(Input::get('days'));
  }
  
  public function edit_doc()
  {
    $id = Input::get('_id');
    $data = array(
      'title'=>Stripslashes(Input::get('_title')),
      'http'=>Stripslashes(Input::get('_http')),
      'token'=>Stripslashes(Input::get('_token')),
      'uri'=>Stripslashes(Input::get('_uri')),
      'param'=>Stripslashes(Input::get('_param')),
      'return'=>Stripslashes(Input::get('_return')),
      'example'=>Stripslashes(Input::get('_example')),
      'error_code'=>Stripslashes(Input::get('_error_code'))
    );
    
    Log::info('_return:'.Input::get('_return'));
      
    ApiDoc::find($id)->update($data);
    return KdwApi::success_response();
  }
  
  public function add_doc()
  {
    $data = array(
      'title'=>Stripslashes(Input::get('_title')),
      'http'=>Stripslashes(Input::get('_http')),
      'token'=>Stripslashes(Input::get('_token')),
      'uri'=>Stripslashes(Input::get('_uri')),
      'param'=>Stripslashes(Input::get('_param')),
      'return'=>Stripslashes(Input::get('_return')),
      'example'=>Stripslashes(Input::get('_example')),
      'error_code'=>Stripslashes(Input::get('_error_code'))
    );
    
    Log::info('_return:'.Input::get('_return'));
    
    $doc = ApiDoc::create($data);
    return KdwApi::success_response();
  }
  
  public function token_test()
  {
    if (!Input::has('t') || !Input::has('o'))
    {
      $ret = array('error' => "lack of param: 't' or 'o'");
      return json_encode($ret);
    }

    $data = array_values(Input::except('t', 'o'));
    $token = new \Kandouwo\Token\TokenManagerBase();
    $tokenstring = $token->compose_token(null, '1.0', Input::get('t'), Input::get('o'),
      $data, 'sdfsg44424');
    return $tokenstring;
    //$tokenArray = $token->ParseToken($tokenstring, 'sdfsg44424');
    //return json_encode($tokenArray);
  }

  public function test()
  {
    return strtotime("2004-04-04 02:02:13 GMT");
    
    //echo \Kandouwo\Libraries\CurlHelp::get("www.baidu.com");
    return KdwApi::error_response(KdwApi::$error_info['invalid_token']);
    
    return Response::json(json_decode(\Kandouwo\Libraries\CurlHelp::post(ApiController::
      $kindleRegisterUrl, array('username' => 'kandouwo', 'password' => 'kandouwo'))));

    //return new JsonResponse(json_decode(\Kandouwo\Libraries\CurlHelp::post(
    //	ApiController::$kindleRegisterUrl,
    //	array('username'=>'kandouwo','password'=>'kandouwo'))), 200, array(), 0);
  }

  public function test_login()
  {
    return $this->login();  
  }
  
  /**
   * 获取kindle人的登录信息
   *
   * @return json|null
   */
  protected function get_kindle_info($username, $password)
  {
    return json_decode(\Kandouwo\Libraries\CurlHelp::post(ApiController::$kindleRegisterUrl,
      array('username' => $username, 'password' => $password)), true);
  }

  /**
   * 判断用户是否已经登录
   *
   * @return boolean
   */
  protected function hasLogin($account, $password, $uuid)
  {
    return false;
  }

  /**
   * 判断输入的参数是否正确
   * @param param_array 参数名数组：array('uid','password',...)
   * @return boolean
   */
  protected function is_valid_input($param_array)
  {
    foreach ($param_array as $value)
    {
      if (!Input::has($value) || Input::get($value) === '')
      {
        return false;
      }
    }
    return true;
  }

  /**
   * 创建token，并保存session
   * @param data token包含的数据(array)
   * @param uid 用户id(int)
   * @param session 是否保存到session(boolean)
   */
  protected function compose_token($data, $uid, $session = true)
  {
    $dataArray = array_values($data);
    $token = new \Kandouwo\Token\TokenManagerBase();
    $time = time();
    $tokenstring = $token->compose_token(null, '1.0', $time, '12d5j!df', $dataArray,
      ApiController::$token_key);
    if ($session == true)
    {
      Session::put(strval($uid) . '_token', $tokenstring);
      Session::put(strval($uid) . '_time', $time);
    }
    return $tokenstring;
  }

  /**
   *
   * 填充users表的字段信息
   *
   */
  protected function filldata($data, $input)
  {
    //$data['lastlogin_datetime'] = time();
    if (isset($input['nickname']))
      $data['nickname'] = $input['nickname'];
    if (isset($input['sex']))
      $data['sex'] = $input['sex'];
    if (isset($input['signature']))
      $data['signature'] = $unput['signature'];
    if (isset($input['login_place']))
      $data['lastlogin_place'] = $input['login_place'];
    return $data;
  }

  /**
   *
   * 判断token是否有效
   *
   */
  public function check_token($response=true)
  {
    $ret = KdwApi::check_token();
    if ($ret == -1)
    {
      return KdwApi::error_response(KdwApi::$error_info['invalid_token_input']);
    } elseif ($ret == -2)
    {
      return KdwApi::error_response(KdwApi::$error_info['invalid_token']);
    }
  }
}
