<?php
?>

<!DOCTYPE HTML>

<html lang="en">

<head>
  <!-- Force latest IE rendering engine or ChromeFrame if installed -->
  <!--[if IE]>
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <![endif]-->
  <meta charset="utf-8">

  <title>
@section('title')
  看豆窝
@show
  </title>

  <meta name="description" content="Kandouwo App Files">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap styles -->
  <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
  <!-- Generic page styles -->
  <link rel="stylesheet" href="public/css/style.css">
  <link rel="stylesheet" href="public/css/font-awesome.min.css">
  <!-- blueimp Gallery styles -->
  <link rel="stylesheet" href="//blueimp.github.io/Gallery/css/blueimp-gallery.min.css">
  <!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
  <link rel="stylesheet" href="public/css/jquery.fileupload.css">
  <link rel="stylesheet" href="public/css/jquery.fileupload-ui.css">
  <!-- CSS adjustments for browsers with JavaScript disabled -->
  <noscript><link rel="stylesheet" href="public/css/jquery.fileupload-noscript.css"></noscript>
  <noscript><link rel="stylesheet" href="public/css/jquery.fileupload-ui-noscript.css"></noscript>
@yield('styles')
</head>

<body>

<div class="container">
@yield('content')
</div>

@yield('content_ex')
  
<!-- footer -->
<div id="footer" class="footer container">
</div>

@yield('script')
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
<script src="public/js/vendor/jquery.ui.widget.js"></script>
<!-- Bootstrap JS is not required, but included for the responsive demo navigation -->
<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<!-- The main application script -->
<script src="public/js/main.js"></script>
<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE 8 and IE 9 -->
<!--[if (gte IE 8)&(lt IE 10)]>
<script src="js/cors/jquery.xdr-transport.js"></script>
<![endif]-->
</body> 
</html>
