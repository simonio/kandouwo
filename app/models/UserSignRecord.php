<?php

/**
 * @author simonio
 * @copyright 2014
 */

class UserSignRecord extends Eloquent
{
  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'user_sign_record';

  protected $fillable = array(
    'uid',
    'sign_date');
}

?>