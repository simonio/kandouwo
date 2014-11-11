<?php

class UploadController extends \BaseController
{
  
  function generateQRfromGoogle($chl, $widhtHeight = '100', $EC_level = 'L', $margin = '0')
  {
    $chl = urlencode($chl);
    return '<img class="qr-code" src="http://chart.apis.google.com/chart?chs=' . $widhtHeight . 'x' . $widhtHeight . '&cht=qr&chld=' . $EC_level . '|' . $margin . '&chl=' . $chl . '" alt="" widhtHeight="' . $widhtHeight . ' " widhtHeight="' . $widhtHeight . '"/>';
  }
  
  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function android()
  {
    require('UploadHandler.php');
    
    $qr_code    = true;
    $background = '';
    $upload_handler = new UploadHandler('/files/apps/', null, false);
    $files = $upload_handler->get(false);
    $file_param_name = $upload_handler->get_file_param_name();
    
    if ($files != null && count($files[$file_param_name]) != 0) {
      $background = $this->generateQRfromGoogle($files[$file_param_name][0]->url);
    }
    else {
      $qr_code = false;
    }
    return View::make('upload.android', compact('qr_code', 'background'));
  }
  
  public function ios()
  {
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
    if (Input::has('file')) {
      $result = unlink(__DIR__ . '/../../files/apps/' . Input::get('file'));
      if ($result == true) {
        return Response::json(array(
          'delete' => Input::get('file')
        ));
      } else {
        return Response::json(array(
          'error' => 'delete file'
        ));
      }
    } else {
    }
  }
  
}