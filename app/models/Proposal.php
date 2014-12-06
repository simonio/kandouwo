<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class Proposal extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'proposal';
	
	protected $fillable = array('uid', 'timestamp', 'ip', 'phone_num', 'phone_model',
		'sys_version', 'app_version', 'context');
    
  public $timestamps = false;
}
