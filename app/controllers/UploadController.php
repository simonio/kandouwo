<?php

class UploadController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function android()
	{
		$count = 2;
		$files = array();
		for ($i=0; $i<$count; $i++) {
			$files[] = array('name'=>$i,'description'=>$i);
		}
		
		return View::make('upload.android', compact('count', 'files'));
	}
	
	public function ios()
	{
		$count = 4;
		$files = array();
		for ($i=0; $i<$count; $i++) {
			$files[] = array('name'=>$i,'description'=>$i);
		}
		
		return View::make('upload.ios', compact('count', 'files'));
	}
	

	public function handle()
	{
		//error_reporting(E_ALL | E_STRICT);
		require('UploadHandler.php');
    $upload_handler = new UploadHandler('/files/apps/');
	}
	
	public function delete()
	{
		if (Input::has('file'))
		{
			$result = unlink(__DIR__.'/../../files/apps/'.Input::get('file'));
			if ($result == true) {
				return Response::json(array('delete'=>Input::get('file')));
			}
			else {
				return Response::json(array('error'=>'delete file'));
			}
		}
		else {
		}
	}

}
