<?php namespace Kandouwo\Api;

use Response;
use Session;
use Input;

/**
 * @author simonio
 * @copyright 2014
 */

class KdwApi {
  public static $token_expired_time = 1800;
  public static $error_info = array(
    'invalid_token' => array('msg'=>'Invalid token.','code'=>-10000),
    'invalid_token_input' => array('msg'=>'Invalid token input.','code'=>-10001),
    'invalid_input' => array('msg'=>'Invalid input','code'=>10003));
  
  public static function success_response($data_array = null)
  {
    if ($data_array == null)
    {
      return Response::json(array('data' => array('code'=>0)));
    }
    $data_array['code'] = 0;
    return Response::json(array('data' => $data_array));
  }
  
  public static function error_response($error_info)
  {
    return Response::json(array('data' => $error_info));
  }
  
  public static function response($success, $data_array)
  {
    if ($success == true)
    {
      return KdwApi::success_response($data_array);
    } else
    {
      return KdwApi::error_response($data_array);
    }
  }
  
  public static function check_token()
  {
    $uid = Input::get('uid');
    $token = Input::get('token');
    if ($uid == null || $token == null)
    {
      return -1;
    }
    
    if (!((Session::has($uid . '_token') && Session::has($uid . '_time') && Session::
      get($uid . '_token') === $token && (time() - Session::get($uid . '_time')) <
      KdwApi::$token_expired_time)))
      {
        return -2;
      }
    return 0;
  }
}

?>