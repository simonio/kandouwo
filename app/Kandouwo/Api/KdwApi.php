<?php namespace Kandouwo\Api;

use Response;

/**
 * @author simonio
 * @copyright 2014
 */

class KdwApi {
  
  public static function success_response($data_array = null)
  {
    if ($data_array == null)
    {
      return Response::json(array('success' => 1));
    }
    return Response::json(array('success' => 1, 'data' => $data_array));
  }

  //public static function error_response($msg, $code)
  //{
  //  return Response::json(array('success' => 0, 'data' => array('msg' => $msg,
  //        'code' => $code)));
  //}
  
  public static function error_response($error_info)
  {
    return Response::json(array('success' => 0, 'data' => array('msg' => $error_info['desc'],
          'code' => $error_info['code'])));
  }
}

?>