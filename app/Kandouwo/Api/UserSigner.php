<?php namespace Kandouwo\Api;

use UserSign;
use User;

/**
 * 签到
 * period_day_count 签到周期，如果不需要，可以设为0或者负数
 * level 连续签到奖励 连续签到的天数对应的奖励数目
 * 
 * @author simonio
 * @copyright 2014
 */

class UserSigner
{
  private static $error_info = array(
    'invalid_sign_award_input' => array('desc'=>'Invalid sign award input.','code'=>-1),
    'no_user' => array('desc'=>'No user of given uid.','code'=>-2),
    'error_date' => array('desc'=>'Invalid sign date.','code'=>-3),
    'error_days' => array('desc'=>'Invalid sign days.','code'=>-4));
    
  private static $sign_info = array(
    'max_sign_days' => 30, 
    'level' => array(
      0 => 1,
      3 => 2,
      5 => 3,
      9 => 5,
      15 => 8,
      25 => 15));

  private $uid = 0;

  public function __construct($uid)
  {
    $this->uid = $uid;
  }

  /**
   * 签到
   */
  public function sign()
  {
    if ($this->uid == null)
    {
      return KdwApi::error_response(ApiController::$error_info['invalid_sign_award_input']);
    }
    
    $user_sign = UserSign::where('uid', '=', $this->uid)->first();
    $user = User::where('id', '=', $this->uid)->first();
    if ($user == null)
    {
      return KdwApi::error_response(UserSigner::$error_info['no_user']);
    }
    
    $today = new \DateTime();
    $today->setTime(0, 0, 0);
    
    if ($user_sign == null) // 首次签到
    {
      $user_sign = UserSign::create(array(
        'uid' => $this->uid,
        'sign_date' => $today->format('Y-m-d H:i:s'),
        'sign_days' => 1,
        'max_sign_days' => 1,
        'max_sign_days_tmp' => 1));
      return $this->sign_award($user, 1, 1);
    } else
    {
      $sign_date = new \DateTime($user_sign->sign_date);
      $sign_date->setTime(0, 0, 0);

      $diff_days = $today->diff($sign_date);
      $sign_days = $user_sign->sign_days + 1;
      $max_sign_days = $user_sign->max_sign_days;
      $max_sign_days_tmp = $user_sign->max_sign_days_tmp;
        
      if ($diff_days->d == 1) // 连续签到
      {
        if (UserSigner::$sign_info['max_sign_days'] > 0 && $sign_days > UserSigner::$sign_info['max_sign_days'])
        {
          $sign_days = 1;
        }
        if ($sign_days > $max_sign_days_tmp) $max_sign_days_tmp = $sign_days;
        if ($max_sign_days_tmp > $max_sign_days) $max_sign_days = $max_sign_days_tmp;

        $user_sign->update(array(
          'sign_date' => $today->format('Y-m-d H:i:s'),
          'sign_days' => $sign_days,
          'max_sign_days' => $max_sign_days,
          'max_sign_days_tmp' => $max_sign_days_tmp));
        return $this->sign_award($user, $user_sign->sign_days, $max_sign_days);
      } else
        if ($diff_days->d <= 0) // 异常签到
        {
          return KdwApi::error_response(UserSigner::$error_info['error_date']);
        } else // 连续签到被打断
        {
          $user_sign->update(array('sign_date' => $today->format('Y-m-d H:i:s'), 'sign_days' =>
              1, 'max_sign_days_tmp' => 1));
          return $this->sign_award($user, 1, $max_sign_days);
        }
    }
  }

  /**
   * 查询签到信息
   */
  public function sign_info()
  {
    $user_sign = UserSign::where('uid', '=', $this->uid)->first();
    if ($user_sign == null)
    {
      return KdwApi::error_response(UserSigner::$error_info['no_user']);
    } else 
    {
      return KdwApi::success_response(array(
        'sign_date' => $user_sign->sign_date,
        'sign_days' => $user_sign->sign_days,
        'max_sign_days' => $user_sign->max_sign_days));
    }
  }
  
  /**
   * 派发签到奖励，并返回结果（json）
   */
  private function sign_award($user, $days, $max_sign_days)
  {
    if ($days <= 0)
    {
      return KdwApi::error_response(UserSigner::$error_info['error_days']);
    }

    // 获取奖励数目
    $level = $this->get_sign_level($days);
    $award_count = UserSigner::$sign_info['level'][$level];

    // 派发奖励
    $user->kdou = $user->kdou + $award_count;
    $user->save();
    
    return KdwApi::success_response(array(
      'kdou_added' => $award_count,
      'kdou' => ($user) ? $user->kdou : 0,
      'sign_days' => $days,
      'max_sign_days' => $max_sign_days));
  }

  /**
   * 获取签到的奖励等级
   */
  private function get_sign_level($days)
  {
    $level = 0;
    foreach (UserSigner::$sign_info['level'] as $key => $value)
    {
      if ($days >= $key)
      {
        $level = $key;
      } else
      {
        break;
      }
    }
    return $level;
  }
}
