<?php

class UserSign extends Eloquent
{
  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'user_sign';

  protected $fillable = array(
    'uid',
    'sign_date',
    'sign_days',
    'max_sign_days_tmp',
    'max_sign_days');

  public $timestamps = false;
}
