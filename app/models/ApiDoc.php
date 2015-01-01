<?php

/**
 * @author simonio
 * @copyright 2014
 */

class ApiDoc extends Eloquent
{
  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'api_doc';

  protected $fillable = array(
    'type',
    'level_main',
    'level_sub',
    'title',
    'http',
    'token',
    'uri',
    'param',
    'return',
    'example',
    'error_code');

  public $timestamps = false;
}

?>